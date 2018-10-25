<?php

	class CreditCard_Order
	{
		public static function isCreditCardOrder($id_order)
		{
			$db = Db::getInstance();
			$result = $db->getRow('
				SELECT `id_order` FROM `'._DB_PREFIX_.'creditcard_order`
				WHERE `id_order` = "'.intval($id_order).'"');
	
			return intval($result["id_order"]) != 0 ? true : false;
			
		}

		public static function removeDataString($id_order)
		{
			$db = Db::getInstance();
			$result = $db->execute('
				DELETE FROM `'._DB_PREFIX_.'creditcard_order`
				WHERE `id_order` = "'.intval($id_order).'"');
		}

		public static function addDataString($id_order, $transid, $transdate)
		{
			$db = Db::getInstance();
			$result = $db->execute('
			INSERT INTO `'._DB_PREFIX_.'creditcard_order`
			( `id_order`, `data_string` , `trans_date` )
			VALUES
			("'.intval($id_order).'","'.$transid.'","'.$transdate.'")');
		}

		public static function getDataString($id_order)
		{
			$db = Db::getInstance();
			$result = $db->execute('
			SELECT `data_string` FROM `'._DB_PREFIX_.'creditcard_order`
			WHERE `id_order` ="'.intval($id_order).'";');
			return $result[0]['data_string'];
		}

		public static function getTransactionID($id_order)
		{
			$db = Db::getInstance();
			$result = $db->execute('
			SELECT `data_string` FROM `'._DB_PREFIX_.'creditcard_order`
			WHERE `id_order` ="'.intval($id_order).'";');
			return $result[0]['data_string'];
		}

		public static function getTransactionDate($id_order)
		{
			$db = Db::getInstance();
			$result = $db->execute('
			SELECT `trans_date` FROM `'._DB_PREFIX_.'creditcard_order`
			WHERE `id_order` ="'.intval($id_order).'";');
			return $result[0]['trans_date'];
		}
		
		public static function setup()
		{	


			$db = Db::getInstance();

			//Create table to store credit card data
			$db->execute("CREATE TABLE `"._DB_PREFIX_."creditcard_order` (
				`id_record` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`id_order` INT NOT NULL ,
				`data_string` TEXT NOT NULL ,
				`trans_date` TEXT NOT NULL ,
				`date_add` DATE ,
				`date_upd` DATE
				) ENGINE = MYISAM ");
			return true;
		}
		
		public static function remove()
		{	
			$db = Db::getInstance();
			$db->execute("DROP TABLE IF EXISTS `"._DB_PREFIX_."creditcard_order`");
			return true;
		}
		
	}

?>