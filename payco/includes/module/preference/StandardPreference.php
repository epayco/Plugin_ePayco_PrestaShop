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

class StandardPreference extends AbstractPreference
{
    public $standard_info;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @retrun bool|string
     * @throw Exception
     */
    public function createPreference()
    {
        $cart = $this->context->cart;
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        $orderData = $this->createSessionPayment();
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
            $test = 'true';
            if(Configuration::get('EPAYCO_PROD_STATUS') == '1'){
                $test = 'false';
            }
            $external = 'true';
            if(Configuration::get('EPAYCO_STANDARD_MODAL') == '1'){
                $external = 'false';
            }
            if ($iva == 0) $valorBaseDevolucion = 0;
            $response = array(
                'public_key' => trim($this->public_key),
                'private_key' => trim($this->private_key),
                'invoice' => $orderData['reference'],
                'name' => $orderData['reference'],
                //'id_order' => $this->module->currentOrder,
                'amount' => strval($value),
                'tax_base' => strval($valorBaseDevolucion),
                'tax' => strval($iva),
                'taxIco' => '0',
                'response' => $p_url_response,
                'confirmation' => $p_url_confirmation,
                'email_billing' => $this->context->customer->email,
                'name_billing' => $this->context->customer->firstname . " " . $this->context->customer->lastname,
                'address_billing' => $addressdelivery->address1 . " " . $addressdelivery->address2,
                'city_billing' => $addressdelivery->city,
                'country' => $addressdelivery->id_state,
                'extra1' => strval($extra1),
                'extra2' => strval($extra2),
                'lang' => $lang,
                'description' => $descripcion,
                'external' => $external,
                'currency' => $currency->iso_code,
                'ip' =>$myIp,
                'autoclick'=> 'true',
                'test' => $test,
                'method_confirmation'=> "POST"
            );
            $epaycoData = $this->context->cookie->__get('epaycoToken');
            $epaycoDataArray = json_decode($epaycoData, true);
            $cookie_name =  trim($this->public_key)."_apify";
            // if(!isset($_COOKIE[$cookie_name]) || !is_array($epaycoDataArray) ) {
            if(!isset($_COOKIE[$cookie_name]) ) {
                $dataAuth =$this->authentication(trim($this->public_key),trim($this->private_key));
                if(!$dataAuth){
                    throw new ErrorException("Creating bearer_token error.", 106);
                }
                $cookie_value = $dataAuth;
                setcookie($cookie_name, $cookie_value, time() + (60 * 14), "/");
                $bearer_token = $dataAuth;
            }else{
                //$bearer_token = $_COOKIE[$cookie_name]??$epaycoDataArray['epaycoToken'] ?? null;
                $bearer_token = $_COOKIE[$cookie_name] ?? null;
            }
            if(is_null($bearer_token)){
                $this->getResponse([], 400);
                Tools::redirect('index.php?controller=order&step=3&typeReturn=failure');
            }else{
                $this->context->cookie->__set('epaycoToken', json_encode(['epaycoToken' => $bearer_token]));
                $this->context->cookie->write();
                $header = "Bearer ".$bearer_token;
                $session = $this->formatPayload($response, $header);
                $session_data = array('session' => $session, 'external' => $external);
                $this->getResponse($session_data, 200);
                Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
            }
        }else{
            $this->getResponse([], 400);
            Tools::redirect('index.php?controller=order&step=3&typeReturn=failure');
        }
    }

    private function apiService($url, $data, $type, $cabecera = null)
    {
        $header = [
            "cache-control: no-cache",
            "accept: application/json",
            "content-type: application/json",
        ];

        try {
            if ($cabecera) {
                if (is_array($cabecera)) {
                    $header = array_merge($header, $cabecera);
                } else {
                    $header[] = $cabecera;
                }
            }

            $jsonData = json_encode($data);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSLKEYPASSWD => '',
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 600,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $type,
                CURLOPT_POSTFIELDS => $jsonData,
                CURLOPT_HTTPHEADER => $header
            ));
            $resp = curl_exec($curl);
            if ($resp === false) {
                return array('curl_error' => curl_error($curl), 'curerrno' => curl_errno($curl));
            }
            curl_close($curl);
            return json_decode($resp);
        } catch (Exception $exception) {
            return [
                "success" => false,
                "titleResponse" => "error",
                "textResponse" => $exception->getMessage(),
                "data" => []
            ];
        }
    }

    private function authentication($api_key, $private_key){
        try{
            $token = base64_encode($api_key.":".$private_key);
            $bearer = $this->apiService(
                'https://apify.epayco.io/login',
                [],
                'POST',
                "authorization: Basic ".$token
            );
            if(isset($bearer) && isset($bearer->token)){
                $header = [
                    'apikey: '. $api_key,
                    'privatekey: '. $private_key
                ];
                return $bearer->token;
            }else{
                return false;
            }


        } catch (Exception $error) {
            error_log($error->getMessage());
            die($error->getMessage());
        }
    }

    private function formatPayload($data, $cabecera = null)
    {
        try{
            $header = [
                "cache-control: no-cache",
                "accept: application/json",
                "content-type: application/json",
            ];
            $header[] = "authorization: ".$cabecera;

            return $this->apiService(
                'https://apify.epayco.io/payment/session/create',
                $data,
                'POST',
                $header
            );
        } catch (Exception $error) {
            error_log($error->getMessage());
            die($error->getMessage());
        }
    }

}