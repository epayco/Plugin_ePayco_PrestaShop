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

class CreditcardEpaycoCheckout
{
    public $name;
    public $context;
    public $path;

    /**
     * Credit card Checkout constructor.
     *
     * @param $name
     * @param $context
     * @param $path
     */
    public function __construct($name, $context, $path)
    {
        $this->name = $name;
        $this->context = $context;
        $this->path = $path;
        $this->assets_ext_min = !_PS_MODE_DEV_ ? '.min' : '';
    }

    /**
     * @param  $cart
     * @return array
     */
    public function getCreditcardCheckoutPS16($cart)
    {
        $checkoutInfo = $this->getCreditcardCheckout($cart);
        $frontInformations = array_merge(
            $checkoutInfo,
            array("ep_logo" => _MODULE_DIR_ . 'payco/views/img/icon-blue-card.png')
        );
        return $frontInformations;
    }

    /**
     * @param  $cart
     * @return array
     */
    public function getCreditcardCheckoutPS17($cart)
    {
        $checkoutInfo = $this->getCreditcardCheckout($cart);
        $frontInformations = array_merge($checkoutInfo, array("module_dir" => $this->path));
        return $frontInformations;
    }

    /**
     * @param $cart
     * @return array
     * @throw PrestaShopException
     */
    public function getCreditcardCheckout($cart)
    {
        $this->loadJsCustom();
        $test = (bool)Configuration::get('EPAYCO_PROD_STATUS');
        $redirect       = $this->context->link->getModuleLink($this->name, 'creditcard');
        $public_key     = Configuration::get('EPAYCO_PUBLIC_KEY');
        $checkoutInfo = array(
            "version"        => EP_VERSION,
            "redirect"       => $redirect,
            "public_key"     => $public_key,
            "assets_ext_min" => $this->assets_ext_min,
            "payment_method_info" => [
                "documents" => json_encode($this->getIdentificationDocuments()),
                "test" => $test,
                "fees" => json_encode([["id"=>"", "description"=> "fees"],["id"=>"1", "description"=> "1"]])
            ],

            "terms_url"      => 'https://epayco.com/terminos-y-condiciones-usuario-pagador-comprador/',
        );

        return $checkoutInfo;
    }

    /**
     * load checkout scripts
     */
    public function loadJsCustom()
    {
        Media::addJsDef([
            'ePaycoPublicKey' => Configuration::get('EPAYCO_PUBLIC_KEY'),
            'lenguaje' => $this->context->language->iso_code
        ]);
        $this->context->controller->addJS(
            $this->path . '/views/js/checkouts/creditcard/ep-creditcard-checkout.js.js?v=' . EP_VERSION
        );
    }


    /**
     * Person identification allowed in PSE
     *
     * @return array
     */
    private function getIdentificationDocuments()
    {
        $module = Module::getInstanceByName('payco');
        return array(
            array(
                "id" => $module->l('Type', 'CreditcardSettings'),
                "name" => "",
                "type" => 'text',
                "min_length" => 0,
                "max_length" => 0,
            ),
            array(
                "id" => "NIT",
                "name" => "NIT",
                "type" => 'number',
                "min_length" => 7,
                "max_length" => 10,
            ),
            array(
                "id" => "CC",
                "name" => "CC",
                "type" => 'number',
                "min_length" => 5,
                "max_length" => 15
            ),
            array(
                "id" => "CE",
                "name" => "CE",
                "type" => 'number',
                "min_length" => 4,
                "max_length" => 8
            ),
            array(
                "id" => "TI",
                "name" => "TI",
                "type" => 'number',
                "min_length" => 4,
                "max_length" => 20
            ),
            array(
                "id" => "PPN",
                "name" => "PPN",
                "type" => 'text',
                "min_length" => 4,
                "max_length" => 12
            ),
            array(
                "id" => "SSN",
                "name" => "SSN",
                "type" => 'number',
                "min_length" => 9,
                "max_length" => 9
            ),
            array(
                "id" => "LIC",
                "name" => "LIC",
                "type" => 'number',
                "min_length" => 1,
                "max_length" => 20
            ),
            array(
                "id" => "DNI",
                "name" => "DNI",
                "type" => 'text',
                "min_length" => 1,
                "max_length" => 20
            ),
        );
    }
}