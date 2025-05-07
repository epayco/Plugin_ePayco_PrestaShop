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
        $title = $this->module->l('Basic Configuration', 'TicketSettings');
        $fields = array(
            array(
                'type' => 'text',
                'label' => $this->module->l('Titulo', 'TicketSettings'),
                'name' => 'EPAYCO_TICKET_TITLE',
                'required' => true,
                'desc' => $this->module->l('Payment title.', 'TicketSettings'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->module->l('Activate Checkout of face to face payments', 'TicketSettings'),
                'name' => 'EPAYCO_TICKET_CHECKOUT',
                'desc' => $this->module->l('Activate the option of face to face payments in your store.', 'TicketSettings'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'EPAYCO_TICKET_CHECKOUT_ON',
                        'value' => true,
                        'label' => $this->module->l('Active', 'TicketSettings')
                    ),
                    array(
                        'id' => 'EPAYCO_TICKET_CHECKOUT_OFF',
                        'value' => false,
                        'label' => $this->module->l('Inactive', 'TicketSettings')
                    )
                ),
            ),
            array(
                'col' => 4,
                'type' => 'checkbox',
                'label' => $this->module->l('Payment methods', 'TicketSettings'),
                'name' => 'EPAYCO_TICKET_PAYMENT',
                'hint' => $this->module->l('Enable the payment methods available to your customers.', 'TicketSettings'),
                'class' => 'payment-ticket-checkbox',
                'desc' => ' ',
                'values' => array(
                    'query' => $this->ticket_payments,
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            /*array(
                'col' => 2,
                'suffix' => $this->module->l('days', 'TicketSettings'),
                'label' => $this->module->l('Payment due', 'TicketSettings'),
                'type' => 'text',
                'name' => 'EPAYCO_TICKET_EXPIRATION',
                'desc' => $this->module->l('In how many days will the face to face payments expire.', 'TicketSettings'),
            )*/
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
            Epayco::$form_message = $this->module->l('It is not possible to remove ', 'TicketSettings') .
                $this->module->l('all payment methods for ticket checkout.', 'TicketSettings');
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
            'EPAYCO_TICKET_TITLE' => Configuration::get('EPAYCO_TICKET_TITLE'),
            'EPAYCO_TICKET_CHECKOUT' => Configuration::get('EPAYCO_TICKET_CHECKOUT')
            //'EPAYCO_TICKET_EXPIRATION' => Configuration::get('EPAYCO_TICKET_EXPIRATION'),
        );
        $ticketPaymentMethods = [
            [
            'id' => 'sured',
            'name' => 'Sured',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/sured.jpg'
            ],
            [
            'id' => 'pagatodo',
            'name' => 'Pagatodo',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/pagatodo.jpg'
            ],
            [
            'id' => 'gana',
            'name' => 'Gana',
            'status' => 'active',
            'thumbnail' => 'https://secure.epayco.co/img/gana_no_red.png'
            ],
            [
            'id' => 'acertemos',
            'name' => 'Acertemos',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/acertemos.jpg'
            ],
            [
            'id' => 'ganagana',
            'name' => 'Ganagana',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/ganagana.jpg'
            ],
            [
            'id' => 'suchance',
            'name' => 'Suchance',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/suchance.jpg'
            ],
            [
            'id' => 'redserviciosdelcesar',
            'name' => 'Red Servicios del Cesar',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/redserviciosdelcesar.jpg'
            ],
            [
            'id' => 'apuestas75',
            'name' => 'Apuestas Cúcuta 75',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/apuestas75.jpg'
            ],
            [
            'id' => 'jer',
            'name' => 'Jer',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/jer.jpg'
            ],
            [
            'id' => 'laperla',
            'name' => 'Laperla',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/laperla.jpg'
            ],
            [
            'id' => 'efecty',
            'name' => 'Efecty',
            'status' => 'active',
            'thumbnail' => 'https://secure.epayco.co/img/efecty.png'
            ],
            [
            'id' => 'puntored',
            'name' => 'Puntored',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/puntored.jpg'
            ],
            [
            'id' => 'redservi',
            'name' => 'Redservi',
            'status' => 'active',
            'secure_thumbnail' => 'https://secure.epayco.co/img/redservi.jpg'
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