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

class StandardSettings extends AbstractSettings
{
    public function __construct()
    {
        parent::__construct();
        $this->submit = 'submitEpaycoStandard';
        $this->values = $this->getFormValues();
        $this->form = $this->generateForm();
        $this->process = $this->verifyPostProcess();
    }

    /**
     * Generate inputs form
     *
     * @return void
     */
    public function generateForm()
    {

        $title = $this->module->l('Basic Configuration', 'StandardSettings');
        $fields = array(
            array(
                'type' => 'text',
                'label' => $this->module->l('Titulo', 'StandardSettings'),
                'name' => 'EPAYCO_STANDARD_TITLE',
                'required' => true,
                'desc' => $this->module->l('Payment title.', 'StandardSettings'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('Activate checkout', 'StandardSettings'),
                'name' => 'EPAYCO_STANDARD_CHECKOUT',
                'desc' => $this->module->l('Activate the ePayco experience at the checkout of your store.', 'StandardSettings'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'EPAYCO_STANDARD_CHECKOUT_ON',
                        'value' => true,
                        'label' => $this->module->l('Active', 'StandardSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_STANDARD_CHECKOUT_OFF',
                        'value' => false,
                        'label' => $this->module->l('Inactive', 'StandardSettings')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('Modal checkout', 'StandardSettings'),
                'name' => 'EPAYCO_STANDARD_MODAL',
                'is_bool' => true,
                'desc' =>
                    $this->module->l('Your customers will access the ePayco payment ', 'StandardSettings') .
                    $this->module->l('form without leaving your store. If you deactivate it, ', 'StandardSettings') .
                    $this->module->l('they will be redirected to another page.', 'StandardSettings'),
                'values' => array(
                    array(
                        'id' => 'EPAYCO_STANDARD_MODAL_ON',
                        'value' => true,
                        'label' => $this->module->l('Active', 'StandardSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_STANDARD_MODAL_OFF',
                        'value' => false,
                        'label' => $this->module->l('Inactive', 'StandardSettings')
                    )
                ),
            ),
        );

        return $this->buildForm($title, $fields);
    }

    /**
     * Save form data
     *
     * @return array
     */
    public function postFormProcess()
    {
        $this->validate = ([
            'EPAYCO_STANDARD_TITLE' => 'title'
        ]);
        parent::postFormProcess();

        Configuration::updateValue('EPAYCO_STANDARD', true);
    }

    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {
        $form_values = array(
            'EPAYCO_STANDARD_TITLE' => Configuration::get('EPAYCO_STANDARD_TITLE'),
            'EPAYCO_STANDARD_CHECKOUT' => Configuration::get('EPAYCO_STANDARD_CHECKOUT'),
            'EPAYCO_STANDARD_MODAL' => Configuration::get('EPAYCO_STANDARD_MODAL'),
        );

        return $form_values;
    }

}