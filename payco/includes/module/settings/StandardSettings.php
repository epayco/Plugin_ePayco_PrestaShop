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

        $title = $this->module->l('Configuración básica', 'StandardSettings');
        $fields = array(
            array(
                'type' => 'html',
                'name' => '',
                'html_content' => '
                    <style>
                        .epayco-section-title {
                            font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 20px 0 10px 0;
    /* padding-bottom: 10px; */
    /* border-bottom: 2px solid #007bff; */
    margin-left: -128px;
                        }
     .epayco-section-title-2 {
                            font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 20px 0 10px 0;
    /* padding-bottom: 10px; */
    /* border-bottom: 2px solid #007bff; */
    margin-left: -128px;
                        }
                        .epayco-section-desc {
                            font-size: 13px;
                            color: #666;
                            margin-bottom: 15px;
                            line-height: 1.5;
                            margin-left: -128px;
                        }
                        .epayco-form-group {
                            margin-bottom: 20px;
                        }
                        .epayco-switch-wrapper {
                            background-color: #f8f9fa;
                            padding: 15px;
                            border-radius: 5px;
                            margin-bottom: 15px;
                        }
                        .epayco-switch-label {
                            font-weight: 600;
                            color: #333;
                            font-size: 14px;
                            margin-bottom: 5px;
                            display: block;
                        }
                        .epayco-switch-desc {
                            font-size: 12px;
                            color: #666;
                            margin-top: 5px;
                        }
                    </style>
                    <div class="epayco-section-title">
                        ' . $this->module->l('Activar ePayco Checkout', 'StandardSettings') . '
                    </div>
                    <div class="epayco-section-desc">
                        ' . $this->module->l('Al desactivarlo, desactivará el pago de ePayco.', 'StandardSettings') . '
                    </div>
                ',
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('El pago está habilitado', 'StandardSettings'),
                'name' => 'EPAYCO_STANDARD_CHECKOUT',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'EPAYCO_STANDARD_CHECKOUT_ON',
                        'value' => true,
                        'label' => $this->module->l('Sí', 'StandardSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_STANDARD_CHECKOUT_OFF',
                        'value' => false,
                        'label' => $this->module->l('No', 'StandardSettings')
                    )
                ),
            ),
            array(
                'type' => 'html',
                'name' => '',
                'html_content' => '
                    <div class="epayco-section-title-2">
                        ' . $this->module->l('Modo de pago', 'StandardSettings') . '
                    </div>
                    <div class="epayco-section-desc">
                        ' . $this->module->l('Selecciona cómo deseas procesar los pagos de tus clientes en Prestashop.', 'StandardSettings') . '
                    </div>
                ',
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('OnPage Checkout:', 'StandardSettings'),
                'name' => 'EPAYCO_STANDARD_MODAL',
                'is_bool' => true,
                'desc' => $this->module->l('El pago se realiza dentro de la tienda, sin redirección.', 'StandardSettings'),
                'values' => array(
                    array(
                        'id' => 'EPAYCO_STANDARD_MODAL_ON',
                        'value' => true,
                        'label' => $this->module->l('Activo', 'StandardSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_STANDARD_MODAL_OFF',
                        'value' => false,
                        'label' => $this->module->l('Inactivo', 'StandardSettings')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('Estándar Checkout:', 'StandardSettings'),
                'name' => 'EPAYCO_STANDARD_MODAL_INACTIVE',
                'is_bool' => true,
                'desc' => $this->module->l('Se abre una nueva página (landing de ePayco) para completar el pago.', 'StandardSettings'),
                'values' => array(
                    array(
                        'id' => 'EPAYCO_STANDARD_MODAL_INACTIVE_ON',
                        'value' => false,
                        'label' => $this->module->l('Activo', 'StandardSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_STANDARD_MODAL_INACTIVE_OFF',
                        'value' => true,
                        'label' => $this->module->l('Inactivo', 'StandardSettings')
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
            'EPAYCO_STANDARD_CHECKOUT' => Configuration::get('EPAYCO_STANDARD_CHECKOUT'),
            'EPAYCO_STANDARD_MODAL' => Configuration::get('EPAYCO_STANDARD_MODAL'),
            'EPAYCO_STANDARD_MODAL_INACTIVE' => !Configuration::get('EPAYCO_STANDARD_MODAL'),
        );

        return $form_values;
    }

}