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

require_once EP_ROOT_URL . '/includes/module/checkouts/AbstractEpaycoCheckout.php';

class PseCheckoutEpayco extends AbstractEpaycoCheckout
{
    const PSE_CHECKOUT_EPAYCO_NAME = 'EPAYCO_PSE_CHECKOUT';

    const PAYMENT_METHOD_NAME = 'pse';

    const CHECKOUT_TYPE = 'custom';

    /**
     * @var string
     */
    public $assetsExtMin;

    public function __construct($context)
    {
        parent::__construct($context);
        $this->assetsExtMin = !_PS_MODE_DEV_ ? '.min' : '';
    }
    /**
     * @param array $pluginInfos
     *
     * @return array
     * @throw PrestaShopException
     */public function getPseTemplateData(array $pluginInfos)
    {

        //$redirect       = $this->context->link->getModuleLink($this->name, 'pse');
        $redirect = $pluginInfos['redirect_link'];
        $termsUrl = 'https://epayco.com/terminos-y-condiciones-usuario-pagador-comprador/';
        $moduleDir = $pluginInfos['module_dir'];
        $templateData = array(
            "payment_method_info" => $this->getPsePaymentMethod(),
            "site_id" => '',
            "version" => EP_VERSION,
            "redirect" => $redirect,
            "module_dir" => $moduleDir,
            "terms_url" => $termsUrl,
            "assets_ext_min" => $this->assetsExtMin,
            "discount" => ''
        );

        return array_merge($templateData, array("module_dir" => $moduleDir));
    }

    /**
     * PSE payment method
     *
     * @return array
     */
    private function getPsePaymentMethod()
    {
        $test = (bool)Configuration::get('EPAYCO_PROD_STATUS');
        $bancos = $this->epayco->bank->pseBank($test);
        if(isset($bancos) && isset($bancos->data) ){
            $banks = (array) $bancos->data;
            $convertedBanks = array();
            foreach ($banks as $bank) {
                $convertedBanks[] = array(
                    'id' => $bank->bankCode,
                    'description' => $bank->bankName
                );
            }
        }else{
            $convertedBanks[] =['id' => 0, 'description' => "Selecciona el banco"];
        }
        return array(
            "test" => $test,
            "id" => 'PSE',
            "name" => 'PSE',
            "person_types" => $this->getPersonTypes(),
            "persons_types" => json_encode( $this->getPersonTypes()),
            "financial_institutions" => $convertedBanks,
            "banks" => json_encode($convertedBanks),
            "allowed_identification_types" => $this->getIdentificationDocuments(),
            "documents" => json_encode($this->getIdentificationDocuments()),
        );
    }

    /**
     * Person types allowed in PSE
     *
     * @return array
     */
    private function getPersonTypes()
    {
        $module = Module::getInstanceByName('payco');
        return array(
            array(
                "id" => "PN",
               "description" => $module->l('Persona natural', 'PseSettings'),
            ),
            array(
                "id" => "PJ",
                "description" => $module->l('Persona jurídica', 'PseSettings'),
            ),
        );
    }


}