<?php

/**
 * 2007-2024 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    MercadoPago
 * @copyright Copyright (c) MercadoPago [http://www.mercadopago.com]
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of MercadoPago
 */


//namespace Epayco\Prestashop;
define('EP_VERSION', '1.0.0');
define('EP_ROOT_URL', dirname(__FILE__));

if (!defined('_PS_VERSION_')) {
    exit;
}

include(_PS_MODULE_DIR_ . 'payco/lib/EpaycoOrder.php');
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_Order.php');
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_OrderState.php');
require_once EP_ROOT_URL . '/vendor/autoload.php';


class Payco extends PaymentModule
{
    public $epayco;
    public $name;
    public $tab;
    public $author;
    public $need_instance;
    public $bootstrap;
    public $version;
    public $displayName;
    public $description;
    public $confirmUninstall;
    public $module_key;
    public $ps_version;
    public $assets_ext_min;
    public $path;
    public $_context;

    const PRESTA16 = "1.6";
    const PRESTA17 = "1.7";

    public static $form_alert;
    public static $form_message;

    public $standardCheckout;
    public $pseCheckout;
    public $creditcardCheckout;
    public $ticketCheckout;

    public function __construct()
    {
        $this->loadFiles();
        $this->name = 'payco';
        $this->tab = 'payments_gateways';
        $this->author = 'ePayco';
        $this->need_instance = 1;
        $this->bootstrap = true;

        //Always update, because prestashop doesn't accept version coming from another variable (EP_VERSION)
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        if (!defined('_EPAYCO_MULTIMEDIA_URL_')) {
            define('_EPAYCO_MULTIMEDIA_URL_', 'https://multimedia.epayco.co');
        }

        parent::__construct();

        $this->displayName = $this->l('ePayco');
        $this->description = $this->l('Facilita los pagos en tu tienda online con el plugin de ePayco. Con esta integración, podrás ofrecer a tus clientes una experiencia de pago rápida, segura y sin fricciones.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the module?');
        $this->module_key = '4380f33bbe84e7899aacb';
        $this->ps_version = _PS_VERSION_;
        $this->assets_ext_min = !_PS_MODE_DEV_ ? '.min' : '';
        $this->path = $this->_path;
        $this->_context = $this->context;
        $this->standardCheckout = new StandardCheckoutEpayco($this->name, $this->_context, $this->path);
        $this->pseCheckout = new PseCheckoutEpayco($this->_context);
        $this->creditcardCheckout = new CreditcardEpaycoCheckout($this->name, $this->_context, $this->path);
        $this->ticketCheckout = new TicketEpaycoCheckout($this->name, $this->_context, $this->path);
        $this->daviplataCheckout = new DaviplataEpaycoCheckout($this->name, $this->_context, $this->path);
    }

    /**
     * Load files
     *
     * @return void
     */
    public function loadFiles()
    {
        include_once EP_ROOT_URL . '/includes/module/checkouts/StandardCheckoutEpayco.php';
        include_once EP_ROOT_URL . '/includes/module/checkouts/DaviplataEpaycoCheckout.php';
        include_once EP_ROOT_URL . '/includes/module/checkouts/PseCheckoutEpayco.php';
        include_once EP_ROOT_URL . '/includes/module/checkouts/CreditcardEpaycoCheckout.php';
        include_once EP_ROOT_URL . '/includes/module/checkouts/TicketEpaycoCheckout.php';
    }

