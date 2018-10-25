<?php

/**
 * Resuelve el estado de las transacciones pendientes.
 * Programe un CRON (*nix) o una tarea (Windows) para que cada 5 minutos ejecute este script.
 */
/* Autorizacion */

// datos de conexion al motor de base de datos de su comercio


$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
Tools::displayFileAsDeprecated();
include(dirname(__FILE__).'/payco.php');

$link=FALSE;
$host = _DB_SERVER_;
$dbName = _DB_NAME_;
$dbPrefix = _DB_PREFIX_;
$dbPass = _DB_PASSWD_;
$dbUser = _DB_USER_;

$conexion = mysql_connect($host, $dbUser, $dbPass);
if (!$conexion) {
    die('Error bd: ');
}
else{
mysql_select_db($dbName, $conexion);        
}

date_default_timezone_set('America/Bogota');



try {
    
    // $query = 'SELECT * FROM pago WHERE estado="Pendiente" and fecha BETWEEN  "' . $minutos_antes . '" AND "' . $ahora . '"';
    $fecha=date('Y-m-d');
    $sql="Select* from ".$dbPrefix."orders where date_add like'%".$fecha."%' ";    
   
    $query=  mysql_query($sql,$conexion);
    $totaltran=  mysql_num_rows($query);
    $total_transacciones=array();
    $total_transacciones_ev1 = array();
    $url='http://secure.payco.co/wspayment/service.php?wsdl';
   
    $ws = new SoapClient($url, array('trace' => true));    
    $idcliente=1;    
    $p_key='0826fa87fd0cb01507203bff620b86daf9becc6b';
    $llave=  sha1($p_key.$idcliente);
    $payco = new Payco();
    
    if (count($totaltran)>0) {
        while ($row=mysql_fetch_array($query)){           
            $total_transacciones[] = $row; 
        }       
        mysql_close($conexion);        
        foreach ($total_transacciones as $orden) {            
        //Conectarme al webservice de payco
         
         $c = $ws->getTransactionInformation($idcliente,$llave,$fecha,$orden['reference']);
         $respuesta=$c[0];
         if($respuesta->ResultSuccess=='Failed'){
             
         }
         if($respuesta->ResultSuccess=='Success'){
             //Cambiar la transaccion a pagada            
             $payco->PaymentSuccess($orden['id_order'],$respuesta->TransactionItem->estado);
         }
         
        }
    }else{
        echo 'No se encontraron registros';
    }
}catch(Excepcion $ex){
    
}
?>
