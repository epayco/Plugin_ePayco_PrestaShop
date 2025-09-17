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

class PaycoResultModuleFrontController extends ModuleFrontController
{   

    public $ssl = true;
    public $display_column_left = true;
    public $display_column_right = true;

    public function initContent()
    { 
        parent::initContent();  
       
    }

    public function postProcess()
    {
        $payco = new Payco();
        $history = Tools::getHttpHost(true).__PS_BASE_URI__.'index.php?controller=history';
        //$payco->PaymentReturnOnpage();
        $this->context->smarty->assign(array(
            'base_url'=> Tools::getHttpHost(true).__PS_BASE_URI__,
            'history' => $history
            )
        );
        $this->setTemplate('module:payco/views/templates/front/response.tpl');
    }
    
}
