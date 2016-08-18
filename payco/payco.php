<?php

if (!defined('_PS_VERSION_')) exit;
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_Order.php');
include(_PS_MODULE_DIR_ . 'payco/lib/CreditCard_OrderState.php');

class Payco extends PaymentModule {

  private $_html = '';
  private $_postErrors = array();
  public $orderStates;

  public $p_cust_id_cliente;
  public $p_key;
  public $p_test_request;

  function __construct() {

    $this->currencies = true;
    $this->currencies_mode = 'checkbox';
    $this->name = 'payco';
    $this->author = 'ePayco';
    $this->displayName = 'payco';
    $this->tab = 'payments_gateways';
    $this->controllers = array('payment', 'validation');

    $config = Configuration::getMultiple(array('P_CUST_ID_CLIENTE', 'P_KEY','P_TEST_REQUEST'));
    if (isset($config['P_CUST_ID_CLIENTE']))
        $this->p_cust_id_cliente = trim($config['P_CUST_ID_CLIENTE']);
    if (isset($config['P_KEY']))
        $this->p_key = trim($config['P_KEY']);
    if (isset($config['P_TEST_REQUEST']))
        $this->p_test_request = $config['P_TEST_REQUEST'];

    //necessary to use translations
    parent::__construct();

    $this->page = basename(__FILE__, '.php');
    $this->displayName = $this->l('ePayco');
    $this->description = $this->l('ePayco, Tarjetas de Credito, Debito PSE, SafetyPay, efectivo');

    $this->version = '1.0';
    if (!isset($this->p_cust_id_cliente) OR !isset($this->p_key))
        $this->warning = $this->l('Merchant ID and Merchant Password deben estar configurados para utilizar este m�dulo correctamente');
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
      //Register our module hooks so we can interact with PrestaShop
      $this->registerHook('payment');   //Payment Selection Screen
      $this->registerHook('paymentReturn'); //Payment Return
      $this->registerHook('invoice');   //Back Office Invoice View
      $this->registerHook('updateOrderStatus'); //Back Office Order Status Updated

      if (!isset($this->p_cust_id_cliente))
          Configuration::updateValue('P_CUST_ID_CLIENTE', '');
      if (!isset($this->p_key))
          Configuration::updateValue('P_KEY', '');
       if (!isset($this->p_test_request))
          Configuration::updateValue('P_TEST_REQUEST', '');

      //Set up our currencies and issuers
      CreditCard_OrderState::setup();
      //CreditCard_Issuer::setup();
      CreditCard_Order::setup();
      return true;

      } else {
        return false;
      }
  }

    /** 	uninstall()
     * Called on Back Office -> Uninstall */
    function uninstall() {
      CreditCard_Order::remove();
      if (!Configuration::deleteByName('P_CUST_ID_CLIENTE') OR !Configuration::deleteByName('P_TEST_REQUEST') OR !Configuration::deleteByName('P_KEY') OR !parent::uninstall())
          return false;
      return true;
    }

    private function _postValidation() {
      if (Tools::isSubmit('btnSubmit')) {
        if (!Tools::getValue('merchantid'))
          $this->_postErrors[] = $this->l('\'MerchantID\' Campo Requerido.');
        if (!Tools::getValue('merchantpassword'))
          $this->_postErrors[] = $this->l('\'MerchantPassword\' Campo Requerido.');
      }
    }

    private function _postProcess() {
      if (Tools::isSubmit('btnSubmit')) {
        Configuration::updateValue('P_CUST_ID_CLIENTE', Tools::getValue('merchantid'));
        Configuration::updateValue('P_KEY', Tools::getValue('merchantpassword'));
        Configuration::updateValue('P_TEST_REQUEST', Tools::getValue('merchanttest'));

        CreditCard_OrderState::updateStates(intval(Tools::getValue('id_os_initial')), Tools::getValue('id_os_deleteon'));
        $this->_html.= '<div class="bootstrap"><div class="alert alert-success">'.$this->l('Cambios Aplicados Exitosamente') . '</div></div>';
      }
    }

    private function _displayForm() {

      global $cookie;

      $states = CreditCard_OrderState::getOrderStates();
      $id_os_initial = Configuration::get('CREDITCARD_DATA_OS_INITIAL');

      $this->_html .= '<b>'.
      $this->l('Este modulo acepta pagos utilizando la plataforma de ePayco').'</b><br /><br />'.
      $this->l('Si el cliente opta por esta modalidad de pago, el estado del pedido cambia a \'ePayco - Esperando Validacion\'.').'<br/>'.
      $this->l('Cuando el sitio ePayco confirme el pago, el estado del pedido cambia a \'Payment acepted\'.')."<br/><br/>";

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
        }

        $this->_html.='</tbody>
        								</table>
        							</div>
        						</div>
        					</fieldset>
        					<fieldset>
        				<legend>'.utf8_encode("Configuraci�n Cuenta ePayco").'</legend>

                <img src="../modules/payco/boton.png"/>

                <table border="0" width="600" cellpadding="0" cellspacing="0" id="form">
        					<tr><td colspan="2">Por favor especifique su P_CUST_ID_CLIENTE y P_KEY, sumninistrados por ePayco<br /><br /></td></tr>
        					<tr><td width="250" align="justify" style="padding-right:20px;"><b>P_CUST_ID_CLIENTE</b><br>'.utf8_encode("Corresponde a su N�mero de identificaci�n el cual es proporcionado por ePayco").'</td><td><input type="text" name="merchantid" value="' . Tools::htmlentitiesUTF8(Tools::getValue('merchantid', $this->p_cust_id_cliente)) . '" style="width: 300px;" /></td></tr>
        					<tr><td width="250" >&nbsp;&nbsp;</td></tr>
                            <tr><td width="250"  align="justify" style="padding-right:20px;"><b>P_KEY</b><br>Corresponde a una llave transaccional la cual es sumninistrada  por ePayco</td><td><input type="text" name="merchantpassword" value="' . Tools::htmlentitiesUTF8(Tools::getValue('merchantpassword', $this->p_key)) . '" style="width: 300px;" /></td></tr>
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
        		<input type="submit" name="btnSubmit" value="' . $this->l('Update settings') . '" class="button" />
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

    function PaymentSuccess($idcart, $idorden, $respuesta) {
        $transid = $_POST['x_transaction_id'];
        $transdate = $_POST['x_fecha_transaccion'];
        //then add the credit data to the DB
        CreditCard_Order::addDataString($idorden, $transid, $transdate);
        $this->Acentarpago($respuesta, $idorden);
    }

    private function Acentarpago($respuesta, $idorder) {

        $host = _DB_SERVER_;
        $dbName = _DB_NAME_;
        $dbPrefix = _DB_PREFIX_;
        $dbPass = _DB_PASSWD_;
        $dbUser = _DB_USER_;
        $date = date("Y-m-d H:i:s", strtotime("now"));

        $conexion = mysql_connect($host, $dbUser, $dbPass);
        if (!$conexion) {
            die('Error de conexión DB: ');
        }else{
            mysql_select_db($dbName, $conexion);
        }

        if ($respuesta == 'Aceptada'){

            $sqlstado = "SELECT id_order_state FROM " . $dbPrefix . "order_state where color='#30AF49'";
            $resultPayco1 = mysql_query($sqlstado);
            $id_estado1 = mysql_fetch_array($resultPayco1);

            $id_order_state = $id_estado1[0];


        }else if ($respuesta == 'Rechazada'){

            $sqlstado2 = "SELECT id_order_state FROM " . $dbPrefix . "order_state where color='#FF0202'";
            $resultPayco2 = mysql_query($sqlstado2);
            $id_estado2 = mysql_fetch_array($resultPayco2);

            $id_order_state = $id_estado2[0];

        }else if ($respuesta = 'Pendiente'){

            $sqlstado3 = "SELECT id_order_state FROM " . $dbPrefix . "order_state where color='#FFFFAA'";
            $resultPayco3 = mysql_query($sqlstado3);
            $id_estado3 = mysql_fetch_array($resultPayco3);

            $id_order_state = $id_estado3[0];

        }


        if (isset($id_order_state) && isset($idorder)) {


            $sql = "SELECT MAX(id_order_history) FROM " . $dbPrefix . "order_history";
            $result = mysql_query($sql);
            if (!$result) {
                die(' Invalid query: ' . mysql_error());
                return false;
            }
            $id_max = mysql_fetch_array($result);
            $id_new = intval($id_max[0]) + 1;
            $sql = "INSERT INTO " . $dbPrefix . "order_history (`id_order_history`, `id_employee`, `id_order`, `id_order_state`, `date_add`) ";
            $sql .= "VALUES (" . $id_new . ", '1','" . $idorder . "', '" . $id_order_state . "', '" . $date . "')";
            $result = mysql_query($sql);

            //Actualizar la Orden
            $sqlupdate = 'Update ' . $dbPrefix . 'orders set current_state=' . $id_order_state . ' where id_order=' . $idorder . '';
            $resulupdate = mysql_query($sqlupdate);

            if (!$result)
                die('No se puede insertar');
            return false;
        }else {
            return true;
        }

        mysql_close($conexion);
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

        $smarty->assign(array(
            'nbProducts' => $cart->nbProducts(),
            'default_currency' => $cookie->id_currency,
            'currencies' => $this->getCurrency(),
            'total' => $cart->getOrderTotal(true, 3),
            'iva' => $cart->getOrderTotal(true, 3) - $cart->getOrderTotal(false, 3),
            'baseDevolucionIva' => $cart->getOrderTotal(false, 3),
            'merchantid' => $this->p_cust_id_cliente,
            'merchantpassword' => $this->p_key,
            'merchanttest' => $this->p_test_request,
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

    public function hookPaymentReturn($params) {

      if (!$this->active) return;

      global $smarty, $cart, $cookie;

      //Agregar la order a credicar
      $id_order = $_GET['id_order'];
      $extra1 = $params['objOrder']->id_cart;
      $extra2 = $id_order;
      $emailComprador = $this->context->customer->email;
      $value = $params['total_to_pay'];
      $valorBaseDevolucion = $params['objOrder']->total_paid_tax_excl;
      $iva = $value - $valorBaseDevolucion;

      //$valor = str_replace('.', '', $valor);
      if ($iva == 0) $valorBaseDevolucion = 0;

      $currency = $this->getCurrency();
      $idcurrency = $params['objOrder']->id_currency;
      foreach ($currency as $mon) {
        if ($idcurrency == $mon['id_currency']) $currency = $mon['iso_code'];
      }

      //si no existe la moneda
      if ($currency == '') $currency = 'COP';

      $refVenta = $params['objOrder']->reference;

      $state = $params['objOrder']->getCurrentState();
      $creditcard = new CreditCard_Order();
      $esorden = $creditcard->isCreditCardOrder($id_order);



      if ($state) {

        $p_signature = md5(trim($this->p_cust_id_cliente).'^'.trim($this->p_key).'^'.$refVenta.'^'.$value.'^'.$currency);

        // print_r($this->p_test_request);
        // die();

        $smarty->assign(array(
          'this_path_bw' => $this->_path,
          'p_signature' => $p_signature,
          'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
          'status' => 'ok',
          'refVenta' => $refVenta,
          'custemail' => $emailComprador,
          'extra1' => $extra1,
          'extra2' => $extra2,
          'total' => $value,
          'currency' => $currency,
          'iva' => $iva,
          'baseDevolucionIva' => $valorBaseDevolucion,
          'merchantid' => trim($this->p_cust_id_cliente),
          'merchantpassword' => trim($this->p_key),
          'merchanttest'=> $this->p_test_request,
          'p_key'=>trim($this->p_key),
          'custip' => $_SERVER['REMOTE_ADDR'],
          'custname' => ($cookie->logged ? $cookie->customer_firstname . ' ' . $cookie->customer_lastname : false),
          'returnurl' => Context::getContext()->link->getModuleLink('payco', 'return'),
          'p_billing_email' => $this->context->customer->email,
          'p_billing_name' => $this->context->customer->firstname,
          'p_billing_lastname' => $this->context->customer->lastname
          )
        );

      } else {
          $smarty->assign('status', 'failed');
      }

      return $this->display(__FILE__, 'payment_return.tpl');
    }

    function hookInvoice($params) {
        global $smarty;
        $id_order = $params['id_order'];
        if (CreditCard_Order::isCreditCardOrder($id_order)) {
            $data_string = CreditCard_Order::getTransactionID($id_order);
            $transdate = CreditCard_Order::getTransactionDate($id_order);

            $smarty->assign(array(
                'string' => $data_string,
                'transdate' => $transdate,
                'id_order' => $id_order,
                'this_page' => $_SERVER['REQUEST_URI'],
                'this_path' => $this->_path,
                'this_path_ssl' => Configuration::get('PS_FO_PROTOCOL') . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . "modules/{$this->name}/"));
            return $this->display(__FILE__, 'tpl/invoice_block.tpl');
        }
        else
            return "";
    }

    /**
     * 	hookPaymentReturn($params)
     * 	Called in Front Office upon order placement
     */
    function hookUpdateOrderStatus($params) {
      if (CreditCard_OrderState::isDeleteOnState(intval($params['newOrderStatus']->id)))
            CreditCard_Order::removeDataString($params['id_order']);
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

        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }

}

?>
