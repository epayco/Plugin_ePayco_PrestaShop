<?php

/**
 * Defines statuses which will trigger the 
 * deletion of credit data from the database
 */

class CreditCard_OrderState extends ObjectModel
{
	
	public function getOrderStates($ids_only = false)
	{
		global $cookie;
		
		$returnStates = array();
		
		$states = OrderState::getOrderStates($cookie->id_lang);
		
		
		$states_deleteon = explode(',',Configuration::get('CREDITCARD_DATA_OS_DELETEON'));
		
		$id_initial_state = Configuration::get('CREDITCARD_ORDERSTATE_WAITING');
		
		foreach($states as $k => $state)
		{
			if($ids_only)
			{
				$returnStates[] = $state['id_order_state'];
			}
			else
			{
				$state['delete_on'] = in_array($state['id_order_state'], $states_deleteon);
				$returnStates[] = $state;
			}
		}
		return $returnStates;
	}
	
	public function isDeleteOnState($id)
	{
		if(in_array($id, explode(',', Configuration::get('CREDITCARD_DATA_OS_DELETEON'))))
			return true;
		else
			return false;
	}
	
	public function getInitialState()
	{
		return Configuration::get('CREDITCARD_DATA_OS_INITIAL');
	}
	
	
	public function updateStates($id_initial_state, $delete_on)
	{
		Configuration::updateValue('CREDITCARD_DATA_OS_DELETEON', implode(',', $delete_on));
		Configuration::updateValue('CREDITCARD_DATA_OS_INITIAL', intval($id_initial_state));
		return true;
	}
	
	public function setup()
	{		
		if(!Configuration::get('CREDITCARD_DATA_OS_INITIAL') > 0)
		{
		
			$os = new OrderState();
			$os->name = array_fill(0,10,$this->l("Payco - Esperando Validacion"));
			$os->send_mail = 1;
			$os->template = array_fill(0,10,'order_conf');
			$os->invoice = 0;
			$os->color = "#FFFFAA";
			$os->unremovable = false;
			$os->logable = 0;		
			$os->add();
			Configuration::updateValue('CREDITCARD_DATA_OS_INITIAL', $os->id);



			$os2 = new OrderState();
			$os2->name = array_fill(0,9,$this->l("Payco - Transaccion Aceptada"));
			$os2->send_mail = 1;
			$os2->template = array_fill(0,9,'payco');
			$os2->invoice = 0;
			$os2->color = "#30AF49";
			$os2->unremovable = false;
			$os2->logable = 0;		
			$os2->add();


			$os3 = new OrderState();
			$os3->name = array_fill(0,11,$this->l("Payco - Transaccion Rechazada"));
			$os3->send_mail = 1;
			$os3->template = array_fill(0,11,'payco2');
			$os3->invoice = 0;
			$os3->color = "#FF0202";
			$os3->unremovable = false;
			$os3->logable = 0;		
			$os3->add();




			/*$db = Db::getInstance();
			$result = $db->Execute('
			INSERT INTO `'._DB_PREFIX_.'order_state`
			( `invoice`, `send_email` , `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`  )
			VALUES
			(0,1,"payco rechaced","#FF0202",1,0,0,0,0,0,0)');


			
			$result = $db->getRow('
				SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state`
				WHERE `module_name` = "payco rechaced"');

			$id1 = $result["id_order_state"];


			$db = Db::getInstance();
			$result = $db->Execute('
			INSERT INTO `'._DB_PREFIX_.'order_state`
			( `invoice`, `send_email` , `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`  )
			VALUES
			(0,1,"payco acepted","#30AF49",1,0,0,0,0,0,0)');

	
			$result2 = $db->getRow('
				SELECT `id_order_state` FROM `'._DB_PREFIX_.'order_state`
				WHERE `module_name` = "payco acepted"');

			$id2 = $result2["id_order_state"];
	

			$db = Db::getInstance();
			$result3 = $db->Execute('
			INSERT INTO `'._DB_PREFIX_.'order_state_lang`
			(`id_order_state`, `id_lang`, `name` , `template`  )
			VALUES
			("'.$id1.'",1,"Payco - Transaccion Rechazada","payco_rechaced")');

			$db = Db::getInstance();
			$result4 = $db->Execute('
			INSERT INTO `'._DB_PREFIX_.'order_state_lang`
			(`id_order_state`, `id_lang`, `name` , `template`  )
			VALUES
			("'.$id2.'",1,"Payco - Transaccion Aceptada","payco_acepted")');
*/
		
		
		}
		if(!Configuration::get('CREDITCARD_DATA_OS_DELETEON'))
		{				
			Configuration::updateValue('CREDITCARD_DATA_OS_DELETEON','2');
		}
	}	
}

?>