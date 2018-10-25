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

{capture name=path}{l s='Payco payment.' mod='payco'}{/capture}
<div class="col-xs-12 col-sm-12 col-md-12">
	<div class="wrap">
		<h1 class="page-heading">{l s='RESUMEN DEL PEDIDO' mod='payco'}</h1>
		{assign var='current_step' value='payment'}
		{include file="$tpl_dir./order-steps.tpl"}
		{if $nbProducts <= 0}
			<p class="warning" style="text-align: center; font-size: 16px;">{l s='Your shopping cart is empty.' mod='payco'}</p>
		{else}
		<form action="{$link->getModuleLink('payco', 'validation', [], true)|escape:'html'}" method="post">
			<div class="box cheque-box">
				<h3 class="page-subheading" style="text-align: center; font-size: 10px;">
					<img src="boton.png" alt="{l s='ePayco' mod='payco'}"/>
					<div>
						{l s='Ha elegido pagar con ePayco.' mod='payco'}
					</div>
				</h3>
				<div>
					<table style="width: 100%;">
						<tr>
							<td style="border: solid 1px; text-align: center;"  colspan="2">
								<b>{l s='Resumén de su pedido' mod='payco'}</b>
							<td>
						</tr>
						<tr>
							<td style="border: solid 1px; text-align: center;">
								{l s='El importe total de su pedido es' mod='payco'}
							</td>
							<td style="border: solid 1px;text-align: center;">
								<span id="amount" class="price">{displayPrice price=$total}</span>
									{if $use_taxes == 1}
										{l s='(IVA incluído)' mod='payco'}
									{/if}
							</td>
						</tr>
						<tr>
							<td style="border: solid 1px; text-align: center;" colspan="2">
								<b>{l s='Por favor, confirme su pedido haciendo clic en CONFIRMO MI PEDIDO' mod='payco'}.</b>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="cart_navigation" class="cart_navigation clearfix">
				<input type="submit"
					style="background: #F0943E; color: #FFFFFF; font-size: 16px;"
					value="{l s='CONFIRMO MI PEDIDO' mod='payco'}"
					class="button btn btn-default pull-right"/>
			</div>
		</form>
	</div>
</div>
{/if}