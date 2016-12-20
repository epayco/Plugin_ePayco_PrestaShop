<?php
/**
* 2016 ePayco
*
*  @author    ePayco <daniel.quiroz@payco.co>
*  @copyright 2016 EPAYCO
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class PaycoResponseModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {   
		/*parent::initContent();

        $this->context = Context::getContext();     
     
		$payulatam = new PayuLatam();
		
		if (isset($_REQUEST['signature']))
			$signature = $_REQUEST['signature'];
		else
			$signature = $_REQUEST['firma'];

		if (isset($_REQUEST['merchantId']))
			$merchant_id = $_REQUEST['merchantId'];
		else
			$merchant_id = $_REQUEST['usuario_id'];

		if (isset($_REQUEST['referenceCode']))
			$reference_code = $_REQUEST['referenceCode'];
		else
			$reference_code = $_REQUEST['ref_venta'];

		if (isset($_REQUEST['TX_VALUE']))
			$value = $_REQUEST['TX_VALUE'];
		else
			$value = $_REQUEST['valor'];

		if (isset($_REQUEST['currency']))

			$currency = $_REQUEST['currency'];
		else
			$currency = $_REQUEST['moneda'];

		if (isset($_REQUEST['transactionState']))
			$transaction_state = $_REQUEST['transactionState'];
		else
			$transaction_state = $_REQUEST['estado'];

		$value = number_format($value, 1, '.', '');

		$api_key = Configuration::get('PAYU_LATAM_API_KEY');
		$signature_local = $api_key.'~'.$merchant_id.'~'.$reference_code.'~'.$value.'~'.$currency.'~'.$transaction_state;
		$signature_md5 = md5($signature_local);

		if (isset($_REQUEST['polResponseCode']))
			$pol_response_code = $_REQUEST['polResponseCode'];
		else
			$pol_response_code = $_REQUEST['codigo_respuesta_pol'];

		$messageApproved = '';
		if ($transaction_state == 6 && $pol_response_code == 5)
			$estado_tx = $payulatam->l('Failed Transaction');
		else if ($transaction_state == 6 && $pol_response_code == 4)
			$estado_tx = $payulatam->l('Rejected Transaction');
		else if ($transaction_state == 12 && $pol_response_code == 9994)
			$estado_tx = $payulatam->l('Pending Transaction, Please check if the debit was made in the Bank');
		else if ($transaction_state == 4 && $pol_response_code == 1)
		{
			$estado_tx = $payulatam->l('Transaction Approved');
			$messageApproved = $payulatam->l('Thank you for your purchase!');
		}
		else
		{
			if (isset($_REQUEST['message']))
				$estado_tx = $_REQUEST['message'];
			else
				$estado_tx = $_REQUEST['mensaje'];
		}

		if (isset($_REQUEST['transactionId']))
			$transaction_id = $_REQUEST['transactionId'];
		else
			$transaction_id = $_REQUEST['transaccion_id'];

		if (isset($_REQUEST['reference_pol']))
			$reference_pol = $_REQUEST['reference_pol'];
		else
			$reference_pol = $_REQUEST['ref_pol'];

		if (isset($_REQUEST['pseBank']))
			$pse_bank = $_REQUEST['pseBank'];
		else
			$pse_bank = $_REQUEST['banco_pse'];

		$cus = $_REQUEST['cus'];
		if (isset($_REQUEST['description']))
			$description = $_REQUEST['description'];
		else
			$description = $_REQUEST['descripcion'];

		if (isset($_REQUEST['lapPaymentMethod']))
			$lap_payment_method = $_REQUEST['lapPaymentMethod'];
		else
			$lap_payment_method = $_REQUEST['medio_pago_lap'];

			
		$cart = new Cart((int)$reference_code);
		
		if (Tools::strtoupper($signature) == Tools::strtoupper($signature_md5))
		{
			if (!($cart->orderExists()))
			{
				$customer = new Customer((int)$cart->id_customer);
				$this->context->customer = $customer;
				$payulatam->validateOrder((int)$cart->id, Configuration::get('PAYU_OS_PENDING'), (float)$cart->getordertotal(true), 'PayU', null, array(), (int)$cart->id_currency, false, $customer->secure_key);
				Configuration::updateValue('PAYULATAM_CONFIGURATION_OK', true);
			}
			
			$this->context->smarty->assign(
				array(
					'estadoTx' => $estado_tx,
					'transactionId' => $transaction_id,
					'reference_pol' => $reference_pol,
					'referenceCode' => $reference_code,
					'pseBank' => $pse_bank,
					'cus' => $cus,
					'value' => $value,
					'currency' => $currency,
					'description' => $description,
					'lapPaymentMethod' => $lap_payment_method,
					'messageApproved' => $messageApproved,
					'valid' => true,
					'css' => '../modules/payulatam/css/'
				)
			);

		}
		else
		{
			$this->context->smarty->assign(
				array(
					'valid' => false,
					'css' => '../modules/payulatam/css/'
				)
			);
		}

        $this->setTemplate('response.tpl');*/

        $payco = new Payco();

        global $smarty; 
    $payco->PaymentSuccess($_POST['x_extra1'],$_POST['x_extra2'],$_POST['x_respuesta']);				
	$medio_pago=$_POST['x_franchise'];
	switch($medio_pago)
	{
		case 'VS': $franquicia = "Visa";break;
		case 'MC': $franquicia = "MasterCard";break;
		case 'AM': $franquicia = "American Express";break;
		case 'DN': $franquicia = "Diners";break;
		case 'CR': $franquicia = "Credencial";break;
		case 'PSE': $franquicia = "PSE (Proveedor de Servicios Electr&oacute;nicos)";break;
		case 'DV': $franquicia = "Debito Visa";break;
		case 'DM': $franquicia = "Debito MasterCard";break;		
	}	
      
	/*Acentarpago($_POST['x_respuesta'],$_POST['x_id_factura']);	*/
	$smarty->assign(array('respuesta'=>$_POST['x_respuesta'],
					'ref_venta'=>$_POST['x_id_factura'],
					'valor'=>$_POST['x_amount_ok'],
					'moneda'=>$_POST['x_currency_code'],
					'fecha'=>$_POST['x_fecha_transaccion'],
					'numero_transaccion'=>$_POST['x_transaction_id'],
					'codaprovacion'=>$_POST['x_approval_code'],										
					'refpayco'=>$_POST['x_ref_payco'],
					'franquicia'=>$franquicia,
					'mensaje'=>$_POST['x_response_reason_text']));
	$smarty->display( dirname(__FILE__) . '/tpl/respuesta.tpl' );


    }
}
?>