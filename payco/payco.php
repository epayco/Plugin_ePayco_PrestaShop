<?php

if (!defined('_PS_VERSION_')) exit;

include(_PS_MODULE_DIR_ . 'payco/lib/EpaycoOrder.php');
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_Order.php');
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_OrderState.php');

class Payco extends PaymentModule {

  private $_html = '';
  private $_postErrors = array();
  public $orderStates;
  /*Declaramos las variables*/
  public $p_cust_id_cliente;
  public $p_key;
  public $public_key;
  public $p_test_request;

  function __construct() {


    $this->name = 'payco';
    $this->tab = 'payments_gateways';
    $this->version = '1.1';
    $this->author = 'ePayco';
    $this->displayName = 'ePayco';
    $this->controllers = array('payment', 'validation','return');
    $this->currencies = true;
    $this->currencies_mode = 'checkbox';
   
    $config = Configuration::getMultiple(array('P_CUST_ID_CLIENTE','P_KEY','PUBLIC_KEY','P_TEST_REQUEST'));
    if (isset($config['P_CUST_ID_CLIENTE']))
        $this->p_cust_id_cliente = trim($config['P_CUST_ID_CLIENTE']);
    if (isset($config['P_KEY']))
        $this->p_key = trim($config['P_KEY']);
    if (isset($config['PUBLIC_KEY']))
        $this->public_key = trim($config['PUBLIC_KEY']);  
    if (isset($config['P_TEST_REQUEST']))
        $this->p_test_request = $config['P_TEST_REQUEST'];

    //necessary to use translations
    $this->bootstrap = true;
    parent::__construct();

    $this->page = basename(__FILE__, '.php');
    $this->displayName = $this->l('ePayco');
    $this->description = $this->l('ePayco, Tarjetas de Credito, Debito PSE, SafetyPay, Efectivo');
    $this->confirmUninstall = $this->l('Está seguro que desea desinstalar ePayco');
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);


    if (!isset($this->p_cust_id_cliente) OR !isset($this->p_key) OR !isset($this->public_key))
        $this->warning = $this->l('P_CUST_ID_CLIENTE, P_KEY y PUBLIC_KEY deben estar configurados para utilizar este módulo correctamente');
    if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
        $this->warning = $this->l('No currency set for this module');

