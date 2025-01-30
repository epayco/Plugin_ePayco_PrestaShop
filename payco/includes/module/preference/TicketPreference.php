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

class TicketPreference extends AbstractPreference
{
    public $ticket_info;

    const CASH_ENTITIES = [
        [
            "id" =>"EF",
            "name" =>"efecty"
        ],
        [
            "id" =>"GA",
            "name" =>"gana"
        ],
        [
            "id" =>"PR",
            "name" =>"puntored"
        ],
        [
            "id" =>"RS",
            "name" =>"redservi"
        ],
        [
            "id" =>"SR",
            "name" =>"sured"
        ],
        [
            'id' => 'SR',
            'name' => 'Suchance'
        ],
        [
            'id' => 'SR',
            'name' => 'Laperla'
        ],
        [
            'id' => 'SR',
            'name' => 'jer'
        ],
        [
            'id' => 'SR',
            'name' => 'pagatodo'
        ],
        [
            'id' => 'SR',
            'name' => 'acertemos'
        ],
        [
            'id' => 'SR',
            'name'  => 'ganagana',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @pararm $cart
     * @param $ticket_info
     * @retrun bool|string
     * @throw Exception
     */
    public function createPreference($cart, $ticket_info)
    {
        $this->ticket_info = $ticket_info;
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');
        $ticket_info["date_expiration"] = '9';
        $payment_method_id= $ticket_info["payment_method_id"];
        $key = array_search( $payment_method_id, array_column(self::CASH_ENTITIES, 'name'));
        $ticket_info['paymentMethod'] = self::CASH_ENTITIES[$key]['id'];
        $paymentData = $this->createSessionPayment();
        $orderData = array_merge($paymentData, $ticket_info);
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
            $end_date = date('y-m-d', strtotime(sprintf('+%s days',"9") ));
            $response = array(
                "paymentMethod" => $orderData["paymentMethod"],
                'invoice' => $orderData['reference'],
                //'id_order' => $this->module->currentOrder,
                'value' => strval($value),
                'taxBase' => strval($valorBaseDevolucion),
                'tax' => strval($iva),
                'taxIco' => '0',
                "type_person" => "0",
                'urlResponse' => $p_url_response,
                'urlConfirmation' => $p_url_confirmation,
                'email' => $orderData['email'],
                'name' => $orderData['name'],
                'lastName' => $orderData['name'],
                'address' => $addressdelivery->address1 . " " . $addressdelivery->address2,
                'city' => $addressdelivery->city,
                'country' => "CO",
                "cellPhone" => $orderData['cellphone'],
                "docType" => $orderData['documentType'],
                "docNumber" =>  $orderData['document'],
                "endDate" => $end_date,
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
            $cash = $this->epayco->cash->create($response);
            $response = json_decode(json_encode($cash), true);
            if (is_array($response) && $response['success']) {
                $order = new Order($extra2);
                $descuento = $order->total_discounts_tax_incl;
                $params = http_build_query([
                    'refPayco' =>  $response['data']['refPayco'],
                    'autorizacion' => $response['data']['autorizacion'],
                    'descuento' => $descuento,
                    'factura' => $response['data']['invoice'],
                    'descripcion' => $descripcion,
                    'valor' => $response['data']['value'],
                    'ip' => $response['data']['ip'],
                    'estado' => $response['data']['status'],
                    'fecha' => $response['data']['paymentDate'],
                    'franquicia' => $response['data']['franchise'],
                    'respuesta' => $response['data']['response']
                ]);

                // URL completa
                $url =  Context::getContext()->link->getModuleLink('payco', 'download_receipt') . '?' . $params;
                $epaycoOrder = [
                    'success' => true,
                    'status' => $response['data']['status'],
                    'type' => 'type',
                    'refPayco'  => $response['data']['refPayco'],
                    'factura'  => $response['data']['invoice'],
                    'descripcion'  => $response['data']['description'],
                    'valor' => $response['data']['value'],
                    'iva' => $response['data']['tax'],
                    'ip' => $response['data']['ip'],
                    'estado' => $response['data']['status'],
                    'respuesta' => $response['data']['response'],
                    'fecha' => $response['data']['paymentDate'],
                    'descuento' => $descuento,
                    'autorizacion' => $response['data']['autorizacion'],
                    'franquicia' => $response['data']['franchise'],
                    'extra1' => $response['data']['extras']["extra1"],
                    'baseurl' => $url,
                    'pin'  => $response['data']['pin'],
                    'codeProject'  => $response['data']['codeProject'],
                    'expirationDate' => $response['data']['expirationDate']
                ];
                $this->context->cookie->__set('redirect_epayco_message', json_encode($epaycoOrder));
                $this->context->cookie->write();

                $uri = __PS_BASE_URI__ . 'index.php?controller=order-confirmation';
                $uri .= '&id_cart=' . $order->id_cart;
                $uri .= '&key=' . $order->secure_key;
                $uri .= '&id_order=' . $order->id;
                $uri .= '&id_module=' . $this->module->id;
                $uri .= '&ref_payco=' . $response['data']['refPayco'];

                //redirect to order confirmation page
                Tools::redirect($uri);
            }else{
                Tools::redirect('index.php?controller=order&step=3&typeReturn=failure');
            }
        }else{
            $this->getResponse([], 400);
            Tools::redirect('index.php?controller=order&step=3&typeReturn=failure');
        }
    }

}