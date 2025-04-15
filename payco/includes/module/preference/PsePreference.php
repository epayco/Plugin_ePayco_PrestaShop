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

class PsePreference extends AbstractPreference
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @pararm $cart
     * @param $pse_info
     * @retrun bool|string
     * @throw Exception
     */
    public function createPreference($cart, $pse_info)
    {
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');

        $paymentData = $this->createSessionPayment();
        $orderData = array_merge($paymentData, $pse_info);
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

            $order = new Order($extra2);
            $uri = Context::getContext()->shop->getBaseURL(). $lang  . '/confirmacion-pedido';
            $uri .= '?id_cart=' . $order->id_cart;
            $uri .= '&key=' . $order->secure_key;
            $uri .= '&id_order=' . $order->id;
            $uri .= '&id_module=' . $this->module->id;

            $response = array(
                "bank" => $orderData["bank"],
                'invoice' => $orderData['reference'],
                //'id_order' => $this->module->currentOrder,
                'value' => strval($value),
                'taxBase' => floatval($valorBaseDevolucion),
                'tax' => floatval($iva),
                'taxIco' => floatval('0'),
                "typePerson" => "0",
                //'urlResponse' => $orderData['person_type'],
                'urlResponse' => $uri,
                'urlConfirmation' => $p_url_confirmation,
                'email' => strval($orderData['email']),
                'name' => strval($orderData['name']),
                'lastName' => strval($orderData['name']),
                'address' => strval($orderData['address']),
                'city' => strval($orderData['country']),
                'country' => strval($orderData['countryType']),
                "cellPhone" => $orderData['cellphone'],
                "docType" => $orderData['documentType'],
                "docNumber" =>  $orderData['document'],
                'extra1' => strval($extra1),
                'extra2' => strval($extra2),
                'lang' => $lang,
                'description' => $descripcion,
                'currency' => $currency->iso_code,
                //'ip' =>$myIp,
                'ip' =>'186.97.212.162',
                "testMode" => $test,
                "extras_epayco"=>["extra5"=>"P19"],
                'methodConfirmation'=> "POST"
            );
            $bank = $this->epayco->bank->create($response);
            $response = json_decode(json_encode($bank), true);

            if (is_array($response) && $response['success']) {
                if (in_array(strtolower($response['data']['estado']),["pendiente","pending"])) {
                    $ref_payco = $response['data']['refPayco']??$response['data']['ref_payco'];
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
                    //redirect to order confirmation page
                    Tools::redirect($response['data']['urlbanco']);
                }else{
                    Tools::redirect('index.php?controller=order&step=3&typeReturn=failure');
                }
            }else{
                Tools::redirect('index.php?controller=order&step=3&typeReturn=failure');
            }
        }else{
            $this->getResponse([], 400);
            Tools::redirect('index.php?controller=order&step=3&typeReturn=failure');
        }
    }
}