    $this->orderStates['waiting'] = Configuration::get('CREDITCARD_ORDERSTATE_WAITING');
    $this->orderStates['validated'] = Configuration::get('CREDITCARD_ORDERSTATE_VALIDATED');
    $this->options['verifyAddress'] = Configuration::get('CREDITCARD_OPTIONS_VERIFYADDRESS') == '1' ? true : false;
  }

  /** 	install()
   * 	Called on Back Office -> Install */
  public function install() {

    if (parent::install()) {
    if (version_compare(_PS_VERSION_, '1.7.0.0 ', '<')){
      $this->registerHook('payment');
      }else{
        $this->registerHook('paymentOptions');
      }
      $this->registerHook('paymentReturn'); //Payment Return
      $this->registerHook('updateOrderStatus'); //Back Office Order Status Updated

      if (!isset($this->p_cust_id_cliente))
          Configuration::updateValue('P_CUST_ID_CLIENTE', '');
      if (!isset($this->p_key))
          Configuration::updateValue('P_KEY', '');
      if (!isset($this->public_key))
          Configuration::updateValue('PUBLIC_KEY', '');  
       if (!isset($this->p_test_request))
          Configuration::updateValue('P_TEST_REQUEST', '');
        
      ///Set up our currencies and issuers
      CreditCard_OrderState::remove();
      CreditCard_OrderState::setup();
      //CreditCard_Issuer::setup();
      CreditCard_Order::setup();

      //crear la tabla para administrar los pedidos
      EpaycoOrder::remove();
      EpaycoOrder::setup();
      return true;

      } else {
        return false;
      }
  }

    /** 	uninstall()
     * Called on Back Office -> Uninstall */
    function uninstall() {
      CreditCard_Order::remove();
      Configuration::deleteByName('payco', false);
      //eliminar la tabla para administrar los pedidos
      EpaycoOrder::remove();
      if (!Configuration::deleteByName('P_CUST_ID_CLIENTE') OR !Configuration::deleteByName('P_TEST_REQUEST') OR !Configuration::deleteByName('P_KEY') OR !Configuration::deleteByName('PUBLIC_KEY') OR !parent::uninstall())
          return false;
      return true;
    }

    private function _postValidation() {
      if (Tools::isSubmit('btnSubmit')) {
        if (!Tools::getValue('merchantid'))
          $this->_postErrors[] = $this->l('\'P_CUST_ID_CLIENTE\' Campo Requerido.');
        if (!Tools::getValue('merchantpassword'))
          $this->_postErrors[] = $this->l('\'P_KEY\' Campo Requerido.');
        if (!Tools::getValue('public_key'))
          $this->_postErrors[] = $this->l('\'PUBLIC_KEY\' Campo Requerido.');
      }
    }

    private function _postProcess() {
      if (Tools::isSubmit('btnSubmit')) {
        Configuration::updateValue('P_CUST_ID_CLIENTE', Tools::getValue('merchantid'));
        Configuration::updateValue('P_KEY', Tools::getValue('merchantpassword'));
        Configuration::updateValue('PUBLIC_KEY', Tools::getValue('public_key'));
        Configuration::updateValue('P_TEST_REQUEST', Tools::getValue('merchanttest'));
        CreditCard_OrderState::updateStates(intval(Tools::getValue('id_os_initial')), Tools::getValue('id_os_deleteon'));
        $this->_html.= '<div class="bootstrap"><div class="alert alert-success">'.$this->l('Cambios Aplicados Exitosamente') . '</div></div>';
      }
    }

    private function _displayForm() {

      global $cookie;

      $states = CreditCard_OrderState::getOrderStates();
      $id_os_initial = Configuration::get('PAYCO_ORDERSTATE_WAITING');

      $this->_html .= '<b>'.
      $this->l('Este modulo acepta pagos utilizando la plataforma de ePayco').'</b><br /><br />'.
      $this->l('Si el cliente opta por esta modalidad de pago, el estado del pedido cambia a \'ePayco Esperando Pago\'.').'<br/>'.
      $this->l('Cuando el sitio ePayco confirme el pago, el estado del pedido cambia a \'Pago aceptado\'.')."<br/><br/>";

      $this->_html.='<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post" class="half_form">

        <fieldset style="width: 90%; overflow: auto;display:none;">
        <legend style="cursor:pointer;">
          <img src="../img/admin/cog.gif" />
            '.$this->l("Advanced Settings").':
        </legend>
        <div id="advanced" >
          <div style="float: left;padding:10px;">
            <table cellpadding="0" cellspacing="0" class="table">
            <thead>
              <tr>
                <th style="width: 200px;font-weight: bold;"><p style="display:inline;color:red">Advanced</p> Order States</th>
                <th>Initial State</th>
                <th>Delete On</th>
              </tr>
            </thead>
            <tbody>';


        foreach ($states as $item => $state) {
          $checked = "";
          $checkedorder = "";
          if ($state['id_order_state'] == $id_os_initial) {
            $checked = 'checked=checked';
          }

          if ($state['id_order_state']) {
            $checkedorder = 'checked=checked';
          }

          $this->_html.='.<tr style="background-color: ' . $state['color'] . ';">
            <td>' . $this->l($state['name']) . '</td>
            <td style="text-align:center"><input type="radio" name="id_os_initial" ' . $checked . ' value="' . $state['id_order_state'] . '"/></td>
            <td style="text-align:center"><input type="checkbox" name="id_os_deleteon[]" value="' . $state['id_order_state'] . '" ' . $checkedorder . ' /> </td>
            </tr>';
        }

        if(Tools::getValue('merchanttest', $this->p_test_request) == "TRUE") {
            $checked1 = "selected";
            $checked2 = "";
        } else if(Tools::getValue('merchanttest', $this->p_test_request) == "FALSE") {
            $checked1 ="";
            $checked2 = "selected";
        }else{
          $checked1 ="selected";
          $checked2 = "";
        }

        $this->_html.='</tbody>
        								</table>
        							</div>
        						</div>
        					</fieldset>
        					<fieldset>
        				<legend>'.utf8_encode("Configuraci&oacute;n ePayco").'</legend>

                <img src="../modules/payco/boton.png"/>

                <table border="0" width="600" cellpadding="0" cellspacing="0" id="form">
        					<tr><td colspan="2">Por favor especifique su P_CUST_ID_CLIENTE, P_KEY y PUBLIC_KEY, sumninistrados por ePayco<br /><br /></td></tr>
        					<tr><td width="250" align="justify" style="padding-right:20px;"><b>P_CUST_ID_CLIENTE</b><br>ID de cliente que lo representa en la plataforma. es Proporcionado en su panel de clientes en la opción configuración.</td><td><input type="text" name="merchantid" value="' . Tools::htmlentitiesUTF8(Tools::getValue('merchantid', $this->p_cust_id_cliente)) . '" style="width: 300px;" /></td></tr>
        					<tr><td width="250" >&nbsp;&nbsp;</td></tr>
                            <tr><td width="250"  align="justify" style="padding-right:20px;"><b>P_KEY</b><br>Corresponde a la llave transacción de su cuenta, Proporcionado en su panel de clientes en la opción configuración.</td><td><input type="text" name="merchantpassword" value="' . Tools::htmlentitiesUTF8(Tools::getValue('merchantpassword', $this->p_key)) . '" style="width: 300px;" /></td></tr>
                    <tr><td width="250" >&nbsp;&nbsp;</td></tr>
                            <tr><td width="250"  align="justify" style="padding-right:20px;"><b>PUBLIC_KEY</b><br>ID de cliente que lo representa en la plataforma. es Proporcionado en su panel de clientes en la opción configuración.</td><td><input type="text" name="public_key" value="' . Tools::htmlentitiesUTF8(Tools::getValue('public_key', $this->public_key)) . '" style="width: 300px;" /></td></tr>        
        				    <tr><td width="250" >&nbsp;&nbsp;</td></tr>
                            <tr><td width="250" ><b>Sitio en pruebas</b><br></td>
                            <td><select name="merchanttest" >
                                    <option value="TRUE" '. $checked1.'>SI</option>
                                    <option value="FALSE" '. $checked2.'>NO</option>
                                </select>
                            </td>
                            </tr>

        				</table>
        			</fieldset>
        	<div style="clear: both;"></div>
        	<br/>
        	<center>
        		<input type="submit" name="btnSubmit" value="' . $this->l('Guardar Cambios') . '" class="button" />
        	</center>
        	<hr />
        </form>';

    }

    public function getContent() {
      $this->_html = '<h2>' . $this->displayName . '</h2>';

      if (Tools::isSubmit('btnSubmit')) {
        $this->_postValidation();
        if (!count($this->_postErrors)) {
          $this->_postProcess();
        } else {
          foreach ($this->_postErrors as $err) {
            $this->_html .= '<div class="alert error">' . $err . '</div>';
          }
        }
      } else {
        $this->_html .= '<br/>';
      }

      $this->_displayForm();
      return $this->_html;
    }

    public function PaymentReturnOnpage() {

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

        if (isset($_REQUEST["x_ref_payco"])) {
            $config = Configuration::getMultiple(array('P_CUST_ID_CLIENTE', 'P_KEY', 'PUBLIC_KEY', 'P_TEST_REQUEST'));
            $public_key = $config["PUBLIC_KEY"];
            $ref_payco = $_REQUEST["x_ref_payco"];
            $url = "https://secure.payco.co/restpagos/transaction/response.json?ref_payco=$ref_payco&public_key=" . $public_key;
            $confirmation = true;
        }

        if (isset($_REQUEST["?ref_payco"]) != "" || isset($_REQUEST["ref_payco"]) || $ref_payco) {

            if (isset($_REQUEST["?ref_payco"])) {
                $ref_payco = $_REQUEST["?ref_payco"];
            }
            if (isset($_REQUEST["ref_payco"])) {
                $ref_payco = $_REQUEST["ref_payco"];
            }
            if ($url == "") {
                $url = 'https://secure.epayco.co/validation/v1/reference/' . $ref_payco;
            }
        }

        if ($ref_payco != "" and $url != "") {

            $responseData = $this->PostCurl($url, false, $this->StreamContext());

            $jsonData = @json_decode($responseData, true);
            $data = $jsonData['data'];

            //Consultamos la transaccion en el servidor
            $data["ref_payco"] = $ref_payco;
            $data["url"] = $url;

            $this->Acentarpago($data["x_extra1"], $data["x_cod_transaction_state"], $data["x_ref_payco"], $data["x_transaction_id"], $data["x_amount"], $data["x_currency_code"], $data["x_signature"], $confirmation);
            $smarty=$this->context->smarty;
            
            $smarty->assign($data);
        }

         //return $this->display(__FILE__, 'response.tpl');

    }

    function PaymentSuccess($extra1,$response,$referencia,$transid,$amount,$currency,$signature) {
        $this->Acentarpago($extra1,$response,$referencia,$transid,$amount,$currency,$signature);
    }

    private function Acentarpago($extra1,$response,$referencia,$transid,$amount,$currency,$signature) {

           $config = Configuration::getMultiple(array('P_CUST_ID_CLIENTE','P_KEY','PUBLIC_KEY','P_TEST_REQUEST'));  
           $x_cust_id_cliente=trim($config['P_CUST_ID_CLIENTE']);
           $x_key=trim($config['P_KEY']);
           $idorder=$extra1;
           $x_cod_response=(int)$response;
           $x_signature=hash('sha256',
            $x_cust_id_cliente.'^'
            .$x_key.'^'
            .$referencia.'^'
            .$transid.'^'
            .$amount.'^'
            .$currency
          );

          $state = 'PAYCO_OS_REJECTED';
          if ($x_cod_response == 4)
            $state = 'PAYCO_OS_FAILED';
          else if ($x_cod_response == 2)
            $state = 'PAYCO_OS_REJECTED';
          else if ($x_cod_response == 3)
            $state = 'PAYCO_OS_PENDING';
          else if ($x_cod_response == 1)
            $state = 'PS_OS_PAYMENT';
          
          //Validamos la firma
          if($x_signature==$signature){

            $id_state=(int)Configuration::get($state);
        
            $order = new Order((int)Order::getOrderByCartId((int)$idorder));
            
            //Busca si ya se restauro el stock y si se configuro reducir el stock en transacciones pendientes  
            if ($x_cod_response == 3 && !EpaycoOrder::ifStockDiscount($order->id)) {

                //reducir el stock
                if (EpaycoOrder::updateStockDiscount($order->id,1)) {
                    $this->RestoreStock($order,'-');
                }
            }
            $current_state = $order->current_state;

            if($current_state!=2){

              //validar si la transaccion esta pendiente y pasa a rechazada y ya habia descontado el stock
              if($current_state == Configuration::get('PAYCO_OS_PENDING') && ($x_cod_response == 2 || $x_cod_response == 4) && EpaycoOrder::ifStockDiscount($order->id)){

                  //si no se restauro el stock restaurarlo inmediatamente
                  $this->RestoreStock($order,'+');
              };
             
              if ($current_state != Configuration::get('PS_OS_PAYMENT'))
              {
                $history = new OrderHistory();
                $history->id_order = (int)$order->id;
                $history->date_add=date('Y-m-d H:i:s');
                $history->date_upd=date('Y-m-d H:i:s');
                $history->changeIdOrderState((int)Configuration::get($state), $order, true);
                $history->addWithemail(false);
              }
              if ($state != 'PS_OS_PAYMENT')
              {
                /*foreach ($order->getProductsDetail() as $product)
                  StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], + (int)$product['product_quantity'], $order->id_shop);*/
                  //Busca si ya se descontó el stock
                if (!EpaycoOrder::ifStockDiscount($order->id)) {
                    
                    //se descuenta el stock
                    if (EpaycoOrder::updateStockDiscount($order->id,1)) {
                        $this->RestoreStock($order,'-');
                    }
                }
              }

            }
            
          }
          header("location:index.php?controller=history");
                           
    }

    /** 	execPayment($cart)
     * 	Called from front office when a user clicks "Pay with Credit Card" */
    function execPayment($cart) {


        if (!$this->active)
            return;
        if (!$this->_checkCurrency($cart))
            return;

        global $cookie, $smarty;

        if (isset($_POST['USR_MSG'])) {
            $msgpost = $_POST['USR_MSG'];
        } else {
            $msgpost = '';
        }
        if (isset($_GET['USR_MSG'])) {
            $msgget = $_GET['USR_MSG'];
        } else {
            $msgget = '';
        }

        $transid = $cart->id . "" . time();

        $transsignature = sha1("##" . strtoupper($this->p_cust_id_cliente) . "##" . strtoupper($this->p_key) . "##" . strtoupper($transid) . "##" . $total . "##0##");
        $addressdelivery = new Address(intval($cart->id_address_delivery));
        $addressbilling = new Address(intval($cart->id_address_invoice));

        if (Validate::isLoadedObject($addressdelivery) AND Customer::customerHasAddress(intval($cookie->id_customer), intval($cart->id_address_delivery))) {
            $smarty->assign(array(
                'SHIPPING_ADDRESS' => $addressdelivery->address1 . " " . $addressdelivery->address2,
                'SHIPPING_ADDRESS_CITY' => $addressdelivery->city,
                'SHIPPING_ADDRESS_REGION' => "",
                'SHIPPING_ADDRESS_STATE' => State::getNameById($addressdelivery->id_state),
                'SHIPPING_ADDRESS_POSCODE' => $addressdelivery->postcode,
                'SHIPPING_ADDRESS_COUNTRY_CODE' => Country::getIsoById($addressdelivery->id_country)
            ));
        }
        if($this->p_test_request=="TRUE"){
          $merchanttest="true";
        }else{
           $merchanttest="false";
        }
        $smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'default_currency' => $cookie->id_currency,
            'currencies' => $this->getCurrency(),
            'total' => $cart->getOrderTotal(true, 3),
            'iva' => $cart->getOrderTotal(true, 3) - $cart->getOrderTotal(false, 3),
            'baseDevolucionIva' => $cart->getOrderTotal(false, 3),
            'merchantid' => $this->p_cust_id_cliente,
            'merchantpassword' => $this->p_key,
            'merchanttest' =>$merchanttest,
            'msgpost1' => $msgpost,
            'this_path' => $this->_path,
            'custip' => $_SERVER['REMOTE_ADDR'],
            'transid' => $transid,
            'transsign' => $transsignature,
            'custname' => $cookie->logged ? $cookie->customer_firstname . ' ' . $cookie->customer_lastname : false,
            'this_path_ssl' => Tools::getHttpHost(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
        ));

        return $this->display(__FILE__, 'payment_execution.tpl');
    }
 function hookPayment($params) {

      if (!$this->active) return;
      if (!$this->checkCurrency($params['cart'])) return;
      $this->smarty->assign(array(
        'this_path' => $this->_path,
        'this_path_bw' => $this->_path,
        'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
      ));
      return $this->display(__FILE__, 'payment.tpl');
    }  
  public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $payment_options = [
            $this->getModalepayco(),
        ];

        return $payment_options;
    }
