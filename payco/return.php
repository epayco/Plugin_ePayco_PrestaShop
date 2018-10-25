<?php
echo "hola mundo de afuera";
var_export($_REQUEST);

//$useSSL = true;
/*include(dirname(__FILE__).'/../../config/config.inc.php');
Tools::displayFileAsDeprecated();
include(dirname(__FILE__).'/payco.php');
//$context = Context::getContext();
include( '../../header.php' );
$payco = new Payco();
if (isset($_REQUEST['x_cod_respuesta']))
{	
	
        $payco->PaymentSuccess($_REQUEST['x_extra1'],$_REQUEST['x_extra2'],$_REQUEST['x_cod_respuesta']);			
	//Tools::redirect('index.php?controller=respuestaev1'.($_REQUEST ? '&'.http_build_query($_REQUEST, '', '&') : ''), __PS_BASE_URI__, null, 'HTTP/1.1 301 Moved Permanently');
}
if (isset($_REQUEST['x_cod_respuesta']))
{	
	global $smarty; 
        $payco->PaymentSuccess($_REQUEST['x_extra1'],$_REQUEST['x_extra2'],$_REQUEST['x_cod_respuesta']);				
	$medio_pago=$_POST['x_franchise'];
	switch($medio_pago)
	{
		case 'VS': $franquicia = "Visa";break;
		case 'MC': $franquicia = "MasterCard";break;
		case 'AM': $franquicia = "American Express";break;
		case 'DN': $franquicia = "Diners";break;
		case 'CR': $franquicia = "Credencial";break;
		case 'PSE': $franquicia = "PSE (Proveedor de Servicios Electr&oacute;nicos)";break;
		//case 'DV': $franquicia = "Debito Visa";break;
		//case 'DM': $franquicia = "Debito MasterCard";break;		
	}	
      
	//Acentarpago($_POST['x_respuesta'],$_POST['x_id_factura']);	
	$smarty->assign(array('respuesta'=>$_REQUEST['x_cod_respuesta'],
					'ref_venta'=>$_REQUEST['x_id_factura'],
					'valor'=>$_REQUEST['x_amount'],
					'moneda'=>$_REQUEST['x_currency_code'],
					'fecha'=>$_REQUEST['fecha_transaccion'],
					'numero_transaccion'=>$_REQUEST['x_transaction_id'],
					'codaprovacion'=>$_REQUEST['x_aproval_code'],										
					'refpayco'=>$_REQUEST['x_ref_payco'],
					'franquicia'=>$franquicia,
					'mensaje'=>$_REQUEST['x_response_reason_text']));
	$smarty->display( dirname(__FILE__) . '/tpl/respuesta.tpl' ); 
}
  include( '../../footer.php' );*/
?>