    /**
     * Install the module
     *
     * @return bool
     * @throws PrestaShopException
     */
    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension ') .
                $this->l('on your server to install this module.');
            return false;
        }

        //Set up our currencies and issuers
        //CreditCard_OrderState::remove();
        CreditCard_OrderState::setup();
        //CreditCard_Issuer::setup();
        CreditCard_Order::setup();
        //EpaycoOrder::remove();
        EpaycoOrder::setup();
        //install hooks and dependencies
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('paymentOptions') &&
            $this->registerHook('orderConfirmation') &&
            $this->registerHook('displayWrapperTop') &&
            $this->registerHook('displayTopColumn') &&
            $this->registerHook('actionOrderSlipAdd') &&
            $this->registerHook('actionOrderStatusUpdate') &&
            $this->registerHook('displayAdminOrder');
    }

    /**
     * Uninstall the module
     *
     * @return bool
     */
    public function uninstall()
    {
        //CreditCard_Order::remove();
        // CreditCard_OrderState::remove();
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     *
     * @return mixed
     * @throws Exception
     */
    public function getContent()
    {
        //add css to configuration page
        $this->context->controller->addCSS($this->_path . 'views/css/admin/ep-admin-settings.css');
        //add js to configuration page
        $this->context->controller->addJS($this->_path . 'views/js/epayco.js');
        $this->context->controller->addJS($this->_path . 'views/js/admin/ep-admin-settings.min.js');

        $this->context->smarty->assign('module_dir', $this->_path);

        $this->loadSettings();

        //return checkout forms
        $credentials = new CredentialsSettings();
        $credentials_form = $this->renderForm($credentials->submit, $credentials->values, $credentials->form);
        $standard = new StandardSettings();
        $standard_form = $this->renderForm($standard->submit, $standard->values, $standard->form);
        $pse = new PseSettings();
        $pse_form = $this->renderForm($pse->submit, $pse->values, $pse->form);
        $creditcard = new CreditcardSettings();
        $creditcard_form = $this->renderForm($creditcard->submit, $creditcard->values, $creditcard->form);
        $ticket = new TicketSettings();
        $ticket_form = $this->renderForm($ticket->submit, $ticket->values, $ticket->form);
        $daviplata = new DaviplataSettings();
        $daviplata_form = $this->renderForm($daviplata->submit, $daviplata->values, $daviplata->form);
        //variables for admin configuration
        $public_key = Configuration::get('EPAYCO_PUBLIC_KEY');
        $private_key = Configuration::get('EPAYCO_PRIVATE_KEY');
        $p_key = Configuration::get('EPAYCO_P_KEY');
        $p_cust_id = Configuration::get('EPAYCO_P_CUST_ID_CLIENTE');

        $output = $this->context->smarty->assign(
            array(
                //module requirements
                'alert' => self::$form_alert,
                'message' => self::$form_message,
                'ep_version' => EP_VERSION,
                'url_base' => __PS_BASE_URI__,
                'application' => Configuration::get('EPAYCO_APPLICATION_ID'),
                'standard_test' => Configuration::get('EPAYCO_STANDARD'),
                'sandbox_status' => Configuration::get('EPAYCO_PROD_STATUS'),

                //credentials
                'public_key' => $public_key,
                'private_key' => $private_key,
                'p_key' => $p_key,
                'p_cust_id' => $p_cust_id,

                //forms
                'credentials' => $credentials_form,
                'standard_form' => $standard_form,
                'creditcard_form' => $creditcard_form,
                'pse_form' => $pse_form,
                'ticket_form' => $ticket_form,
                'daviplata_form' => $daviplata_form
            )
        )->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output;
    }


    /**
     * Load settings
     *
     * @return void
     */
    public function loadSettings()
    {
        include_once EP_ROOT_URL . '/includes/module/settings/CreditcardSettings.php';
        include_once EP_ROOT_URL . '/includes/module/settings/StandardSettings.php';
        include_once EP_ROOT_URL . '/includes/module/settings/DaviplataSettings.php';
        include_once EP_ROOT_URL . '/includes/module/settings/PseSettings.php';
        include_once EP_ROOT_URL . '/includes/module/settings/CredentialsSettings.php';
        include_once EP_ROOT_URL . '/includes/module/settings/TicketSettings.php';
    }

    /**
     * Render forms
     *
     * @param  $submit
     * @param  $values
     * @param  $form
     * @return string
     */
    protected function renderForm($submit, $values, $form)
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->submit_action = $submit;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        // Asignar los valores para los inputs
        $helper->tpl_vars = array(
            'fields_value' => $values,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'class' => 'custom-form-class',
        );

        /*foreach ($form['input'] as &$input) {
            if (!isset($input['class'])) {
                $input['class'] = 'custom-input-class';
            }
        }*/

        return $helper->generateForm(array($form));
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO
     *
     * @return void
     */
    public function hookHeader()
    {
        Media::addJsDef([
            'ePaycoPublicKey' => Configuration::get('EPAYCO_PUBLIC_KEY'),
            'lenguaje' => $this->context->language->iso_code
        ]);
        $this->context->controller->addCSS($this->_path . 'views/css/checkouts/ep-plugins-components.css');
        $this->context->controller->addJS($this->_path . 'views/js/jquery-1.11.0.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/crypto-v3.1.2.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addJS($this->_path . 'views/js/checkouts/ep-plugins-components.js');
        //$this->context->controller->addJS("https://cms.epayco.io/js/library.js");
    }

    /**
     * Show payment options in version 1.6
     *
     * @param  $params
     * @return array|string|mixed
     */
    public function hookPayment($params)
    {
        return $this->loadPayments($params, self::PRESTA16);
    }

    /**
     * Show payment options in version 1.7
     *
     * @param  $params
     * @return array|string|void
     */
    public function hookPaymentOptions($params)
    {
        return $this->loadPayments($params, self::PRESTA17);
    }

    /**
     * @param $params
     * @param $version
     * @return array|string|void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function loadPayments($params, $version)
    {
        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }
        $cart = $this->context->cart;
        $paymentOptions = array();

        $version == self::PRESTA16 ? $this->smarty->assign('module_dir', $this->_path) : null;

        $checkouts = array(
            'EPAYCO_STANDARD_CHECKOUT' => 'getStandardCheckout',
            'EPAYCO_CREDITCARD_CHECKOUT' => 'getCreditcardCheckout',
            'EPAYCO_DAVIPLATA_CHECKOUT' => 'getDaviplataCheckout',
            'EPAYCO_PSE_CHECKOUT' => 'getPseCheckout',
            'EPAYCO_TICKET_CHECKOUT' => 'getTicketCheckout',
        );

        foreach ($checkouts as $checkout => $method) {
            if ($this->isActiveCheckout($checkout)) {
                $paymentOptions[] = $this->{$method}($cart, $version);
            } else {
                $this->disableCheckout($checkout);
            }
        }

        return $version == self::PRESTA16 ? implode('', $paymentOptions) : $paymentOptions;
    }

    /**
     * @param $checkout
     * @return bool
     */
    public function isActiveCheckout($checkout)
    {
        return (Configuration::get($checkout) == true);
    }

    /**
     * @param $checkout
     * @return void
     */
    public function disableCheckout($checkout)
    {
        Configuration::updateValue($checkout, false);
    }

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */
    public function getStandardCheckout($cart, $version)
    {
        if ($version == self::PRESTA16) {
            $frontInformations = $this->standardCheckout->getStandardCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/standard.tpl');
        } else {
            // $title = 'Checkout ePayco';
            $this->context->smarty->assign(array(
                "logo_url" => _EPAYCO_MULTIMEDIA_URL_ . '/plugins-sdks/paymentLogo.svg',

            ));
            $frontInformations = $this->standardCheckout->getStandardCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/standard.tpl');
            $standardCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $standardCheckout->setLogo(_MODULE_DIR_ . 'payco/views/img/checkout/checkout.png')
                //  ->setCallToActionText($this->l($title))
                ->setForm($infoTemplate);
                // ->setAdditionalInformation($this->context->smarty->fetch('module:payco/views/templates/hook/seven/standard.tpl'));

            return $standardCheckout;
        }
    }

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */


    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */
    public function getCreditcardCheckout($cart, $version)
    {
        if ($version == self::PRESTA16) {
            $frontInformations = $this->creditcardCheckout->getCreditcardCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/creditcard.tpl');
        } else {
            $title = 'Tarjeta de crédito y/o débito';
            $frontInformations = $this->creditcardCheckout->getCreditcardCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/creditcard.tpl');
            $creditcardCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $creditcardCheckout->setLogo(_MODULE_DIR_ . 'payco/views/img/checkout/CreditCart.png')
                // ->setCallToActionText($this->l($title))
                ->setForm($infoTemplate);

            return $creditcardCheckout;
        }
    }

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */
    public function getDaviplataCheckout($cart, $version)
    {
        if ($version == self::PRESTA16) {
            $frontInformations = $this->daviplataCheckout->getDaviplataCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/daviplata.tpl');
        } else {
            $title = 'Daviplata';
            $frontInformations = $this->daviplataCheckout->getDaviplataCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/daviplata.tpl');
            $daviplataCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $daviplataCheckout->setLogo(_MODULE_DIR_ . 'payco/views/img/checkout/daviplata.png')
                // ->setCallToActionText($this->l($title))
                ->setForm($infoTemplate);

            return $daviplataCheckout;
        }
    }

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */
    public function getPseCheckout($cart, $version)
    {
        $pluginInfos = array(
            'redirect_link' => $this->context->link->getModuleLink($this->name, PseCheckoutEpayco::PAYMENT_METHOD_NAME),
            'module_dir' => $this->path,
        );
        $title = 'PSE';
        $templateData = $this->pseCheckout->getPseTemplateData($pluginInfos);
        $infoTemplate = $this->context->smarty->assign($templateData)
            ->fetch('module:payco/views/templates/hook/seven/pse.tpl');
        $psePaymentOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $psePaymentOption->setLogo(_MODULE_DIR_ . 'payco/views/img/checkout/pse.png')
            // ->setCallToActionText($this->l($title) )
            ->setForm($infoTemplate);

        return $psePaymentOption;
    }

    public function getTicketCheckout($cart, $version)
    {
        if ($version == self::PRESTA16) {
            $frontInformations = $this->ticketCheckout->getTicketCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/ticket.tpl');
        } else {
            $title = 'Efectivo';
            $frontInformations = $this->ticketCheckout->getTicketCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/ticket.tpl');
            $ticketCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $ticketCheckout->setLogo(_MODULE_DIR_ . 'payco/views/img/checkout/Money.png')
                // ->setCallToActionText($this->l($title))
                ->setForm($infoTemplate);

            return $ticketCheckout;
        }
    }



    /**
     * Check currency
     *
     * @param  mixed $cart
     * @return boolean
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * This hook is used to display the order confirmation page.
     *
     * @param  mixed $params
     * @return string
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }
        $epaycoData = $this->context->cookie->__get('redirect_epayco_message');
        $payment = json_decode($epaycoData);

        // ref_payco comes either from the URL param or from the cookie payload
        $ref_payco = Tools::getValue('ref_payco');
        if (!$ref_payco && isset($payment->refPayco)) {
            $ref_payco = $payment->refPayco;
        }

        // Persist ref_payco in ps_order_payment.transaction_id so it is
        // available later in hookActionOrderSlipAdd for refund processing.
        if ($ref_payco) {
            $order = isset($params['order']) ? $params['order'] : (isset($params['objOrder']) ? $params['objOrder'] : null);
            if ($order) {
                Db::getInstance()->update(
                    'order_payment',
                    ['transaction_id' => pSQL($ref_payco)],
                    'order_reference = \'' . pSQL($order->reference) . '\''
                );
            }
        }

        return $this->getPaymentReturn($payment);
    }

    /**
     * Get template of payment confirmation
     *
     * @param  mixed $payment
     * @return string
     */
    public function getPaymentReturn($payment)
    {
        $this->context->smarty->assign(
            array(
                'status' => $payment->status,
                'type' => $payment->type,
                'refPayco' => $payment->refPayco,
                'factura' => $payment->factura,
                'descripcion' => $payment->descripcion,
                'valor' => floatval($payment->valor),
                'iva' => $payment->iva,
                'ip' => $payment->ip,
                'estado' => $payment->estado,
                'respuesta' => $payment->respuesta,
                'fecha' => $payment->fecha,
                'descuento' =>  floatval($payment->descuento),
                'autorizacion' =>  $payment->autorizacion,
                'franquicia' => $payment->franquicia,
                'extra1' => $payment->extra1,
                'baseurl' => $payment->baseurl
            )
        );

        $versions = array(
            self::PRESTA16 => 'six',
            self::PRESTA17 => 'seven',
        );

        return $this->display(__FILE__, 'views/templates/hook/' . $versions[$this->getVersionPs()] . '/payment_return.tpl');
    }

    /**
     * @return string
     */
    public function getVersionPs()
    {
        if ($this->ps_version >= 1.7) {
            return self::PRESTA17;
        } else {
            return self::PRESTA16;
        }
    }

    /**
     * This hook is used to display in order confirmation page.
     *
     * @param  mixed $params
     * @return string
     */
    public function hookDisplayOrderConfirmation($params)
    {
        $order = isset($params['order']) ? $params['order'] : $params['objOrder'];
        $checkout_type = Tools::getIsset('checkout_type') ? Tools::getValue('checkout_type') : null;
        $mp_currency = $this->context->currency->iso_code;
        $total_paid_amount = $this->context->currentLocale->formatPrice($order->total_paid, $mp_currency);

        $this->context->smarty->assign(
            array(
                'checkout_type' => $checkout_type,
                'total_paid_amount' => $total_paid_amount,
            )
        );

        $versions = array(
            self::PRESTA16 => 'six',
            self::PRESTA17 => 'seven',
        );

        return $this->display(__FILE__, 'views/templates/hook/' . $versions[$this->getVersionPs()] . '/order_confirmation.tpl');
    }

    /**
     * Hook triggered when a refund slip is created in the backoffice.
     * Calls the Epayco reversion API and logs the response.
     *
     * Requires the ref_payco to be stored in ps_order_payment.transaction_id
     * when the payment is confirmed.
     *
     * @param array $params Hook parameters (contains 'order', 'productList', 'qtyList')
     * @return void
     */
    public function hookActionOrderSlipAdd($params)
    {
        $order = $params['order'];

        // Get ref_payco stored as transaction_id in ps_order_payment
        $ref_payco = null;
        $payments = $order->getOrderPaymentCollection();
        foreach ($payments as $payment) {
            if (!empty($payment->transaction_id)) {
                $ref_payco = $payment->transaction_id;
                break;
            }
        }

        if (!$ref_payco) {
            $this->logRefund('ERROR: ref_payco not found in ps_order_payment for order id=' . $order->id . ' reference=' . $order->reference);
            return;
        }

        $bearer_token = $this->getEpaycoApifyToken();
        if (!$bearer_token) {
            $this->logRefund('ERROR: Could not obtain Epayco bearer token for order id=' . $order->id . ' ref_payco=' . $ref_payco);
            return;
        }

        $result = $this->callReversionApi((int)$ref_payco, $bearer_token);

        $this->logRefund(
            'ORDER id=' . $order->id . ' reference=' . $order->reference .
            ' ref_payco=' . $ref_payco .
            ' http_code=' . $result['http_code'] .
            ' response=' . $result['body']
        );

        // Store refund result in cookie to display alert on next page load
        $this->context->cookie->__set('epayco_refund_result', $result['body']);
        $this->context->cookie->__set('epayco_refund_order_id', $order->id);
        $this->context->cookie->write();
    }

    /**
     * Hook triggered when the order status changes in the backoffice.
     * When the new state is "Reembolsado" it calls the Epayco reversion API.
     *
     * @param array $params Hook parameters (contains 'newOrderStatus', 'id_order')
     * @return void
     */
    public function hookActionOrderStatusUpdate($params)
    {
        // Auto-register displayAdminOrder hook if not already registered
        if (!$this->isRegisteredInHook('displayAdminOrder')) {
            $this->registerHook('displayAdminOrder');
        }

        $new_state = $params['newOrderStatus'];

        // Only act on the refunded state
        if (strtolower($new_state->name) !== 'reembolsado'
            && (int)$new_state->id !== (int)Configuration::get('PS_OS_REFUND')
        ) {
            return;
        }

        $order = new Order((int)$params['id_order']);
        if (!Validate::isLoadedObject($order)) {
            $this->logRefund('ERROR: Could not load order id=' . $params['id_order']);
            return;
        }

        // Get ref_payco stored as transaction_id in ps_order_payment
        $ref_payco = null;
        $payments = $order->getOrderPaymentCollection();
        foreach ($payments as $payment) {
            if (!empty($payment->transaction_id)) {
                $ref_payco = $payment->transaction_id;
                break;
            }
        }

        if (!$ref_payco) {
            $this->logRefund('ERROR: ref_payco not found in ps_order_payment for order id=' . $order->id . ' reference=' . $order->reference);
            return;
        }

        $bearer_token = $this->getEpaycoApifyToken();
        if (!$bearer_token) {
            $this->logRefund('ERROR: Could not obtain Epayco bearer token for order id=' . $order->id . ' ref_payco=' . $ref_payco);
            return;
        }

        $result = $this->callReversionApi((int)$ref_payco, $bearer_token);

        $this->logRefund(
            'STATUS_UPDATE ORDER id=' . $order->id . ' reference=' . $order->reference .
            ' new_status=' . $new_state->name .
            ' ref_payco=' . $ref_payco .
            ' http_code=' . $result['http_code'] .
            ' response=' . $result['body']
        );

        // Store refund result in cookie to display alert on next page load
        $this->context->cookie->__set('epayco_refund_result', $result['body']);
        $this->context->cookie->__set('epayco_refund_order_id', $order->id);
        $this->context->cookie->write();
    }

    /**
     * Hook to display the refund API response as an alert on the admin order page.
     *
     * @param array $params
     * @return string
     */
    public function hookDisplayAdminOrder($params)
    {
        $refund_result = $this->context->cookie->__get('epayco_refund_result');
        $refund_order_id = $this->context->cookie->__get('epayco_refund_order_id');

        if (!$refund_result) {
            return '';
        }

        // Clear cookie after reading
        $this->context->cookie->__unset('epayco_refund_result');
        $this->context->cookie->__unset('epayco_refund_order_id');
        $this->context->cookie->write();

        $response = json_decode($refund_result, true);
        $success = isset($response['success']) ? $response['success'] : false;
        $title = isset($response['titleResponse']) ? $response['titleResponse'] : 'Respuesta ePayco';
        $text = isset($response['textResponse']) ? $response['textResponse'] : $refund_result;

        $alertType = $success ? 'success' : 'danger';
        $icon = $success ? 'check-circle' : 'exclamation-circle';

        $html = '<div class="alert alert-' . $alertType . '" role="alert" style="margin-top:10px;">';
        $html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $html .= '<span aria-hidden="true">&times;</span>';
        $html .= '</button>';
        $html .= '<p class="alert-text">';
        $html .= '<i class="material-icons" style="vertical-align:middle;margin-right:5px;">' . ($success ? 'check_circle' : 'error') . '</i>';
        $html .= '<strong>ePayco Reembolso - Pedido #' . (int)$refund_order_id . ':</strong> ';
        $html .= htmlspecialchars($title) . ' - ' . htmlspecialchars($text);
        $html .= '</p>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Authenticates against the Epayco apify service and returns a Bearer token.
     *
     * @return string|null
     */
    private function getEpaycoApifyToken()
    {
        $public_key  = Configuration::get('EPAYCO_PUBLIC_KEY');
        $private_key = Configuration::get('EPAYCO_PRIVATE_KEY');

        if (!$public_key || !$private_key) {
            return null;
        }

        $basic = base64_encode($public_key . ':' . $private_key);

        $ch = curl_init('https://eks-apify-service.epayco.io/login');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([]),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . $basic,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        return $data['token'] ?? $data['bearer_token'] ?? null;
    }

    /**
     * Calls the Epayco transaction reversion endpoint.
     *
     * @param int    $ref_payco
     * @param string $bearer_token
     * @return array{http_code: int, body: string}
     */
    private function callReversionApi($ref_payco, $bearer_token)
    {
        $ch = curl_init('https://eks-apify-service.epayco.io/transaction/reversion');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['referencePayco' => $ref_payco]),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $bearer_token,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $body      = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            $body = json_encode(['curl_error' => $curl_error]);
        }

        return ['http_code' => $http_code, 'body' => $body];
    }

    /**
     * Appends a timestamped entry to the refund log file.
     *
     * @param string $message
     * @return void
     */
    private function logRefund($message)
    {
        $log_dir  = EP_ROOT_URL . '/logs';
        $log_file = $log_dir . '/refund.log';

        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * @param $sql_file
     * @return bool
     */
    public function loadSQLFile($sql_file)
    {
        // Get install SQL file content
        $sql_content = Tools::file_get_contents($sql_file);

        // Replace prefix and store SQL command in array
        $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
        $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);

        // Execute each SQL statement
        $result = true;
        foreach ($sql_requests as $request) {
            if (!empty($request)) {
                $result &= Db::getInstance()->execute(trim($request));
            }
        }

        // Return result
        return $result;
    }

    /**
     * Display payment failure on version 1.6
     *
     * @return string
     */
    public function hookDisplayTopColumn()
    {
        return $this->getDisplayFailure();
    }

    /**
     * Display payment failure on version 1.7
     *
     * @return string
     */
    public function hookDisplayWrapperTop()
    {
        return $this->getDisplayFailure();
    }


    /**
     * @return mixed
     */
    public function getDisplayFailure()
    {
        if (Tools::getValue('typeReturn') == 'failure') {
            $cookie = $this->context->cookie;
            if ($cookie->__isset('redirect_message')) {
                $this->context->smarty->assign(array('redirect_message' => $cookie->__get('redirect_message')));
                $cookie->__unset('redirect_message');
            }

            return $this->display(__FILE__, 'views/templates/hook/failure.tpl');
        }
    }
}
