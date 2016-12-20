<?php
/**
* 2016 ePayco
*
*  @author    ePayco <daniel.quiroz@payco.co>
*  @copyright 2016 EPAYCO
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class paycoreturnModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {   
		

        $payco = new Payco();
        
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
		$cart = new Cart((int)$_POST['x_id_invoice']);
		
		if ($_POST['x_signature']!="")
		{
			/*if (!($cart->orderExists()))
			{
				$customer = new Customer((int)$cart->id_customer);
				$this->context->customer = $customer;
				$payco->validateOrder((int)$cart->id, Configuration::get('PAYCO_OS_PENDING'), (float)$cart->getordertotal(true), 'Payco', null, array(), (int)$cart->id_currency, false, $customer->secure_key);
				Configuration::updateValue('PAYCO_CONFIGURATION_OK', true);
			}*/
		
 		$this->context = Context::getContext();   

		$this->context->smarty->assign(array('respuesta'=>$_POST['x_respuesta'],
						'ref_venta'=>$_POST['x_id_factura'],
						'valor'=>$_POST['x_amount_ok'],
						'moneda'=>$_POST['x_currency_code'],
						'fecha'=>$_POST['x_fecha_transaccion'],
						'numero_transaccion'=>$_POST['x_transaction_id'],
						'codaprovacion'=>$_POST['x_approval_code'],										
						'refpayco'=>$_POST['x_ref_payco'],
						'franquicia'=>$franquicia,
						'mensaje'=>$_POST['x_response_reason_text']));

		 //var_dump($this->context->smarty);
		 $this->setTemplate('respuesta.tpl');
	 }

  }
}
?>