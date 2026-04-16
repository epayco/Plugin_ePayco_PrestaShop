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
require_once EP_ROOT_URL . '/includes/module/preference/AbstractPreference.php';

class CreditcardPreference extends AbstractPreference
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @pararm $cart
     * @param $creditcard_info
     * @retrun bool|string
     * @throw Exception
     */
    public function createPreference($cart, $creditcard_info)
    {
        $logFile = dirname(__DIR__, 3) . '/logs/creditcard.log';
        //file_put_contents($logFile, date('Y-m-d H:i:s') . " [DEBUG] cardTokenId recibido: " . ($creditcard_info['cardTokenId'] ?? 'VACIO') . " | customer_id: " . ($creditcard_info['customer_id'] ?? 'N/A') . PHP_EOL, FILE_APPEND);
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        $addressdelivery = new Address((int)($cart->id_address_delivery));
        $paymentData = $this->createSessionPayment();
        $orderData = array_merge($paymentData, $creditcard_info);
        // If this is a retry on an order that was previously left in a
        // rejected/cancelled/error state, reset it to the "Esperando Pago"
        // (PAYCO_ORDERSTATE_WAITING) state so PrestaShop doesn't abort the new
        // charge with "idShop is corrupted" or similar guard checks.
        $this->resetRejectedOrderStateForRetry(isset($orderData['id_order']) ? (int)$orderData['id_order'] : 0, $logFile);
        if ($orderData) {
            $value = floatval($orderData['total_paid']);
            $valorBaseDevolucion = floatval($orderData['total_paid_tax_excl']);
            $iva = $value - $valorBaseDevolucion;
            $iso = 'CO';
            $extra1 = $orderData['id_cart'];
            $extra2 = $orderData['id_order'];
            $currency = new Currency($orderData['id_currency']);
            $lang = $this->context->language->language_code;
            $addressdelivery = new Address((int)($cart->id_address_delivery));
            $descripcion = '';
            $productos = Db::getInstance()->executeS('
                    SELECT id_product FROM `' . _DB_PREFIX_ . 'cart_product`
                    WHERE `id_cart` = ' . (int) $extra1);
            $p_url_response =  Context::getContext()->link->getModuleLink('payco', 'response');
            $p_url_confirmation =  Context::getContext()->link->getModuleLink('payco', 'confirmation');
            foreach ($productos as $producto) {
                // Your product id
                $id_product = (int)$producto['id_product'];
                // Language id
                $lang_id = (int) Configuration::get('PS_LANG_DEFAULT');
                // Load product object
                $product = new Product($id_product, false, $lang_id);
                // Validate product object
                if (Validate::isLoadedObject($product)) {
                    // Get product name
                    $descripcion = $descripcion . $product->name . ', ';
                }
            }
            $myIp = $this->getCustomerIp();
            $descripcion = substr($descripcion, 0, -2);
            $test = (bool)Configuration::get('EPAYCO_PROD_STATUS');
            if ($iva == 0) $valorBaseDevolucion = 0;

            $response = array(
                "token_card" => $orderData["cardTokenId"],
                "customer_id" => 'customer_id',
                'bill' => $orderData['reference'],
                "dues" => $orderData["installmet"],
                //'id_order' => $this->module->currentOrder,
                'value' => strval($value),
                'tax_base' => strval($valorBaseDevolucion),
                'tax' => strval($iva),
                'url_response' => $p_url_response,
                'url_confirmation' => $p_url_confirmation,
                'email' => $orderData['email'],
                'name' => $orderData['name'],
                'last_name' => $orderData['name'],
                'address' => strval($orderData['address']),
                'city' => $addressdelivery->city,
                'country' => $addressdelivery->country === "Colombia" ? "CO":"US",
                "cell_phone" => $orderData['cellphone'],
                "doc_type" => $orderData['documentType'],
                "doc_number" =>  $orderData['document'],
                'extra1' => strval($extra1),
                'extra2' => strval($extra2),
                'lang' => $lang,
                'description' => $descripcion,
                'currency' => $currency->iso_code,
                'ip' =>$myIp,
                "use_default_card_customer" => true,
                "testMode" => $test,
                "extras_epayco"=>["extra5"=>"P19"],
                'metodoconfirmacion'=> "POST",
                "method_confirmation" => "POST",
                'extras'=> [
                    'extra1' => strval($extra1),
                    'extra2' => strval($extra2)
                ]
            );
            try {
                $charge = $this->epayco->charge->create($response);
            } catch (\Exception $e) {
                file_put_contents($logFile, date('Y-m-d H:i:s') . " [ERROR] Exception en charge->create: " . $e->getMessage() . " | Code: " . $e->getCode() . PHP_EOL, FILE_APPEND);
                $this->jsonErrorResponse($this->friendlyErrorMessage($e->getMessage() ?: 'Error procesando el pago'), [
                    'reason' => 'exception',
                    'exception_code' => $e->getCode(),
                ]);
                return;
            }

            $response = json_decode(json_encode($charge), true);
            //file_put_contents($logFile, date('Y-m-d H:i:s') . " [DEBUG] Respuesta charge->create: " . json_encode($response) . PHP_EOL, FILE_APPEND);
            if (is_array($response) && !empty($response['success'])) {
                $ref_payco = $response['data']['refPayco']??$response['data']['ref_payco'];
                $estado = $response['data']['estado']??$response['data']['status']??'desconocido';
                //file_put_contents($logFile, date('Y-m-d H:i:s') . " [INFO] Transaccion procesada | ref_payco: " . $ref_payco . " | estado: " . $estado . " | valor: " . ($response['data']['value']??$response['data']['valor']??'N/A') . " | franquicia: " . ($response['data']['franchise']??$response['data']['franquicia']??'N/A') . " | autorizacion: " . ($response['data']['autorizacion']??'N/A') . " | respuesta: " . ($response['data']['response']??$response['data']['respuesta']??'N/A') . PHP_EOL, FILE_APPEND);
                $estadoLower = strtolower($estado);
                $estadosValidos = ["aceptada", "acepted", "accepted", "pendiente", "pending"];
                if (!in_array($estadoLower, $estadosValidos)) {
                    $respuestaMsg = $response['data']['respuesta']
                        ?? $response['data']['response']
                        ?? ($response['data']['cc_network_response']['message'] ?? null)
                        ?? 'Transaccion rechazada';
                    $codError = $response['data']['cod_error']
                        ?? ($response['data']['cc_network_response']['code'] ?? null);
                    file_put_contents($logFile, date('Y-m-d H:i:s') . " [WARNING] Transaccion NO aprobada | ref_payco: " . $ref_payco . " | estado: " . $estado . " | mensaje: " . $respuestaMsg . PHP_EOL, FILE_APPEND);
                    $this->jsonErrorResponse($this->friendlyErrorMessage($respuestaMsg), [
                        'reason'    => 'rejected',
                        'estado'    => $estado,
                        'ref_payco' => $ref_payco,
                        'cod_error' => $codError,
                        'respuesta' => $respuestaMsg,
                    ]);
                    return;
                }else{
                    $order = new Order($extra2);
                    $descuento = $order->total_discounts_tax_incl;
                    $params = http_build_query([
                        'refPayco' =>  $ref_payco,
                        'autorizacion' => $response['data']['autorizacion']??$response['data']['autorizacion']??null,
                        'descuento' => $descuento,
                        'factura' => $response['data']['invoice']??$response['data']['factura']??null,
                        'descripcion' => $descripcion,
                        'valor' => $response['data']['value']??$response['data']['valor']??null,
                        'ip' => $response['data']['ip']??null,
                        'estado' => $response['data']['status']??$response['data']['estado']??null,
                        'fecha' => $response['data']['paymentDate']??$response['data']['fecha']??null,
                        'franquicia' => $response['data']['franchise']??$response['data']['franquicia']??null,
                        'respuesta' => $response['data']['response']??$response['data']['respuesta']??null
                    ]);

                    // URL completa
                    $url =  Context::getContext()->link->getModuleLink('payco', 'download_receipt') . '?' . $params;
                    $epaycoOrder = [
                        'success' => true,
                        'status' => $response['data']['status']??$response['data']['estado']??null,
                        'type' => 'type',
                        'refPayco'  => $ref_payco,
                        'factura'  => $response['data']['invoice']??$response['data']['factura']??null,
                        'descripcion'  => $response['data']['description']??$response['data']['descripcion']??null,
                        'valor' => $response['data']['value']??$response['data']['valor']??null,
                        'iva' => $response['data']['tax']??$response['data']['iva']??null,
                        'ip' => $response['data']['ip'],
                        'estado' => $response['data']['status']??$response['data']['estado']??null,
                        'respuesta' => $response['data']['response']??$response['data']['respuesta']??null,
                        'fecha' => $response['data']['paymentDate']??$response['data']['fecha']??null,
                        'descuento' => $descuento,
                        'autorizacion' => $response['data']['autorizacion']??$response['data']['autorizacion']??null,
                        'franquicia' => $response['data']['franchise']??$response['data']['franquicia']??null,
                        'extra1' => $response['data']['extras']["extra1"]??null,
                        'baseurl' => $url
                    ];
                    $this->context->cookie->__set('redirect_epayco_message', json_encode($epaycoOrder));
                    $this->context->cookie->write();

                    $uri = __PS_BASE_URI__ . 'index.php?controller=order-confirmation';
                    $uri .= '&id_cart=' . $order->id_cart;
                    $uri .= '&key=' . $order->secure_key;
                    $uri .= '&id_order=' . $order->id;
                    $uri .= '&id_module=' . $this->module->id;
                    $uri .= '&ref_payco=' . $ref_payco;
                    if (in_array(strtolower($response['data']['estado']),["aceptada","acepted"])) {
                        $orderHistory = new OrderHistory();
                        $orderHistory->id_order = (int)$order->id;
                        $default_order_state = Configuration::get('EPAYCO_STANDARD_STATE_END_TRANSACTION');
                        $orderHistory->changeIdOrderState((int)$default_order_state, (int)$order->id);
                        $orderHistory->add();
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " [SUCCESS] Pago ACEPTADO | ref_payco: " . $ref_payco . " | order_id: " . $order->id . " | reference: " . $order->reference . " | valor: " . ($response['data']['value']??$response['data']['valor']??'N/A') . " | franquicia: " . ($response['data']['franchise']??$response['data']['franquicia']??'N/A') . " | autorizacion: " . ($response['data']['autorizacion']??'N/A') . " | ip: " . ($response['data']['ip']??'N/A') . PHP_EOL, FILE_APPEND);
                    } else {
                        file_put_contents($logFile, date('Y-m-d H:i:s') . " [INFO] Pago con estado pendiente | ref_payco: " . $ref_payco . " | order_id: " . $order->id . " | estado: " . ($response['data']['estado']??'N/A') . PHP_EOL, FILE_APPEND);
                    }
                    //redirect to order confirmation page
                    Tools::redirect($uri);
                }
            }else{
                file_put_contents($logFile, date('Y-m-d H:i:s') . " [ERROR] Respuesta NO exitosa de ePayco. Response completa: " . json_encode($response) . PHP_EOL, FILE_APPEND);
                $mensaje = (is_array($response) ? ($response['message'] ?? null) : null)
                    ?? (is_array($response) ? ($response['data']['description'] ?? null) : null)
                    ?? 'Error procesando la transaccion';
                $detalle = is_array($response) ? ($response['data']['errors'] ?? null) : null;
                if (!empty($detalle) && is_string($detalle)) {
                    $mensaje .= ' - ' . $detalle;
                }
                $this->jsonErrorResponse($this->friendlyErrorMessage($mensaje), [
                    'reason' => 'api_error',
                ]);
                return;
            }
        }else{
            file_put_contents($logFile, date('Y-m-d H:i:s') . " [ERROR] orderData vacio o falso" . PHP_EOL, FILE_APPEND);
            $this->jsonErrorResponse('No pudimos leer los datos de tu orden. Por favor recarga la pagina e intenta de nuevo.', [
                'reason' => 'missing_order_data',
            ]);
            return;
        }
    }


    /**
     * Convierte el mensaje tecnico de ePayco en un texto amigable para el cliente.
     *
     * @param string $raw
     * @return string
     */
    private function friendlyErrorMessage($raw)
    {
        $raw = trim((string)$raw);
        if ($raw === '') {
            return 'No pudimos procesar tu pago. Por favor intenta nuevamente.';
        }
        $lower = mb_strtolower($raw);
        $map = [
            'fondos insuficientes'        => 'Fondos insuficientes en la tarjeta. Intenta con otro medio de pago.',
            'tarjeta bloqueada'           => 'Tu tarjeta se encuentra bloqueada. Comunicate con tu banco o usa otra tarjeta.',
            'tarjeta vencida'             => 'Tu tarjeta se encuentra vencida. Utiliza otra tarjeta.',
            'excede el limite'            => 'La transaccion excede el limite autorizado por tu banco.',
            'denegada'                    => 'Tu banco rechazo la transaccion. Comunicate con tu banco o usa otra tarjeta.',
            'rechazada'                   => 'La transaccion fue rechazada. Verifica los datos de tu tarjeta o intenta con otra.',
            'cliente o token inexistente' => 'No pudimos validar los datos de la tarjeta. Por favor verificalos e intenta nuevamente.',
            'operacion no permitida'      => 'Operacion no permitida por la entidad emisora. Intenta con otra tarjeta.',
            'transaccion invalida'        => 'Transaccion invalida. Verifica los datos de tu tarjeta e intenta nuevamente.',
        ];
        foreach ($map as $needle => $friendly) {
            if (mb_strpos($lower, $needle) !== false) {
                return $friendly;
            }
        }
        return $raw;
    }
}