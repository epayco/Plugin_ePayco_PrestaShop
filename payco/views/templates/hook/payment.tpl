{*
* 2007-2012 PrestaShop
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
*  @author ePayco SAS <desarrollo@epayco.co>
*  @copyright  2011-2017 ePayco SAS
*  @version  Release: $Revision: 100 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="row">
	<div class="col-xs-12 col-md-12">
		<p class="payment_module">
			<a class="bankwire" 
				 href="{$link->getModuleLink('payco', 'payment')|escape:'html'}" 
				 title="{l s='ePayco Reciba pagos online con tarjetas de credito, debito PSE, Efectivo y SafetyPay en menos de 24 Horas con multiples herramientas.' mod='payco'}">	
				 <img src="https://multimedia.epayco.co/epayco-landing/btns/epayco-logo-fondo-claro-lite.png" />			
				{l s='ePayco' mod='payco'}&nbsp;
				<span style="font-size: 14px;">
					{l s='(Reciba pagos online con tarjetas de Crédito, Debito PSE, Efectivo y SafetyPay en menos de 24 Horas con multiples herramientas).' mod='payco'}
				</span>
			</a>
		</p>
	</div>
</div>