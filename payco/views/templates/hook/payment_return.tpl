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

<div class="loader-container">
    <div class="loading"></div>
</div>
<p style="text-align: center;" class="epayco-title">
    <span class="animated-points">Cargando métodos de pago</span>
   <br><small class="epayco-subtitle"> Si no se cargan automáticamente, de clic en el botón "Pagar con ePayco"</small>
</p>
<center>
<a id="btn_epayco" href="#">
    <img src="{$url_button|escape:'htmlall':'UTF-8'}">
</a>
</center>
<form id="epayco_form" style="text-align: center;">
    <script src="https://epayco-checkout-testing.s3.amazonaws.com/checkout.preprod.js"></script>
     <script language="JavaScript" type="text/javascript">
        var handler = ePayco.checkout.configure({
            key: "{$public_key}",
            test: "{$merchanttest}"
        })
        var date = new Date().getTime();
        var extras_epayco = {
            extra5:"p23"
        };
        var data = {
            name: "{$descripcion}",
            description: "{$descripcion}",
            invoice: "{$refVenta|escape:'htmlall':'UTF-8'}",
            currency: "{$currency|lower|escape:'htmlall':'UTF-8'}",
            amount: "{$total|escape:'htmlall':'UTF-8'}".toString(),
            tax_base: "{$baseDevolucionIva|escape:'htmlall':'UTF-8'}".toString(),
            tax: "{$iva|escape:'htmlall':'UTF-8'}".toString(),
            taxIco: "0",
            country: "{$iso|lower|escape:'htmlall':'UTF-8'}",
            lang: "{$lang|escape:'htmlall':'UTF-8'}",
            external: "{$external|escape:'htmlall':'UTF-8'}",
            confirmation: "{$p_url_confirmation|unescape: 'html' nofilter}",
            response: "{$p_url_response|unescape: 'html' nofilter}",
            name_billing: "{$p_billing_name|escape:'htmlall':'UTF-8'} {$p_billing_last_name|escape:'htmlall':'UTF-8'}",
            address_billing: "{$p_billing_address|escape:'htmlall':'UTF-8'}",
            email_billing: "{$p_billing_email|escape:'htmlall':'UTF-8'}",
            extra1: "{$extra1|escape:'htmlall':'UTF-8'}",
            extra2: "{$extra2|escape:'htmlall':'UTF-8'}",
            extra3: "{$refVenta|escape:'htmlall':'UTF-8'}",
            autoclick: "true",
            ip:  "{$ip|escape:'htmlall':'UTF-8'}",
            test: "{$merchanttest|escape:'htmlall':'UTF-8'}".toString(),
            extras_epayco: extras_epayco
        }
        const apiKey = "{$public_key}";
        const privateKey = "{$private_key}";
        var openChekout = function () {
            if(localStorage.getItem("invoicePayment") == null){
            localStorage.setItem("invoicePayment", data.invoice);
                makePayment(privateKey,apiKey,data, data.external == "true"?true:false)
            }else{
                if(localStorage.getItem("invoicePayment") != data.invoice){
                    localStorage.removeItem("invoicePayment");
                    localStorage.setItem("invoicePayment", data.invoice);
                        makePayment(privateKey,apiKey,data, data.external == "true"?true:false)
                }else{
                    makePayment(privateKey,apiKey,data, data.external == "true"?true:false)
                }
            }
        }
        var makePayment = function (privatekey, apikey, info, external) {
            const headers = { "Content-Type": "application/json" } ;
            headers["privatekey"] = privatekey;
            headers["apikey"] = apikey;
            var payment =   function (){
                return  fetch("https://cms.epayco.io/checkout/payment/session", {
                    method: "POST",
                    body: JSON.stringify(info),
                    headers
                })
                    .then(res =>  res.json())
                    .catch(err => err);
            }
            payment()
                .then(session => {
                    if(session.data.sessionId != undefined){
                        localStorage.removeItem("sessionPayment");
                        localStorage.setItem("sessionPayment", session.data.sessionId);
                        const handlerNew = window.ePayco.checkout.configure({
                            sessionId: session.data.sessionId,
                            external: external,
                        });
                        handlerNew.openNew()
                    }
                })
                .catch(error => {
                    error.message;
                });
        }
        var bntPagar = document.getElementById("btn_epayco");
        bntPagar.addEventListener("click", openChekout);
        openChekout()  
    </script>
</form> 
<script language="Javascript">
    const app = document.getElementById("epayco_form");
    window.onload = function() {
        document.addEventListener("contextmenu", function(e){
        e.preventDefault();
        }, false);
    } 
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).keydown(function (event) {
        if (event.keyCode == 123) {
            return false;
        } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) {        
            return false;
        }
    });
</script>
{else}
<p class="warning">
  {l s='Hemos notado un problema con tu orden, si crees que es un error puedes contactar a nuestro departamento de Soporte' mod='payco'}
  {l s='' mod='payco'}.
</p>
{/if}
