<?php
//echo "hola mundo de afuera";
//var_export($_REQUEST);

$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
Tools::displayFileAsDeprecated();
include(dirname(__FILE__).'/payco.php');
//$context = Context::getContext();
include( '../../header.php' );
$payco = new Payco();
if (isset($_POST['x_cod_respuesta']))
{	
	
        $payco->PaymentSuccess($_POST['x_extra1'],$_POST['x_extra2'],$_POST['x_cod_respuesta']);			
	//Tools::redirect('index.php?controller=respuestaev1'.($_REQUEST ? '&'.http_build_query($_REQUEST, '', '&') : ''), __PS_BASE_URI__, null, 'HTTP/1.1 301 Moved Permanently');
}
if (isset($_POST['x_cod_respuesta']))
{	
	global $smarty; 
        $payco->PaymentSuccess($_POST['x_extra1'],$_POST['x_extra2'],$_POST['x_cod_respuesta']);				
	$medio_pago=$_POST['x_franchise'];
	switch($medio_pago)
	{
		case 'VS': $franquicia = "Visa";break;
		case 'MC': $franquicia = "MasterCard";break;
		case 'AM': $franquicia = "American Express";break;
		case 'DN': $franquicia = "Diners";break;
		case 'CR': $franquicia = "Credencial";break;
		case 'PSE': $franquicia = "PSE (Proveedor de Servicios Electr&oacute;nicos)";break;
		/*case 'DV': $franquicia = "Debito Visa";break;
		case 'DM': $franquicia = "Debito MasterCard";break;*/		
	}	
      
	/*Acentarpago($_POST['x_respuesta'],$_POST['x_id_factura']);	*/
	$smarty->assign(array('respuesta'=>$_POST['x_cod_respuesta'],
					'ref_venta'=>$_POST['x_id_factura'],
					'valor'=>$_POST['x_amount'],
					'moneda'=>$_POST['x_currency_code'],
					'fecha'=>$_POST['fecha_transaccion'],
					'numero_transaccion'=>$_POST['x_transaction_id'],
					'codaprovacion'=>$_POST['x_aproval_code'],										
					'refpayco'=>$_POST['x_ref_payco'],
					'franquicia'=>$franquicia,
					'mensaje'=>$_POST['x_response_reason_text']));
	$smarty->display( dirname(__FILE__) . '/tpl/respuesta.tpl' );
        
 
  
}
  include( '../../footer.php' );


 

?>
