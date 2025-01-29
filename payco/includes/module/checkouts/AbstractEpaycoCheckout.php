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
require_once EP_ROOT_URL . '/vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AbstractEpaycoCheckout
{
    public $context;
    public $epayco;

    public function __construct($context)
    {
        $this->context = $context;
        $public_key = Configuration::get('EPAYCO_PUBLIC_KEY');
        $private_key = Configuration::get('EPAYCO_PRIVATE_KEY');
        $test = (bool)Configuration::get('EPAYCO_PROD_STATUS');
        $lang = $this->context->language->iso_code == 'es' ? 'es' : "en";
        $this->epayco  = new Epayco\Epayco(array(
            "apiKey" => $public_key,
            "privateKey" => $private_key,
            "lenguage" => $lang,
            "test" => !$test
        ));
    }

    /**
     * Person identification allowed in PSE
     *
     * @return array
     */
    public function getIdentificationDocuments()
    {
        $module = Module::getInstanceByName('payco');
        return array(
            array(
                "id" => $module->l('Type', 'AbstractEpaycoCheckout'),
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