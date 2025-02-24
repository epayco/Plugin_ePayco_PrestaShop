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
require_once EP_ROOT_URL . '/vendor/autoload.php';

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
        $this->epayco  = new Epayco\Epayco(array(
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
        $cart = $this->context->cart;
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
        if(!isset($_COOKIE[$cookie_cart_name]) || $epaycoCartData ) {
            $this->module->validateOrder($cart->id, CreditCard_OrderState::getInitialState(), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
            $cookie_value = $this->module->currentOrder;
            setcookie($cookie_cart_name, $cookie_value, time() + (60 * 14), "/");
            $order_id = $this->module->currentOrder;
        }else{
            $order_id = $_COOKIE[$cookie_cart_name]??$epaycoCartDataArray['epaycoCart'] ?? null;
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

}