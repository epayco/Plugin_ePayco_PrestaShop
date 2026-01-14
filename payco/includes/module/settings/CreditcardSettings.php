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
 * versions in the future. If you wish to CREDITCARDize PrestaShop for your
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

class CreditcardSettings extends AbstractSettings
{
    public function __construct()
    {
        parent::__construct();
        $this->submit = 'submitEpaycoCreditcard';
        $this->values = $this->getFormValues();
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
        $title = $this->module->l('Configuración básica', 'CreditcardSettings');
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
                            margin-left: -270px;
                        }
                        .epayco-section-title-2 {
                            font-size: 16px;
                            font-weight: 600;
                            color: #333;
                            margin: 20px 0 10px 0;
                            margin-left: -270px;
                        }
                        .epayco-section-desc {
                            font-size: 13px;
                            color: #666;
                            margin-bottom: 15px;
                            line-height: 1.5;
                            margin-left: -270px;
                        }
                    </style>
                    <div class="epayco-section-title">
                        ' . $this->module->l('Activar', 'CreditcardSettings') . '
                    </div>
                    <div class="epayco-section-desc">
                        ' . $this->module->l('Al desactivarlo, desactivará todos los pagos con tarjeta de crédito.', 'CreditcardSettings') . '
                    </div>
                ',
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('El pago está habilitado', 'CreditcardSettings'),
                'name' => 'EPAYCO_CREDITCARD_CHECKOUT',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'EPAYCO_CREDITCARD_CHECKOUT_ON',
                        'value' => true,
                        'label' => $this->module->l('Sí', 'CreditcardSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_CREDITCARD_CHECKOUT_OFF',
                        'value' => false,
                        'label' => $this->module->l('No', 'CreditcardSettings')
                    )
                ),
            ),
            array(
                'type' => 'html',
                'name' => '',
                'html_content' => '
                    <div class="epayco-section-desc" style="margin-top: 10px; margin-left: -270px;">
                        ' . $this->module->l('Inicia y finaliza la compra dentro de la tienda sin ser redireccionado.', 'CreditcardSettings') . '
                    </div>
                ',
            ),
        );

        return $this->buildForm($title, $fields);
    }

    /**
     * Save form data
     *
     * @return void
     */
    public function postFormProcess()
    {
        $this->validate = array();
        parent::postFormProcess();
    }

    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {
        return array(
            'EPAYCO_CREDITCARD_CHECKOUT' => Configuration::get('EPAYCO_CREDITCARD_CHECKOUT'),
        );
    }
}