public function getModalepayco()
    {
        $modalOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $modalOption->setCallToActionText($this->l(''))
                      ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
                      ->setAdditionalInformation($this->context->smarty->fetch('module:payco/views/templates/front/payment_onpage.tpl'))
                      ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/logo-small.png'));

        return $modalOption;
    }

    public function hookPaymentReturn($params) {
     

      if (!$this->active) return;

      global $smarty, $cart, $cookie;
      if (version_compare(_PS_VERSION_, '1.7.0.0 ', '<')){
      $order = $params['objOrder'];
      $value = $params['total_to_pay'];
      $currence = $params['currencyObj'];
      }else{
        $order = $params['order'];
        $value = $params['order']->getOrdersTotalPaid();
        $currence = new Currency($params['order']->id_currency);
      }

      $value = round($value,2);
      //Agregar la order a credicar
      $id_order = $_GET['id_order'];
      $extra1 = $order->id_cart;
      $extra2 = $id_order;
      $emailComprador = $this->context->customer->email;
      $valorBaseDevolucion = $order->total_paid_tax_excl;
      $iva = $value - $valorBaseDevolucion;

      /*
      Para determinar la ubicación o por default CO
      */
      $iso = 'CO';
      //$valor = str_replace('.', '', $valor);
      if ($iva == 0) $valorBaseDevolucion = 0;

      $currency = $this->getCurrency();
      $idcurrency = $order->id_currency;
    

      foreach ($currency as $mon) {
        if ($idcurrency == $mon['id_currency']) $currency = $mon['iso_code'];
      }

      //si no existe la moneda
      if ($currency == '') $currency = 'COP';

      $refVenta = $order->reference;

      $state = $order->getCurrentState();

      if ($state) {

        $p_signature = md5(trim($this->p_cust_id_cliente).'^'.trim($this->p_key).'^'.$refVenta.'^'.$value.'^'.$currency);

        $addressdelivery = new Address((int)($cart->id_address_delivery));

        // print_r($this->p_test_request);
        // die();
        if($this->p_test_request=="TRUE"){
          $merchanttest="true";
        }else{
           $merchanttest="false";
        }

        //Busca si ya se restauro el stock
        if (!EpaycoOrder::ifExist($order->id)) {
            //si no se restauro el stock restaurarlo inmediatamente
            $this->RestoreStock($order,'+');
            EpaycoOrder::create($order->id,1);
        }

        $smarty->assign(array(
          'this_path_bw' => $this->_path,
          'p_signature' => $p_signature,
          'total_to_pay' => Tools::displayPrice($value, $currence, false),
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
          'merchantpassword' => trim($this->p_key),
          'merchanttest'=> $merchanttest,
          'p_key'=>trim($this->p_key),
          'public_key'=>trim($this->public_key),
          'custip' => $_SERVER['REMOTE_ADDR'],
          'custname' => ($cookie->logged ? $cookie->customer_firstname . ' ' . $cookie->customer_lastname : false),
          'returnurl' => Context::getContext()->link->getModuleLink('payco', 'return'),
          'responseurl' => Context::getContext()->link->getModuleLink('payco', 'return'),
          'p_billing_email' => $this->context->customer->email,
          'p_billing_name' => $this->context->customer->firstname,
          'p_billing_last_name' => $this->context->customer->lastname,
          'p_billing_address'=>$addressdelivery->address1 . " " . $addressdelivery->address2,
          'p_billing_city'=>$addressdelivery->city,
          'p_billing_country'=>$addressdelivery->id_state,
          )
        );

      } else {
          $smarty->assign('status', 'failed');
      }

      return $this->display(__FILE__, 'payment_return.tpl');
    }

    /**
     * 	hookPaymentReturn($params)
     * 	Called in Front Office upon order placement
     */
    function hookUpdateOrderStatus($params) {
      /*if (CreditCard_OrderState::isDeleteOnState(intval($params['newOrderStatus']->id)))
            CreditCard_Order::removeDataString($params['id_order']);*/
    }

    /*
     * 	validateCard($cardnumber)
     * 	Checks mod10 check digit of card, returns true if valid
     */

    function validateCard($cardnumber) {

      $cardnumber = preg_replace("/\D|\s/", "", $cardnumber);  # strip any non-digits
      $cardlength = strlen($cardnumber);
      if ($cardlength != 0) {
        $parity = $cardlength % 2;
        $sum = 0;
        for ($i = 0; $i < $cardlength; $i++) {
          $digit = $cardnumber[$i];
          if ($i % 2 == $parity)
              $digit = $digit * 2;
          if ($digit > 9)
              $digit = $digit - 9;
          $sum = $sum + $digit;
        }

        $valid = ($sum % 10 == 0);
        return $valid;
      }
      return false;
    }

    /**
     * is_blank( $var )
     * returns true if the var is blank (its like isset() but it works!)
     */
    function is_blank($var) {
        return isset($var) || $var == '0' ? ($var == "" ? true : false) : false;
    }

    public function checkCurrency($cart) {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        return true;
        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }

    private function PostCurl($url) {

        if (function_exists('curl_init')) {
            $ch = curl_init();
            $timeout = 5;
            $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        } else {
            $data = @Tools::file_get_contents($url);
            return $data;
        }
    }

    //restaurar el stock de la orden
    private function RestoreStock($orderId,$operation){
        $order = $orderId;

        foreach ($order->getProductsDetail() as $product){
            StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], $operation.(int)$product['product_quantity'], $order->id_shop);
        }
    }

    private function StreamContext() {

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'protocol_version' => 1.1,
                'timeout' => 10,
                'ignore_errors' => true
            )
        ));

        return $context;
    }

}
