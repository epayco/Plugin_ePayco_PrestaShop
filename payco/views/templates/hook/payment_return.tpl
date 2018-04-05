{*
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
*}
{if $status == 'ok'}

<p style="text-align: center;">
   Enviando a transacción de pago... si el pedido no se envia automaticamente de click en el botón "Pagar con ePayco"
</p>

<form id="epayco_form" style="text-align: center;">
    <script src="https://checkout.epayco.co/checkout.js"
        class="epayco-button"
        data-epayco-key="{$public_key}"
        data-epayco-amount="{$total|escape:'htmlall':'UTF-8'}"
        data-epayco-tax="{$tax|escape:'htmlall':'UTF-8'}"
        data-epayco-tax-base="{$base_tax|escape:'htmlall':'UTF-8'}"    
        data-epayco-name="ORDEN DE COMPRA # {$refVenta|escape:'htmlall':'UTF-8'}"
        data-epayco-description="ORDEN DE COMPRA # {$refVenta|escape:'htmlall':'UTF-8'}"
        data-epayco-currency="{$currency|lower|escape:'htmlall':'UTF-8'}"
        data-epayco-invoice="{$refVenta|escape:'htmlall':'UTF-8'}"
        data-epayco-country="{$iso|lower|escape:'htmlall':'UTF-8'}"
        data-epayco-test={$merchanttest}
        data-epayco-extra1="{$extra1|escape:'htmlall':'UTF-8'}",
        data-epayco-extra2="{$extra2|escape:'htmlall':'UTF-8'}",
        data-epayco-extra3="",
        data-epayco-external="{$external|escape:'htmlall':'UTF-8'}"
        data-epayco-response="{$p_url_response|unescape: 'html' nofilter}" 
        data-epayco-confirmation="{$p_url_response|unescape: 'html' nofilter}"
        data-epayco-email-billing="{$p_billing_email|escape:'htmlall':'UTF-8'}"
        data-epayco-name-billing="{$p_billing_name|escape:'htmlall':'UTF-8'} {$p_billing_last_name|escape:'htmlall':'UTF-8'}"
        data-epayco-address-billing="{$p_billing_address|escape:'htmlall':'UTF-8'}"
        data-epayco-lang="es"
        data-epayco-mobilephone-billing="{$p_billing_phone|escape:'htmlall':'UTF-8'}"
        >
    </script>
</form>

<script>
    setTimeout(function(){ 
       document.getElementsByClassName("epayco-button-render" )[0].click();
    }, 2500);
</script>

<!-- <div style="text-align: center;">
  
  Enviando a transacción de pago... si el pedido no se envia automaticamente de click en el botón "Pagar con ePayco"

  <a id="btn-pagar" href="#" onclick="open_checkout();"><img src="https://369969691f476073508a-60bf0867add971908d4f26a64519c2aa.ssl.cf5.rackcdn.com/btns/epayco/boton_de_cobro_epayco2.png" />


  </a>
</div>

<script type="text/javascript" src="https://checkout.epayco.co/checkout.js" > </script>
<script type="text/javascript" >

    
    var handler = ePayco.checkout.configure({
        key: "{$public_key}",
        test: {$merchanttest}
    })
    var data = { 
            amount: "{$total|escape:'htmlall':'UTF-8'}",
            base_tax:"{$base_tax|escape:'htmlall':'UTF-8'}",
            tax:"{$tax|escape:'htmlall':'UTF-8'}",
            name: "ORDEN DE COMPRA # {$refVenta|escape:'htmlall':'UTF-8'}",
            description: "ORDEN DE COMPRA # {$refVenta|escape:'htmlall':'UTF-8'}",
            currency: "{$currency|lower|escape:'htmlall':'UTF-8'}",
            country: "{$iso|lower|escape:'htmlall':'UTF-8'}",
            lang: "es",
            external:"{$external|escape:'htmlall':'UTF-8'}",
            extra1:"{$extra1|escape:'htmlall':'UTF-8'}",
            extra2:"{$extra2|escape:'htmlall':'UTF-8'}",
            extra3:"",
            invoice: "{$refVenta|escape:'htmlall':'UTF-8'}",
            confirmation: "{$p_url_response|unescape: 'html' nofilter}",
            response: "{$p_url_response|unescape: 'html' nofilter}",
            email_billing: "{$p_billing_email|escape:'htmlall':'UTF-8'}",
            name_billing: "{$p_billing_name|escape:'htmlall':'UTF-8'} {$p_billing_last_name|escape:'htmlall':'UTF-8'}",
            address_billing: "{$p_billing_address|escape:'htmlall':'UTF-8'}",
            phone_billing:"{$p_billing_phone|escape:'htmlall':'UTF-8'}"
        }
        console.log(data);
        setTimeout(function(){ 
            handler.open(data);
         }, 2000);


        function open_checkout(){
            handler.open(data);
        }


     </script> -->

{else}
<p class="warning">
  {l s='Hemos notado un problema con tu orden, si crees que es un error puedes contactar a nuestro departamento de Soporte' mod='payco'}
  {l s='' mod='payco'}.
</p>
{/if}