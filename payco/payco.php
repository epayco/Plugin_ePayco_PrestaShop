<?php

/**
 * 2007-2018 PrestaShop
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
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use EpaycoOrder as EpaycoOrder;

include(_PS_MODULE_DIR_ . 'payco/lib/EpaycoOrder.php');
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_Order.php');
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_OrderState.php');

class Payco extends PaymentModule
{
    protected $config_form = false;
    private $_html = '';
    private $_postErrors = array();
    public $orderStates;
    public $p_cust_id_cliente;
    public $p_key;
    public $public_key;
    public $private_key;
    public $p_test_request;
    public $lenguaje;
    public $p_url_response;
    public $p_url_confirmation;
    public $p_state_end_transaction;
    public $p_type_checkout;

    public function __construct()
    {

        $this->name = 'payco';
        $this->tab = 'payments_gateways';
        $this->version = '1.9.5.1';
        $this->author = 'payco';
        $this->need_instance = 0;



        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Pagar con epayco');
        $this->description = $this->l('ePayco: Paga con Tarjeta de crédito/débito nacional e internacional, PSE, Daviplata, Nequi, Paypal, Efectivo, Safetypay y muchos más.');

        // Definir constante global
        if (!defined('_EPAYCO_MULTIMEDIA_URL_')) {
            define('_EPAYCO_MULTIMEDIA_URL_', 'https://multimedia.epayco.co');
        }
        $this->confirmUninstall = $this->l('Esta seguro de desistalar este modulo?');
        $config = Configuration::getMultiple(array(
            'P_CUST_ID_CLIENTE',
            'P_KEY',
            'PUBLIC_KEY',
            'PRIVATE_KEY',
            'P_TEST_REQUEST',
            'LENGUAJE',
            'P_TITULO',
            'P_TYPE_CHECKOUT',
            'P_STATE_END_TRANSACTION',
            'P_REDUCE_STOCK_PENDING',
            'P_URL_RESPONSE',
            'P_URL_CONFIRMATION'
        ));
        if (isset($config['P_CUST_ID_CLIENTE']))
            $this->p_cust_id_cliente = trim($config['P_CUST_ID_CLIENTE']);
        if (isset($config['P_KEY']))
            $this->p_key = trim($config['P_KEY']);
        if (isset($config['PUBLIC_KEY']))
            $this->public_key = trim($config['PUBLIC_KEY']);
        if (isset($config['PRIVATE_KEY']))
            $this->private_key = trim($config['PRIVATE_KEY']);
        if (isset($config['P_TEST_REQUEST']))
            $this->p_test_request = $config['P_TEST_REQUEST'];
        if (isset($config['LENGUAJE']))
            $this->lenguaje = $config['LENGUAJE'];
        if (isset($config['P_TITULO']))
            $this->p_titulo = trim($config['P_TITULO']);
        if (isset($config['P_TYPE_CHECKOUT']))
            $this->p_type_checkout = $config['P_TYPE_CHECKOUT'];
        if (isset($config['P_SPLIT_PRIMARY_RECEIVER_FEE']))
            $this->p_state_end_transaction = $config['P_STATE_END_TRANSACTION'];
        if (isset($config['P_REDUCE_STOCK_PENDING']))
            $this->p_reduce_stock_pending = $config['P_REDUCE_STOCK_PENDING'];
        if (isset($config['P_URL_RESPONSE']))
            $this->p_url_response = trim($config['P_URL_RESPONSE']);
        if (isset($config['P_URL_CONFIRMATION']))
            $this->p_url_confirmation = trim($config['P_URL_CONFIRMATION']);
        if (!isset($this->p_cust_id_cliente) or !isset($this->p_key) or !isset($this->public_key))
            $this->warning = $this->l('P_CUST_ID_CLIENTE, P_KEY y PUBLIC_KEY deben estar configurados para utilizar este módulo correctamente');
        if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
            $this->warning = $this->l('No currency set for this module');
    }

    /**
     * @return void
     */
    public function hookDisplayHeader()
    {
        $this->context->controller->registerJavascript('epayco-checkout', 'https://checkout.epayco.co/checkout.js', ['position' => 'bottom', 'priority' => 150]);
        $this->context->controller->registerStylesheet(
            'epayco-checkout-css',
            $this->getPathUri() . 'views/css/back.css',
            ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/front.js', 'all');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if ((int)Configuration::get('payco') == 1) {
            $this->_errors[] = $this->l('El modulo ePayco actualmente ya esta instado');
            return false;
        }

        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        Configuration::updateValue('P_TITULO', 'Checkout ePayco, (Tarjetas de crédito,debito,efectivo.)');
        Configuration::updateValue('P_CUST_ID_CLIENTE', '');
        Configuration::updateValue('P_KEY', '');
        Configuration::updateValue('PUBLIC_KEY', '');
        Configuration::updateValue('PRIVATE_KEY', '');
        Configuration::updateValue('P_TEST_REQUEST', false);
        Configuration::updateValue('LENGUAJE', false);
        Configuration::updateValue('P_STATE_END_TRANSACTION', '');
        Configuration::updateValue('P_REDUCE_STOCK_PENDING', true);
        Configuration::updateValue('P_URL_RESPONSE', Context::getContext()->link->getModuleLink('payco', 'response'));
        Configuration::updateValue('P_URL_CONFIRMATION', Context::getContext()->link->getModuleLink('payco', 'confirmation'));
        //Set up our currencies and issuers
        CreditCard_OrderState::remove();
        CreditCard_OrderState::setup();
        //CreditCard_Issuer::setup();
        CreditCard_Order::setup();
        EpaycoOrder::remove();
        EpaycoOrder::setup();

        Configuration::updateValue('payco', true);
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('paymentOptions');
    }

    public function uninstall()
    {
        CreditCard_Order::remove();
        CreditCard_OrderState::remove();
        Configuration::deleteByName('PAYCO_LIVE_MODE');
        Configuration::deleteByName('P_TITULO');
        Configuration::deleteByName('P_CUST_ID_CLIENTE');
        Configuration::deleteByName('P_KEY');
        Configuration::deleteByName('PUBLIC_KEY');
        Configuration::deleteByName('PRIVATE_KEY');
        Configuration::deleteByName('P_TEST_REQUEST');
        Configuration::deleteByName('LENGUAJE');
        Configuration::deleteByName('P_STATE_END_TRANSACTION');
        Configuration::deleteByName('P_REDUCE_STOCK_PENDING');
        Configuration::deleteByName('payco', false);
        Configuration::deleteByName('P_TYPE_CHECKOUT');
        Configuration::deleteByName('P_URL_RESPONSE');
        Configuration::deleteByName('P_URL_CONFIRMATION');

        return parent::uninstall();
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (Tools::isSubmit('btnSubmit')) {
            $this->postValidation();
            if (!count($this->_postErrors)) {
                $this->postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }



        $this->context->smarty->assign(array(
            'module_dir' => $this->_path
        ));
        $this->_html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $states = CreditCard_OrderState::getOrderStates();

        $order_states = array();

        foreach ($states as $state) {
            $order_states[] = array("id" => $state["id_order_state"], "name" => $state["name"]);
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Configuración ePayco', array(), 'Modules.Payco.Admin'),
                    'icon' => 'icon-envelope'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Titulo', array(), 'Modules.Payco.Admin'),
                        'name' => 'P_TITULO',
                        'required' => true,
                        'desc' => $this->trans('Titulo que el usuario vera durante el Checkout del Plugin', array(), 'Modules.Payco.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('P_CUST_ID_CLIENTE', array(), 'Modules.Payco.Admin'),
                        'name' => 'P_CUST_ID_CLIENTE',
                        'desc' => $this->trans(
                            'Id del cliente recibidor primario (App, Maketplace, Tienda, etc).',
                            array(),
                            'Modules.Payco.Admin'
                        ),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('P_KEY', array(), 'Modules.Payco.Admin'),
                        'name' => 'P_KEY',
                        'desc' => $this->trans('Llave para firmar la información enviada y recibida de ePayco', array(), 'Modules.Payco.Admin'),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('PUBLIC_KEY', array(), 'Modules.Payco.Admin'),
                        'name' => 'PUBLIC_KEY',
                        'desc' => $this->trans('LLave para autenticar y consumir los servicios de ePayco.', array(), 'Modules.Payco.Admin'),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('PRIVATE_KEY', array(), 'Modules.Payco.Admin'),
                        'name' => 'PRIVATE_KEY',
                        'desc' => $this->trans('LLave para autenticar y consumir los servicios de ePayco.', array(), 'Modules.Payco.Admin'),
                        'required' => true
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->trans('Habilitar modo pruebas', array(), 'Modules.Payment.Admin'),
                        'name' => "P_TEST_REQUEST",
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'P_TEST_REQUEST_TRUE',
                                'value' => true,
                                'label' => $this->trans('Si (Transacciones en pruebas)', array(), 'Modules.Payment.Admin'),
                            ),
                            array(
                                'id' => 'P_TEST_REQUEST_FALSE',
                                'value' => false,
                                'label' => $this->trans('No (Transacciones en producción)', array(), 'Modules.Payment.Admin'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->trans('Idioma del checkout', array(), 'Modules.Payment.Admin'),
                        'name' => "LENGUAJE",
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'LANGUAJE_ES',
                                'value' => true,
                                'label' => $this->trans('Español', array(), 'Modules.Payment.Admin'),
                            ),
                            array(
                                'id' => 'LANGUAJE_EN',
                                'value' => false,
                                'label' => $this->trans('Ingles', array(), 'Modules.Payment.Admin'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Página de Respuesta', array(), 'Modules.Payco.Admin'),
                        'name' => 'P_URL_RESPONSE',
                        'placeholder' => "http://tutienda.com/respuesta",
                        'desc' => $this->trans('Url de la tienda mostrada luego de finalizar el pago.', array(), 'Modules.Payco.Admin'),
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Página de Confirmación', array(), 'Modules.Payco.Admin'),
                        'name' => 'P_URL_CONFIRMATION',
                        'placeholder' => "http://tutienda.com/confirmacion",
                        'desc' => $this->trans('Url de Confirmación donde ePayco confirma el pago.', array(), 'Modules.Payco.Admin'),
                        'required' => true
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->trans('Tipo de checkout ePayco', array(), 'Modules.Payco.Admin'),
                        'name' => "P_TYPE_CHECKOUT",
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'onpage',
                                'value' => true,
                                'label' => $this->trans('OnPage Checkout (El usuario al pagar se queda en la tienda no hay redirección a ePayco)', array(), 'Modules.Payco.Admin'),
                            ),
                            array(
                                'id' => 'standart',
                                'value' => false,
                                'label' => $this->trans('Estandar Checkout (El usuario al pagar es redireccionado a la pasarela de ePayco)', array(), 'Modules.Payco.Admin'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->trans('Reducir el stock en transacciones pendientes', array(), 'Modules.Payco.Admin'),
                        'name' => "P_REDUCE_STOCK_PENDING",
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'P_REDUCE_STOCK_PENDING_TRUE',
                                'value' => true,
                                'label' => $this->trans('Si', array(), 'Modules.Payment.Admin'),
                            ),
                            array(
                                'id' => 'P_REDUCE_STOCK_PENDING_FALSE',
                                'value' => false,
                                'label' => $this->trans('No', array(), 'Modules.Payment.Admin'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Estado final Pedido', array(), 'Modules.Payco.Admin'),
                        'name' => 'P_STATE_END_TRANSACTION',
                        'desc' => $this->trans('Escoja el estado del pago que se aplicar al aceptar la trasacción.', array(), 'Modules.Payco.Admin'),
                        'required' => true,
                        'options' => array(
                            'id' => 'id',
                            'name' => 'name',
                            'default' => array(
                                'value' => '2',
                                'label' => $this->l('Pago aceptado')
                            ),
                            'query' => $order_states,

                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        return $fields_form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'P_TITULO' => Tools::getValue('P_TITULO', Configuration::get('P_TITULO')),
            'P_CUST_ID_CLIENTE' => Tools::getValue('P_CUST_ID_CLIENTE', Configuration::get('P_CUST_ID_CLIENTE')),
            'P_KEY' => Tools::getValue('P_KEY', Configuration::get('P_KEY')),
            'PUBLIC_KEY' => Tools::getValue('PUBLIC_KEY', Configuration::get('PUBLIC_KEY')),
            'PRIVATE_KEY' => Tools::getValue('PRIVATE_KEY', Configuration::get('PRIVATE_KEY')),
            'P_TEST_REQUEST' => Tools::getValue('P_TEST_REQUEST', Configuration::get('P_TEST_REQUEST')),
            'LENGUAJE' => Tools::getValue('LENGUAJE', Configuration::get('LENGUAJE')),
            'P_STATE_END_TRANSACTION' => Tools::getValue('P_STATE_END_TRANSACTION', Configuration::get('P_STATE_END_TRANSACTION')),
            'P_REDUCE_STOCK_PENDING' => Tools::getValue('P_REDUCE_STOCK_PENDING', Configuration::get('P_REDUCE_STOCK_PENDING')),
            'P_TYPE_CHECKOUT' => Tools::getValue('P_TYPE_CHECKOUT', Configuration::get('P_TYPE_CHECKOUT')),
            'P_URL_RESPONSE' => Tools::getValue('P_URL_RESPONSE', Configuration::get('P_URL_RESPONSE')),
            'P_URL_CONFIRMATION' => Tools::getValue('P_URL_CONFIRMATION', Configuration::get('P_URL_CONFIRMATION'))
        );
    }

    private function postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('P_CUST_ID_CLIENTE'))
                $this->_postErrors[] = $this->l('\'P_CUST_ID_CLIENTE\' Campo Requerido.');
            if (!Tools::getValue('P_KEY'))
                $this->_postErrors[] = $this->l('\'P_KEY\' Campo Requerido.');
            if (!Tools::getValue('PUBLIC_KEY'))
                $this->_postErrors[] = $this->l('\'PUBLIC_KEY\' Campo Requerido.');
            if (!Tools::getValue('PRIVATE_KEY'))
                $this->_postErrors[] = $this->l('\'PRIVATE_KEY\' Campo Requerido.');
        }
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (Tools::getValue('P_URL_RESPONSE') == "") {
                $p_url_response = Context::getContext()->link->getModuleLink('payco', 'response');
            } else {
                $p_url_response = Tools::getValue('P_URL_RESPONSE');
            }
            if (Tools::getValue('P_URL_CONFIRMATION') == "") {
                $p_url_confirmation = Context::getContext()->link->getModuleLink('payco', 'confirmation');
            } else {
                $p_url_confirmation = Tools::getValue('P_URL_CONFIRMATION');
            }
            if (Tools::getValue('P_TITULO') == "") {
                $p_titulo = "Checkout ePayco, Tarjetas de Crédito, Débito y  Efectivo";
            } else {
                $p_titulo = Tools::getValue('P_TITULO');
            }
            Configuration::updateValue('P_CUST_ID_CLIENTE', Tools::getValue('P_CUST_ID_CLIENTE'));
            Configuration::updateValue('P_KEY', Tools::getValue('P_KEY'));
            Configuration::updateValue('PUBLIC_KEY', Tools::getValue('PUBLIC_KEY'));
            Configuration::updateValue('PRIVATE_KEY', Tools::getValue('PRIVATE_KEY'));
            Configuration::updateValue('P_TEST_REQUEST', Tools::getValue('P_TEST_REQUEST'));
            Configuration::updateValue('LENGUAJE', Tools::getValue('LENGUAJE'));
            Configuration::updateValue('P_TITULO', $p_titulo);
            Configuration::updateValue('P_STATE_END_TRANSACTION', Tools::getValue('P_STATE_END_TRANSACTION'));
            Configuration::updateValue('P_REDUCE_STOCK_PENDING', Tools::getValue('P_REDUCE_STOCK_PENDING'));
            Configuration::updateValue('P_TYPE_CHECKOUT', Tools::getValue('P_TYPE_CHECKOUT'));
            Configuration::updateValue('P_URL_RESPONSE', $p_url_response);
            Configuration::updateValue('P_URL_CONFIRMATION', $p_url_confirmation);
            //CreditCard_OrderState::updateStates(intval(Tools::getValue('id_os_initial')), Tools::getValue('id_os_deleteon'));
            $this->_html .= '<div class="bootstrap"><div class="alert alert-success">' . $this->l('Cambios Aplicados Exitosamente') . '</div></div>';
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/back.css');
    }

    /**
     * This method is used to render the payment button,
     * Take care if the button should be displayed or not.
     */
    public function hookPayment($params)
    {
        if (!$this->active)
            return false;

        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int)$currency_id);

        if (in_array($currency->iso_code, $this->limited_currencies) == false)
            return false;

        $this->smarty->assign('module_dir', $this->_path);

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $this->context->smarty->assign(array(
            "titulo" => $this->p_titulo,

            "logo_url" => _EPAYCO_MULTIMEDIA_URL_ . '/plugins-sdks/paymentLogo.svg',

        ));
        $modalOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $modalOption->setCallToActionText($this->l('Pagar con ePayco'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setAdditionalInformation($this->context->smarty->fetch('module:payco/views/templates/hook/payment_onpage.tpl'));
        $payment_options = [
            $modalOption,
        ];

        return $payment_options;
    }


    /**
     * This hook is used to display the order confirmation page.
     */
    public function hookPaymentReturn($params)
    {
        if ($this->active == false)
            return;

        if (version_compare(_PS_VERSION_, '1.7.0.0 ', '<')) {
            $order = $params['objOrder'];
            $value = $params['total_to_pay'];
            $currence = $params['currencyObj'];
        } else {
            $order = $params['order'];
            $value = $params['order']->getOrdersTotalPaid();
            $currence = new Currency($params['order']->id_currency);
        }

        if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR')) {
            $this->smarty->assign('status', 'ok');
        }


        $extra1 = $order->id_cart;
        $extra2 = $order->id;
        $emailComprador = $this->context->customer->email;
        $valorBaseDevolucion = $order->total_paid_tax_excl;
        $iva = $value - $valorBaseDevolucion;
        $cart = $this->context->cart;

        $iso = 'CO';
        if ($iva == 0) $valorBaseDevolucion = 0;

        $currency = $this->getCurrency();
        $idcurrency = $order->id_currency;
        foreach ($currency as $mon) {
            if ($idcurrency == $mon['id_currency']) $currency = $mon['iso_code'];
        }

        if ($currency == '') {
            $currency = 'COP';
        }
        $refVenta = $order->reference;
        $state = $order->getCurrentState();

        if ($state) {

            $p_signature = md5(trim($this->p_cust_id_cliente) . '^' . trim($this->p_key) . '^' . $refVenta . '^' . $value . '^' . $currency);
            $addressdelivery = new Address((int)($cart->id_address_delivery));

            if ($this->p_test_request == 1) {
                $test = "true";
            } else {
                $test = "false";
            }

            if ($this->lenguaje == 1) {
                $lenguaje = "es";
            } else {
                $lenguaje = "en";
            }

            if ($this->p_type_checkout == 1) {
                $external = "false";
            } else {
                $external = "true";
            }

            $descripcion = '';
            $productos = Db::getInstance()->executeS('
			SELECT id_product FROM `' . _DB_PREFIX_ . 'cart_product`
			WHERE `id_cart` = ' . (int) $extra1);

            foreach ($productos as $producto) {
                // Your product id
                $id_product = (int)$producto['id_product'];
                // Language id
                $lang_id = (int) Configuration::get('PS_LANG_DEFAULT');
                // Load product object
                $product = new Product($id_product, false, $lang_id);
                // Validate product object
                if (Validate::isLoadedObject($product)) {
                    // Get product name
                    $descripcion = $descripcion . $product->name . ', ';
                }
            }
            $descripcion = substr($descripcion, 0, -2);

            if (!EpaycoOrder::ifExist($order->id)) {
                EpaycoOrder::create($order->id, 1);
            }

            $p_url_response = !empty($this->p_url_response) ? $this->p_url_response : Context::getContext()->link->getModuleLink('payco', 'response');
            $p_url_confirmation = !empty($this->p_url_confirmation) ? $this->p_url_confirmation : Context::getContext()->link->getModuleLink('payco', 'confirmation');

            $lang = $this->context->language->language_code;

            if ($lang == "es") {
                // Si el idioma es español, asigna el botón en español
                $url_button = _EPAYCO_MULTIMEDIA_URL_ . '/plugins-sdks/Boton-color-espanol.png';
            } else {
                // Para cualquier otro idioma, asigna el botón en inglés
                $url_button = _EPAYCO_MULTIMEDIA_URL_ . '/plugins-sdks/Boton-color-Ingles.png';
                $lang = "en"; // Asegúrate de que el idioma sea 'en' en caso de no ser español
            }


            $myIp = $this->getCustomerIp();
            $this->smarty->assign(
                array(
                    'this_path_bw' => $this->_path,
                    'p_signature' => $p_signature,
                    'total_to_pay' => $value,
                    'status' => 'ok',
                    'refVenta' => $refVenta,
                    'custemail' => $emailComprador,
                    'extra1' => $extra1,
                    'extra2' => $extra2,
                    'total' => $value,
                    'currency' => $currency,
                    'iso' => $iso,
                    'iva' => $iva,
                    'baseDevolucionIva' => $valorBaseDevolucion,
                    'merchantid' => trim($this->p_cust_id_cliente),
                    'external' => $external,
                    'merchanttest' => $test,
                    'p_key' => trim($this->p_key),
                    'public_key' => trim($this->public_key),
                    'private_key' => trim($this->private_key),
                    'custip' => $_SERVER['REMOTE_ADDR'],
                    'custname' => $this->context->customer->firstname . " " . $this->context->customer->lastname,
                    'p_url_response' => $p_url_response,
                    'p_url_confirmation' => $p_url_confirmation,
                    'p_billing_email' => $this->context->customer->email,
                    'p_billing_name' => $this->context->customer->firstname,
                    'p_billing_last_name' => $this->context->customer->lastname,
                    'p_billing_address' => $addressdelivery->address1 . " " . $addressdelivery->address2,
                    'p_billing_city' => $addressdelivery->city,
                    'p_billing_country' => $addressdelivery->id_state,
                    'p_billing_phone' => "",
                    'lang' => $lenguaje,
                    'descripcion' => $descripcion,
                    'url_button' => $url_button,
                    'ip' => $myIp
                )
            );
        } else {
            $this->smarty->assign('status', 'failed');
        }
        $this->context->controller->addCSS($this->_path . '/views/css/back.css');
        //redirige al checkout
        //luego al controlador response.php
        return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');
    }


    private function is_blank($var)
    {
        return isset($var) || $var == '0' ? ($var == "" ? true : false) : false;
    }

    private function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }

    private function getCustomerIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function PaymentReturnOnpage()
    {

        $ref_payco = "";
        $url = "";
        $confirmation = false;
        $x_ref_payco = "";

        foreach ($_REQUEST as $value) {
            if (preg_match("/ref_payco/", $value)) {
                $arr_refpayco = explode("=", $value);
                $ref_payco = $arr_refpayco[1];
            }
        }


        if (isset($_REQUEST["?ref_payco"]) != "" || isset($_REQUEST["ref_payco"]) || $ref_payco) {

            if (isset($_REQUEST["?ref_payco"])) {
                $ref_payco = $_REQUEST["?ref_payco"];
            }

            if (isset($_REQUEST["ref_payco"])) {
                $ref_payco = $_REQUEST["ref_payco"];
            }

            $url = 'https://secure.epayco.co/validation/v1/reference/' . $ref_payco;

        }


        if ($ref_payco != "" and $url != "") {
            $responseData = $this->PostCurl($url, false, $this->StreamContext());
            $jsonData = @json_decode($responseData, true);
            $data = $jsonData['data'];

            $data["ref_payco"] = $ref_payco;
            $data["url"] = $url;

            $this->Acentarpago($data["x_extra1"], $data["x_cod_response"], $data["x_ref_payco"], $data["x_transaction_id"], $data["x_amount"], $data["x_currency_code"], $data["x_signature"], $confirmation, $data["x_test_request"], $data["x_cod_transaction_state"], $ref_payco, $data["x_approval_code"], $data["x_franchise"]);
            $this->context->smarty->assign($data);
        }
    }

    public function PaymentSuccess($extra1, $response, $referencia, $transid, $amount, $currency, $signature, $confirmation, $textMode, $x_cod_transaction_state, $ref_payco, $x_approval_code, $x_franchise)
    {
        $this->Acentarpago($extra1, $response, $referencia, $transid, $amount, $currency, $signature, $confirmation, $textMode, $x_cod_transaction_state, $ref_payco, $x_approval_code, $x_franchise);
    }


    private function Acentarpago($extra1, $response, $referencia, $transid, $amount, $currency, $signature, $confirmation, $textMode, $x_cod_transaction_state, $old_ref_payco, $x_approval_code, $x_franchise)
    {
        $config = Configuration::getMultiple(array('P_CUST_ID_CLIENTE', 'P_KEY', 'PUBLIC_KEY', 'P_TEST_REQUEST', 'P_STATE_END_TRANSACTION'));
        $x_cust_id_cliente = trim($config['P_CUST_ID_CLIENTE']);
        $x_key = trim($config['P_KEY']);
        $idorder = $extra1;
        $x_cod_response = (int)$response;
        $x_signature = hash(
            'sha256',
            $x_cust_id_cliente . '^'
                . $x_key . '^'
                . $referencia . '^'
                . $transid . '^'
                . $amount . '^'
                . $currency
        );

        $payment = false;
        $state = 'PAYCO_OS_REJECTED';
        if ($x_cod_response == 4)
            $state = 'PAYCO_OS_FAILED';
        else if ($x_cod_response == 2)
            $state = 'PAYCO_OS_REJECTED';
        else if ($x_cod_response == 3) {
            $state = 'PAYCO_OS_PENDING';
            $statePending = $state;
        } else if ($x_cod_response == 9)
            $state = 'PAYCO_OS_EXPIRED';
        else if ($x_cod_response == 10)
            $state = 'PAYCO_OS_ABANDONED';
        else if ($x_cod_response == 11)
            $state = 'PAYCO_OS_CANCELED';
        else if ($x_cod_response == 1) {
            $state = 'PS_OS_PAYMENT';
            $payment = true;
        }

        $order_id = Order::getByCartId((int)$idorder);
        $order = Order::getByCartId((int)$idorder);
        $keepOn = false;
        if ($this->p_test_request == 1) {
            $test = "yes";
        } else {
            $test = "no";
        }
        $isTestTransaction = $textMode == 'TRUE' ? "yes" : "no";
        $orderAmount = floatval($order->getOrdersTotalPaid());
        if ($orderAmount == floatval($amount)) {

            if ($isTestTransaction == "yes") {
                $validation = true;
            }


            if ($isTestTransaction == "no") {
                if ($x_approval_code != "000000" && $x_cod_response == 1) {
                    $validation = true;
                } else {
                    if ($x_cod_response != 1) {
                        $validation = true;
                    } else {
                        $validation = false;
                    }
                }
            }
        }

        $orderStatusPre = Db::getInstance()->executeS('
        SELECT name FROM `' . _DB_PREFIX_ . 'order_state_lang`
        WHERE `id_order_state` = ' . (int)$order->current_state);
        $orderStatusPreName = $orderStatusPre[0]['name'];

        if (
            $orderStatusPreName == "ePayco Pago Rechazado" ||
            $orderStatusPreName == "ePayco Pago Cancelado" ||
            $orderStatusPreName == "ePayco Pago Abandonado" ||
            $orderStatusPreName == "ePayco Pago Expirado"  ||
            $orderStatusPreName == "ePayco Pago Fallido"
        ) {
            $validacionOrderName = false;
        } else {
            $validacionOrderName = true;
        }

        if (!isset($validation)) {
            $validation = false;  // O algún valor por defecto
        }

        if ($x_signature == $signature && $validation) {
            $current_state = $order->current_state;
            if (!EpaycoOrder::ifStockDiscount($order->id)) {
                EpaycoOrder::updateStockDiscount($order->id, 1);
            }

            if ($current_state != Configuration::get($state)) {
                if ($confirmation && !$payment && $x_cod_response != 3 && EpaycoOrder::ifStockDiscount($order->id)) {
                    if (!$validacionOrderName) {
                        $this->RestoreStock($order, '+');
                        $history = new OrderHistory();
                        $history->id_order = (int)$order->id;
                        $history->changeIdOrderState((int)Configuration::get($state), $order, true);
                    }
                } else {
                    if ($confirmation && $x_cod_response == 3 && EpaycoOrder::ifStockDiscount($order->id)) {
                        if (!$validacionOrderName) {
                            $this->RestoreStock($order, '-');
                        }
                    }
                }

                $history = new OrderHistory();
                $history->id_order = (int)$order->id;

                if ($payment && $validacionOrderName) {
                    // Obtener el nombre del estado final de la transacción


                    $orderStatus = Db::getInstance()->executeS(
                        '
                    SELECT name FROM `' . _DB_PREFIX_ . 'order_state_lang`
                    WHERE `id_order_state` = ' . (int)$config['P_STATE_END_TRANSACTION'] . ' AND id_lang = ' . (int)$order->id_lang
                    );

                    if (!empty($orderStatus)) {
                        $orderStatusName = $orderStatus[0]['name'];

                        // Validar si está configurado el estado de fin de transacción
                        if (isset($this->p_state_end_transaction) && (int)$this->p_state_end_transaction > 0) {
                            // Asignar estado configurado a la orden
                            $orderHistory = new OrderHistory();
                            $orderHistory->id_order = (int)$order->id;
                            $orderHistory->changeIdOrderState((int)$this->p_state_end_transaction, (int)$order->id);
                            $orderHistory->add();
                        } else {
                            // Asignar un estado predeterminado (como "Pago aceptado")
                            $default_order_state = 2; // ID del estado "Pago aceptado", verifica que exista en tu instalación
                            $orderHistory = new OrderHistory();
                            $orderHistory->id_order = (int)$order->id;
                            $orderHistory->changeIdOrderState($default_order_state, (int)$order->id);
                            $orderHistory->add();
                        }

                        // Obtener el ID del estado basado en el nombre
                        $orderStatusEndId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                            'SELECT id_order_state FROM `' . _DB_PREFIX_ . 'order_state_lang`
                        WHERE `name` = "' . pSQL($orderStatusName) . '" AND id_lang = ' . (int)$order->id_lang
                        );

                        if ($orderStatusEndId) {
                            // Cambiar el estado de la orden en el historial
                            $history->changeIdOrderState((int)$orderStatusEndId, $order, true);
                            echo "Cambiado estado de orden en el historial";
                            die;
                        } else {
                            throw new PrestaShopException('No se encontró un estado de orden con el nombre: ' . $orderStatusName);
                        }
                    } else {
                        throw new PrestaShopException('El nombre del estado de la orden no está definido.');
                    }
                } else {
                    if (in_array($x_cod_response, [2, 4, 6, 9, 10, 11])) {
                        if ($current_state != Configuration::get($state)) {
                            // Restaurar stock según las reglas de las franquicias y respuestas
                            if (!$confirmation || ($x_franchise == "PSE" || !in_array($x_franchise, ["VS", "CR", "AM", "DC", "MC", "PSE"]))) {
                                $this->RestoreStock($order, '+');
                            }
                            if (trim($x_cod_response) == 10) {
                                $this->RestoreStock($order, '-');
                            }
                            if ($orderStatusPreName == "ePayco Esperando Pago") {
                                $history->changeIdOrderState((int)Configuration::get($state), $order, true);
                                $this->RestoreStock($order, '+');
                            }
                        }
                    }

                    $history->changeIdOrderState((int)Configuration::get($state), $order, true);

                    if (!$validacionOrderName) {
                        if ($orderStatusPreName != "ePayco Pago Rechazado" && $orderStatusPreName != "ePayco Pago Cancelado" && $orderStatusPreName != "ePayco Pago Fallido") {
                            $keepOn = true;
                        }

                        if ($keepOn && $x_cod_response == 1) {
                            // Obtener el nombre del estado final de la transacción
                            $orderStatus = Db::getInstance()->executeS(
                                '
                            SELECT name FROM `' . _DB_PREFIX_ . 'order_state_lang`
                            WHERE `id_order_state` = ' . (int)$config['P_STATE_END_TRANSACTION']
                            );
                            $orderStatusName = $orderStatus[0]['name'];

                            // Obtener el ID del estado basado en el nombre
                            $orderStatusEndId = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                                'SELECT id_order_state FROM `' . _DB_PREFIX_ . 'order_state_lang`
                            WHERE `name` = "' . pSQL($orderStatusName) . '" AND id_lang = ' . (int)$order->id_lang
                            );

                            if ($orderStatusEndId) {
                                // Cambiar el estado de la orden en el historial
                                $history->changeIdOrderState((int)$orderStatusEndId, $order, true);
                            } else {
                                throw new PrestaShopException('No se encontró un estado de orden con el nombre: ' . $orderStatusName);
                            }
                        }
                    } else {
                        echo " Confirmación procesada";
                        die;
                    }
                }
            }
        }
    }

    private function RestoreStock($orderId, $operation)
    {
        // Obtener la orden con base en el ID proporcionado
        $order = new Order($orderId);

        // Verificamos si la orden contiene productos antes de actualizar el stock
        if ($order && !empty($order->getProductsDetail())) {
            foreach ($order->getProductsDetail() as $product) {
                // Actualizamos la cantidad de stock disponible
                StockAvailable::updateQuantity(
                    (int)$product['product_id'],                  // ID del producto
                    (int)$product['product_attribute_id'],        // ID del atributo del producto (si aplica)
                    $operation . (int)$product['product_quantity'], // Operación (+ o -) seguido de la cantidad
                    (int)$order->id_shop                          // ID de la tienda (shop)
                );
            }
        }
    }

    private function PostCurl($url)
    {
        if (function_exists('curl_init')) {
            // Inicializamos cURL
            $ch = curl_init();
            $timeout = 5;
            $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

            // Configuraciones de cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // Desactivar verificación de certificado SSL
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    // Desactivar verificación de host SSL
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);   // Establecer el agente de usuario
            curl_setopt($ch, CURLOPT_HEADER, 0);                // No incluir encabezados en la salida
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);        // Seguir redirecciones
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // Devolver la respuesta como string
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // Tiempo de conexión máximo
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);        // Tiempo de espera máximo
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);            // Máximo de redirecciones permitidas

            // Ejecutamos la petición y obtenemos los datos
            $data = curl_exec($ch);
            curl_close($ch); // Cerramos la conexión cURL

            return $data; // Devolvemos los datos obtenidos
        } else {
            // Si cURL no está disponible, usamos file_get_contents
            $data = @Tools::file_get_contents($url);
            return $data;
        }
    }

    private function StreamContext()
    {
        // Creamos un contexto de flujo HTTP con método POST y configuraciones
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'protocol_version' => 1.1,
                'timeout' => 10,           // Tiempo de espera máximo
                'ignore_errors' => true     // Ignorar errores HTTP
            )
        ));

        return $context; // Devolvemos el contexto creado
    }
}
