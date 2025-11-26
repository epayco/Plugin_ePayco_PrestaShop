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

class TicketSettings extends AbstractSettings
{
    public $ticket_payment;

    public function __construct()
    {
        parent::__construct();
        $this->submit = 'submitEpaycoTicket';
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
        $title = $this->module->l('Configuración básica', 'TicketSettings');
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
                            margin-left: -128px;
                        }
                        .epayco-section-title-2 {
                            font-size: 16px;
                            font-weight: 600;
                            color: #333;
                            margin: 20px 0 10px 0;
                            margin-left: -128px;
                        }
                        .epayco-section-desc {
                            font-size: 13px;
                            color: #666;
                            margin-bottom: 15px;
                            line-height: 1.5;
                            margin-left: -128px;
                        }
                    </style>
                    <div class="epayco-section-title">
                        ' . $this->module->l('Activar', 'TicketSettings') . '
                    </div>
                    <div class="epayco-section-desc">
                        ' . $this->module->l('Al desactivarlo, desactivará los pagos en efectivo.', 'TicketSettings') . '
                    </div>
                ',
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('El pago está habilitado', 'TicketSettings'),
                'name' => 'EPAYCO_TICKET_CHECKOUT',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'EPAYCO_TICKET_CHECKOUT_ON',
                        'value' => true,
                        'label' => $this->module->l('Sí', 'TicketSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_TICKET_CHECKOUT_OFF',
                        'value' => false,
                        'label' => $this->module->l('No', 'TicketSettings')
                    )
                ),
            ),
            array(
                'type' => 'html',
                'name' => '',
                'html_content' => '
                    <div class="epayco-section-desc" style="margin-top: 10px; margin-bottom: 20px;">
                        ' . $this->module->l('Activa la opción de pagos en efectivo en tu tienda.', 'TicketSettings') . '
                    </div>
                    <div class="epayco-section-title-2">
                        ' . $this->module->l('Métodos de pago', 'TicketSettings') . '
                    </div>
                    <div class="epayco-section-desc">
                        ' . $this->module->l('Selecciona cómo deseas procesar los pagos en efectivo de tus clientes en PrestaShop.', 'TicketSettings') . '
                    </div>
                ',
            ),
            array(
                'col' => 4,
                'type' => 'checkbox',
                'label' => '',
                'name' => 'EPAYCO_TICKET_PAYMENT',
                'hint' => $this->module->l('Habilite los métodos de pago disponibles para sus clientes.', 'TicketSettings'),
                'class' => 'payment-ticket-checkbox',
                'desc' => ' ',
                'values' => array(
                    'query' => $this->ticket_payments,
                    'id' => 'id',
                    'name' => 'name'
                )
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
        $this->validate = ([
            'EPAYCO_TICKET_TITLE' => 'title'
        ]);
        if ($this->validatePaymentMethods()) {
            parent::postFormProcess();
        }
    }

    /**
     * Validates if at least one payment method is checked
     *
     * @return boolean
     */
    public function validatePaymentMethods()
    {
        $count_total = 0;
        $count_checked = 0;
        $payment_methods = array_keys($this->values);

        foreach ($payment_methods as $key) {
            if (strstr($key, 'EPAYCO_TICKET_PAYMENT_')) {
                $count_total++;
                if (Tools::getValue($key) == '') {
                    $count_checked++;
                }
            }
        }

        if ($count_checked == $count_total) {
            Epayco::$form_alert = 'alert-danger';
            Epayco::$form_message = $this->module->l('No es posible remover ', 'TicketSettings') .
                $this->module->l('todos los métodos de pago para el checkout en efectivo.', 'TicketSettings');
            return false;
        }

        return true;
    }

    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {
        $form_values = array(
            'EPAYCO_TICKET_CHECKOUT' => Configuration::get('EPAYCO_TICKET_CHECKOUT')
        );
        $ticketPaymentMethods = [
            [
                'id' => 'sured',
                'name'              => 'Su Red',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/sured.jpg'
            ],
            [
                'id' => 'pagatodo',
                'name'              => 'Pago Todo',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/pagatodo.jpg'
            ],
            [
                'id' => 'gana',
                'name'              => 'Gana',
                'status'            => 'active',
                'thumbnail'         => 'https://secure.epayco.co/img/gana_no_red.png'
            ],
            [
                'id' => 'acertemos',
                'name'              => 'Acertemos',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/acertemos.jpg'
            ],
            [
                'id' => 'ganagana',
                'name'              => 'Gana Gana',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/ganagana.jpg'
            ],
            [
                'id' => 'suchance',
                'name'              => 'Suchance',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/suchance.jpg'
            ],
            [
                'id' => 'redservi',
                'name'              => 'Red Servicios del Cesar',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/redservi.jpg'
            ],
            [
                'id' => 'apuestas',
                'name'              => 'Apuestas Cúcuta 75',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/apuestas.jpg'
            ],
            [
                'id' => 'jer',
                'name'              => 'Jer',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/jer.jpg'
            ],
            [
                'id' => 'laperla',
                'name'              => 'La Perla',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/laperla.jpg'
            ],
            [
                'id' => 'efecty',
                'name'              => 'Efecty',
                'status'            => 'active',
                'thumbnail'         => 'https://secure.epayco.co/img/efecty.png'
            ],
            [
                'id' => 'puntored',
                'name'              => 'Punto Red',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/puntored.jpg'
            ],
            [
                'id' => 'redseril',
                'name'              => 'Red Servi',
                'status'            => 'active',
                'secure_thumbnail'         => 'https://secure.epayco.co/img/redseril.jpg'
            ],
        ];

        foreach ($ticketPaymentMethods as $payment_method) {
            $id = $payment_method['id'];
            $name = 'EPAYCO_TICKET_PAYMENT_' . $id;
            $this->ticket_payments[] = array(
                'id' => $id,
                'name' =>  $payment_method['name'] ,
            );

            $form_values[$name] = Configuration::get($name);
        }
        return $form_values;
    }
}