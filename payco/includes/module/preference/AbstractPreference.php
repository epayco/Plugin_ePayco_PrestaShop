<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2024 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
//require_once EP_ROOT_URL . '/vendor/autoload.php';
use Epayco as EpaycoSdk;
abstract class AbstractPreference
{
    public $module;
    public $epayco;
    public $public_key;
    public $private_key;
    public $context;

    public function __construct()
    {
        $this->module = Module::getInstanceByName('payco');
        $this->public_key = Configuration::get('EPAYCO_PUBLIC_KEY');
        $this->private_key = Configuration::get('EPAYCO_PRIVATE_KEY');
        $test = (bool)Configuration::get('EPAYCO_PROD_STATUS');
        $lang = $this->module->_context->language->iso_code == 'es' ? 'es' : "en";
        $this->epayco  = new EpaycoSdk\Epayco(array(
            "apiKey" => $this->public_key,
            "privateKey" => $this->private_key,
            "lenguage" => $lang,
            "test" => !$test
        ));
    }

    /**
     * Verify if module is avaible
     *
     * @retrun void
     */
    public function verifyModuleParameters($context)
    {
        $this->context = $context;
        $cart = $context->cart;
        $authorized = false;

        if ($cart->id_customer == 0 ||
            $cart->id_address_delivery == 0 ||
            $cart->id_address_invoice == 0 ||
            !$this->module->active
        ) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'payco') {
                $authorized = true;
                break;
            }
        }
        if (!$authorized) {
            die($this->module->l('This payment method is not available.'));
        }
    }

    /**
     * @retrun bool|string
     * @throw Exception
     */
    public function createSessionPayment()
    {
        $logFile = dirname(__DIR__, 3) . '/logs/creditcard.log';
        $cart = $this->context->cart;

        // Retry recovery: on a retry attempt, PrestaShop may have already
        // consumed the cart during the previous validateOrder() call, leaving
        // $this->context->cart without a valid id. Recover it by loading the
        // previous order (tracked via the `epayco_retry_order` cookie) and
        // pulling its id_cart back into the context.
        if (!$cart || !$cart->id) {
            $retryOrderId = isset($_COOKIE['epayco_retry_order']) ? (int)$_COOKIE['epayco_retry_order'] : 0;
            if ($retryOrderId > 0) {
                $previousOrder = new Order($retryOrderId);
                if (Validate::isLoadedObject($previousOrder) && $previousOrder->id_cart) {
                    $recoveredCart = new Cart((int)$previousOrder->id_cart);
                    if (Validate::isLoadedObject($recoveredCart)) {
                        $cart = $recoveredCart;
                        $this->context->cart = $recoveredCart;
                        file_put_contents(
                            $logFile,
                            date('Y-m-d H:i:s') . " [INFO] Cart recovered for retry | order_id={$retryOrderId} cart_id={$cart->id}" . PHP_EOL,
                            FILE_APPEND
                        );
                    }
                }
            }
        }

        if (!$cart || !$cart->id) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " [ERROR] createSessionPayment: cart unavailable and no retry cookie" . PHP_EOL, FILE_APPEND);
            Tools::redirect('index.php?controller=order&step=1');
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $mailVars = array(
            '{epayco_id}' => Configuration::get('merchantid'),
            '{epayco_detail}' => nl2br(Configuration::get('merchantpassword'))
        );
        $epaycoCartData = $this->context->cookie->__get('epaycoCart');
        $epaycoCartDataArray = json_decode($epaycoCartData, true);
        $cookie_cart_name =  trim($cart->id)."_cart";

        // Reuse the existing order id on retries. The previous logic called
        // validateOrder() again whenever either the `${id_cart}_cart` cookie was
        // missing OR the `epaycoCart` cookie was truthy; the second branch could
        // re-trigger validateOrder → Mail::send on retries, causing PrestaShop's
        // "idShop is corrupted" fatal error. We now only create a new order when
        // no prior cookie exists.
        $existingOrderId = null;
        if (isset($_COOKIE[$cookie_cart_name]) && (int)$_COOKIE[$cookie_cart_name] > 0) {
            $existingOrderId = (int)$_COOKIE[$cookie_cart_name];
        } elseif (is_array($epaycoCartDataArray) && !empty($epaycoCartDataArray['epaycoCart'])) {
            $existingOrderId = (int)$epaycoCartDataArray['epaycoCart'];
        }

        // Also fall back to the global `epayco_retry_order` cookie (set below)
        // which is keyed by order_id rather than cart_id. This is the only
        // cookie that survives when PrestaShop resets the cart after a rejected
        // validateOrder() call during a previous attempt.
        if (!$existingOrderId && isset($_COOKIE['epayco_retry_order']) && (int)$_COOKIE['epayco_retry_order'] > 0) {
            $existingOrderId = (int)$_COOKIE['epayco_retry_order'];
        }

        if ($existingOrderId && (int)$existingOrderId > 0) {
            $order_id = $existingOrderId;
        } else {
            $this->module->validateOrder($cart->id, CreditCard_OrderState::getInitialState(), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
            $cookie_value = $this->module->currentOrder;
            setcookie($cookie_cart_name, $cookie_value, time() + (60 * 14), "/");
            // Also set a global retry cookie keyed by nothing so recovery works
            // when $this->context->cart is wiped after validateOrder().
            setcookie('epayco_retry_order', $cookie_value, time() + (60 * 14), "/");
            $order_id = $this->module->currentOrder;
        }
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . (int)$order_id;
        return Db::getInstance()->getRow($sql);
    }


    public function getCustomerIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }


    /**
     * Get response with preference
     *
     * @param StandardPreference $preference
     * @param integer $code
     */
    public function getResponse($preference, $code)
    {
        header('Content-type: application/json');
        $response = array(
            'code' => $code,
            'preference' => $preference,
        );

        echo json_encode($response);
        http_response_code($code);
        exit();
    }

        /**
     * Si la orden actual quedo en un estado rechazado / cancelado / error tras un
     * intento previo, la reseteamos al estado "ePayco Esperando Pago" para que
     * PrestaShop no bloquee el reintento con errores del tipo "idShop is corrupted"
     * (que aparece cuando se intenta procesar una orden cuyo cart ya fue marcado
     * como finalizado en un estado de fallo).
     *
     * @param int    $idOrder
     * @param string $logFile
     */
    protected function resetRejectedOrderStateForRetry($idOrder, $logFile)
    {
        if ($idOrder <= 0) {
            return;
        }
        try {
            $order = new Order($idOrder);
            if (!Validate::isLoadedObject($order)) {
                return;
            }
            $currentState = (int)$order->current_state;
            if ($currentState <= 0) {
                return;
            }

            // Estados que consideramos "rechazados" y que deben resetearse antes de reintentar.
            $rejectedStates = array_filter(array_map('intval', [
                Configuration::get('PS_OS_ERROR'),
                Configuration::get('PS_OS_CANCELED'),
                Configuration::get('EPAYCO_STATE_REJECTED'),
                Configuration::get('EPAYCO_STATE_FAILED'),
                Configuration::get('EPAYCO_STATE_CANCELED'),
            ]));

            if (!in_array($currentState, $rejectedStates, true)) {
                return;
            }

            $pendingState = (int)Configuration::get('PAYCO_ORDERSTATE_WAITING');
            if ($pendingState <= 0) {
                $pendingState = (int)Configuration::get('PS_OS_PAYMENT');
            }
            if ($pendingState <= 0) {
                return;
            }

            $history = new OrderHistory();
            $history->id_order = (int)$order->id;
            $history->changeIdOrderState($pendingState, (int)$order->id);
            $history->add();

            file_put_contents(
                $logFile,
                date('Y-m-d H:i:s') . " [INFO] Orden {$order->id} estaba en estado {$currentState} (rechazado). Reseteado a {$pendingState} para permitir reintento." . PHP_EOL,
                FILE_APPEND
            );
        } catch (\Exception $e) {
            file_put_contents(
                $logFile,
                date('Y-m-d H:i:s') . " [WARNING] No se pudo resetear el estado de la orden {$idOrder} para reintento: " . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );
        }
    }

    /**
     * Devuelve una respuesta JSON con la informacion del error o del rechazo
     * para que el frontend muestre un mensaje amigable sin redirigir al usuario.
     * De esta manera el cliente puede reintentar el pago sobre la misma orden.
     *
     * @param string $message
     * @param array  $extra
     */
    protected function jsonErrorResponse($message, array $extra = [])
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(200);
        }
        $payload = array_merge([
            'success'       => false,
            'error_message' => $message,
        ], $extra);
        echo json_encode($payload);
        exit;
    }

}