<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PaycoReturnModuleFrontController extends ModuleFrontController
{

	/**
	 * @see FrontController::postProcess()
	*/
	public function postProcess()
	{
		$payco = new Payco();
		if (isset($_REQUEST['x_cod_response']))
		{	
			$extra1=$_REQUEST['x_extra1'];
			$response=$_REQUEST['x_cod_response'];
			$referencia=$_REQUEST['x_ref_payco'];
			$transid=$_REQUEST['x_transaction_id'];
			$amount=$_REQUEST['x_amount'];
			$currency=$_REQUEST['x_currency_code'];
			$signature=$_REQUEST['x_signature'];
			$textMode = trim($_REQUEST['x_test_request']);
			$x_cod_transaction_state= trim($_REQUEST['x_cod_transaction_state']);
			$x_approval_code = trim($_REQUEST['x_approval_code']);
		    $payco->PaymentSuccess($extra1,$response,$referencia,$transid,$amount,$currency,$signature,false, $textMode,$x_cod_transaction_state,$x_approval_code);		
		}
	}

}