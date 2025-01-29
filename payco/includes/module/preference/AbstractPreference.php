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

}