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
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Epayco');
        $this->description = $this->l('Acepta pagos fácilmente en tu tienda con ePayco: Tarjeta de crédito - débito, PSE, Daviplata, suscripciones y efectivo, todo en una integración rápida y segura.');
        $this->confirmUninstall = $this->l('¿Estás seguro de que deseas desinstalar el módulo?');
        $this->module_key = '4380f33bbe84e7899aacb';
        $this->ps_version = _PS_VERSION_;
        $this->assets_ext_min = !_PS_MODE_DEV_ ? '.min' : '';
        $this->path = $this->_path;
        $this->_context = $this->context;
        $this->standardCheckout = new StandardCheckoutEpayco($this->name,$this->_context, $this->path);
        $this->pseCheckout = new PseCheckoutEpayco($this->_context);
        $this->creditcardCheckout = new CreditcardEpaycoCheckout($this->name,$this->_context, $this->path);
        $this->ticketCheckout = new TicketEpaycoCheckout($this->name,$this->_context, $this->path);
        $this->daviplataCheckout = new DaviplataEpaycoCheckout($this->name,$this->_context, $this->path);
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
            $this->registerHook('orderConfirmation')&&
            $this->registerHook('displayWrapperTop') &&
            $this->registerHook('displayTopColumn');
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
            'EPAYCO_DAVIPLATA_CHECKOUT' => 'getDaviplataCheckout',
            'EPAYCO_CREDITCARD_CHECKOUT' => 'getCreditcardCheckout',
            'EPAYCO_TICKET_CHECKOUT' => 'getTicketCheckout',
            'EPAYCO_PSE_CHECKOUT' => 'getPseCheckout',
        );

        foreach ($checkouts as $checkout => $method) {
            if ($this->isActiveCheckout($checkout) ) {
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

     //pago con payco checkout estandar
    public function getStandardCheckout($cart, $version)
    {
        if ($version == self::PRESTA16) {
            $frontInformations = $this->standardCheckout->getStandardCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/standard.tpl');
        } else {
            $title = Configuration::get('EPAYCO_STANDARD_TITLE')??'pago con payco standard';
            $frontInformations = $this->standardCheckout->getStandardCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/standard.tpl');
            $standardCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $standardCheckout->setForm($infoTemplate)
                ->setCallToActionText($this->l($title))
                ->setLogo(_MODULE_DIR_ . 'payco/views/img/logo-negro.png');

            return $standardCheckout;
        }
    }

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */

     //pago con payco checkout daviplata
    public function getDaviplataCheckout($cart, $version)
    {
        if ($version == self::PRESTA16) {
            $frontInformations = $this->daviplataCheckout->getDaviplataCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/daviplata.tpl');
        } else {
            $title = Configuration::get('EPAYCO_DAVIPLATA_TITLE')??'pago con payco';
            $frontInformations = $this->daviplataCheckout->getDaviplataCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/daviplata.tpl');
            $daviplataCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $daviplataCheckout->setForm($infoTemplate)
                ->setCallToActionText($this->l($title))
                ->setLogo(_MODULE_DIR_ . 'payco/views/img/icon-daviplata.png');

            return $daviplataCheckout;
        }
    }

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */

        //pago con payco checkout tarjeta de credito
    public function getCreditcardCheckout($cart, $version)
    {
        if ($version == self::PRESTA16) {
            $frontInformations = $this->creditcardCheckout->getCreditcardCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/creditcard.tpl');
        } else {
            $title = Configuration::get('EPAYCO_CREDITCARD_TITLE')??'pago con payco';
            $frontInformations = $this->creditcardCheckout->getCreditcardCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/creditcard.tpl');
            $creditcardCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $creditcardCheckout->setForm($infoTemplate)
                ->setCallToActionText($this->l($title))
                ->setLogo(_MODULE_DIR_ . 'payco/views/img/credit-card-botton-payment.png');

            return $creditcardCheckout;
        }
    }

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */

     //medio de pago efectivo en la tienda
    public function getTicketCheckout($cart, $version)
    {
        
        if ($version == self::PRESTA16) {
            $frontInformations = $this->ticketCheckout->getTicketCheckoutPS16($cart);
            $this->context->smarty->assign($frontInformations);
            return $this->display(__FILE__, 'views/templates/hook/six/ticket.tpl');
        } else {
            $title = Configuration::get('EPAYCO_TICKET_TITLE')??'Ticket';
            $frontInformations = $this->ticketCheckout->getTicketCheckoutPS17($cart);
            $infoTemplate = $this->context->smarty->assign($frontInformations)
                ->fetch('module:payco/views/templates/hook/seven/ticket.tpl');
            $ticketCheckout = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
            $ticketCheckout->setForm($infoTemplate)
                ->setCallToActionText($this->l($title))
                ->setLogo(_MODULE_DIR_ . 'payco/views/img/ticket-botton.png');

            return $ticketCheckout;
        }
    }
    

    /**
     * @param  $cart
     * @param  $version
     * @return PaymentOption | string
     */
        //pago con payco checkout pse
    public function getPseCheckout($cart, $version)
    {
        $pluginInfos = array(
            'redirect_link' => $this->context->link->getModuleLink($this->name, PseCheckoutEpayco::PAYMENT_METHOD_NAME),
            'module_dir' => $this->path,
        );
        $title = Configuration::get('EPAYCO_PSE_TITLE')??'PSE';
        $templateData = $this->pseCheckout->getPseTemplateData($pluginInfos);
        $infoTemplate = $this->context->smarty->assign($templateData)
            ->fetch('module:payco/views/templates/hook/seven/pse.tpl');
        $psePaymentOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $psePaymentOption->setForm($infoTemplate)
            ->setCallToActionText($this->l($title) )
            ->setLogo(_MODULE_DIR_ . 'payco/views/img/icon-pse.png');

        return $psePaymentOption;
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
        $paymentId = Tools::getValue('ref_payco');
        $payment = json_decode($epaycoData);
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