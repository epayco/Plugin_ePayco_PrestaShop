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

require_once EP_ROOT_URL . '/includes/module/settings/AbstractSettings.php';

class PseSettings extends AbstractSettings
{
    public function __construct()
    {
        parent::__construct();
        $this->submit = 'submitEpaycoPse';
        $this->values =  $this->getFormValues();
        $this->form = $this->generateForm();
        $this->process = $this->verifyPostProcess();
    }


    /**
     * Generate inputs form
     *
     * @return array
     */
    public function generateForm()
    {
        $title = $this->module->l('Basic Configuration','PseSettings');
       // $fields = array();

        //if($this->module->isEnabledPaymentMethod('pse')){
        $fields = array(
            array(
                'type' => 'text',
                'label' => $this->module->l('TITLE', 'PseSettings'),
                'name' => 'EPAYCO_PSE_TITLE',
                'required' => true,
                'desc' => $this->module->l('Payment title.', 'PseSettings'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('Activate checkout', 'PseSettings'),
                'name' => 'EPAYCO_PSE_CHECKOUT',
                'desc' => $this->module->l('Activate the Pse experience at the checkout of your store.', 'PseSettings'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'EPAYCO_PSE_CHECKOUT_ON',
                        'value' => true,
                        'label' => $this->module->l('Active', 'PseSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_PSE_CHECKOUT_OFF',
                        'value' => false,
                        'label' => $this->module->l('Inactive', 'PseSettings')
                    )
                ),
            ),
        );
       // }
        return $this->buildForm($title, $fields);
    }

    /**
     * Save form data
     *
     * @return void
     */
    public function postFormProcess()
    {
        $this->validate = ([
            'EPAYCO_PSE_TITLE' => 'title'
        ]);
        parent::postFormProcess();
    }


    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {
        $formValues = array(
            'EPAYCO_PSE_TITLE' => Configuration::get('EPAYCO_PSE_TITLE'),
            'EPAYCO_PSE_CHECKOUT' => Configuration::get('EPAYCO_PSE_CHECKOUT'),

        );

        return $formValues;
    }
}