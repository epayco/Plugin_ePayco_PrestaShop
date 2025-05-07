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

class StandardCheckoutEpayco
{
    public $name;
    public $context;
    public $path;
    /**
     * Custom Checkout constructor.
     *
     * @param $name
     * @param $context
     * @param $path
     */
    public function __construct($name,$context,$path)
    {
        $this->name = $name;
        $this->context = $context;
        $this->path = $path;
        $this->assets_ext_min = !_PS_MODE_DEV_ ? '.min' : '';
    }

    /**
     * @param $cart
     * @return array
     */
    public function getStandardCheckoutPS16($cart)
    {
        $informations = $this->getStandard($cart);
        $frontInformations = array_merge(
            $informations,
            array("mp_logo" => _MODULE_DIR_ . 'payco/views/img/logo.png')
        );
        return $frontInformations;
    }

    /**
     * @param $cart
     * @return array
     */
    public function getStandardCheckoutPS17($cart)
    {
        $informations = $this->getStandard($cart);
        $frontInformations = array_merge($informations, array("module_dir" => $this->path));
        return $frontInformations;
    }

    /**
     * @param $cart
     * @return array
     */
    public function getStandard($cart)
    {
        $this->loadJsStandard();

        $modal = Configuration::get('EPAYCO_STANDARD_MODAL');
        $redirect = $this->context->link->getModuleLink($this->name, 'standard');

        $informations = array(
            "version" => EP_VERSION,
            "modal" => $modal,
            "redirect" => $redirect,
            "public_key" => Configuration::get('EPAYCO_PUBLIC_KEY'),
            "assets_ext_min" => $this->assets_ext_min,
            "terms_url" => 'https://epayco.com/terminos-y-condiciones-generales/',
        );
        return $informations;
    }

    /**
     *
     */
    public function loadJsStandard()
    {
        $this->context->controller->addJS(
            'https://epayco-checkout-testing.s3.amazonaws.com/checkout.preprod.js'
        );
        $this->context->controller->addJS(
            $this->path . '/views/js/standard' . $this->assets_ext_min . '.js?v=' . EP_VERSION
        );
    }
}
