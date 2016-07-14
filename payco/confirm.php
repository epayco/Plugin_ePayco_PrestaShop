<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/pagosonline.php');
include ('/../../config/settings.inc.php');

//recepción de parámetros
$numero = count($_POST);
$tags = array_keys($_POST);
$valores = array_values($_POST);

for($i=0; $i<$numero; $i++)
	{
		$$tags[$i]=$valores[$i];
	}


//validación firma digital

$llavelocal = Configuration::get('PAGOSONLINE_KEYENCRIPT');
$clave_sin= $llavelocal."~".$usuario_id."~".$ref_venta."~".$valor."~".$moneda."~".$estado_pol;
$firma_local = md5($clave_sin);


if (strtoupper($firma_local) == $firma)	
	{
		//actualización BD
		
		$host = _DB_SERVER_;
		$dbName = _DB_NAME_;
		$dbPrefix = _DB_PREFIX_;
		$dbPass = _DB_PASSWD_;
		$dbUser = _DB_USER_;
		$date = date("Y-m-d H:i:s",strtotime("now"));

		$conexion = mysql_connect($host, $dbUser, $dbPass);
		if (!$conexion) {
			die('Error de conexión DB: ');
		}
		else 
			mysql_select_db($dbName, $conexion);
		
		if ($estado_pol == 4)
			$id_order_state = 2;
		else if ($estado_pol ==5 || $estado_pol ==6 )
			$id_order_state = 6;
		
		if (isset($id_order_state) && isset($ref_venta))
			{
				
			$sql="SELECT MAX(id_order_history) FROM ".$dbPrefix."order_history";
			$result=mysql_query($sql);
			if (!$result) 
			{
				die(' Invalid query: ' . mysql_error());
				
			}
			$id_max=mysql_fetch_array($result);
			$id_new=intval($id_max[0])+1;
                        
                        $ref_venta_array = array();
                        $ref_venta_array = explode("-", $ref_venta);
                        $ref_venta = $ref_venta_array[0];

			$sql = "INSERT INTO ".$dbPrefix."order_history (`id_order_history`, `id_employee`, `id_order`, `id_order_state`, `date_add`) ";
			$sql .=	"VALUES (".$id_new.", '0','".$ref_venta."', '".$id_order_state."', '".$date."')";
			$result=mysql_query($sql);
						
			if (!$result) 
				die('No se puede insertar');
			}
		
		mysql_close($conexion);
	}
	
else 
	echo "FIRMA DIGITAL NO VALIDA";

?>
