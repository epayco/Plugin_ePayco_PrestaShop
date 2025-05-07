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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2024 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once EP_ROOT_URL . '/includes/module/checkouts/AbstractEpaycoCheckout.php';

class DaviplataEpaycoCheckout extends AbstractEpaycoCheckout
{
    public $name;
    public $context;
    public $path;
    public $assetsExtMin;
    /**
     * Daviplata Checkout constructor
     *
     * @param $name
     * @param $context
     * @param $path
     */
    public function __construct($name, $context, $path)
    {
        parent::__construct($context);
        $this->name = $name;
        $this->context = $context;
        $this->path = $path;
        $this->assetsExtMin = !_PS_MODE_DEV_ ? '.min' : '';
    }

    /**
     * @param  $cart
     * @return array
     */
    public function getDaviplataCheckoutPS16($cart)
    {
        $checkoutInfo = $this->getDaviplataCheckout($cart);
        $frontInformations = array_merge(
            $checkoutInfo,
            array("ep_logo" => _MODULE_DIR_ . 'payco/views/img/icon-daviplata.png')
        );
        return $frontInformations;
    }

    /**
     * @param  $cart
     * @return array
     */
    public function getDaviplataCheckoutPS17($cart)
    {
        $checkoutInfo = $this->getDaviplataCheckout($cart);
        $frontInformations = array_merge($checkoutInfo, array("module_dir" => $this->path));
        return $frontInformations;
    }

    /**
     * @param $cart
     * @return array
     * @throw PrestaShopException
     */
    public function getDaviplataCheckout($cart)
    {
        //$this->loadJsCustom();
        $test = (bool)Configuration::get('EPAYCO_PROD_STATUS');
        $redirect       = $this->context->link->getModuleLink($this->name, 'daviplata');
        $public_key     = Configuration::get('EPAYCO_PUBLIC_KEY');
        $checkoutInfo = array(
            "version"        => EP_VERSION,
            "redirect"       => $redirect,
            "public_key"     => $public_key,
            "assets_ext_min" => $this->assetsExtMin,
            "payment_method_info" => [
                "documents" => json_encode($this->getIdentificationDocuments()),
                "test" => $test,
                "fees" => json_encode([["id"=>"", "description"=> "fees"],["id"=>"1", "description"=> "1"]])
            ],

            "terms_url"      => 'terminos-y-condiciones-generales/',
        );

        return $checkoutInfo;
    }
}