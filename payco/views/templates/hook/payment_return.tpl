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
    <script src="https://epayco-checkout-testing.s3.us-east-1.amazonaws.com/checkout.preprod.js"></script>
     <script>
        var handler = ePayco.checkout.configure({
            key: "{$public_key}",
            test: "{$test}"
        })
        var date = new Date().getTime();
        var extras_epayco = {
            extra5:"P23"
        };
        const params = JSON.parse(atob("{$checkout}"));
        let {
            description,
            invoice,
            currency,
            amount,
            tax_base,
            tax,
            taxIco,
            country,
            lang,
            external,
            confirmation,
            response,
            name_billing,
            address_billing,
            email_billing,
            mobilephone_billing,
            autoclick,
            ip,
            test,
            extra1,
            extra2,
            bearerToken
        } = params;
        var data = {
            name: description,
            description: description,
            invoice,
            currency,
            amount,
            tax_base:tax_base.toString(),
            tax: tax.toString(),
            taxIco: taxIco.toString(),
            country,
            lang,
            external,
            confirmation,
            response,
            name_billing,
            address_billing,
            email_billing,
            extra1: (extra1 && extra1.toString().trim().length > 0) ? extra1.toString() : "N/A", 
            extra2: (extra2 && extra2.toString().trim().length > 0) ? extra2.toString() : "N/A",
            extra3:invoice,
            autoclick: "true",
            ip,
            test:test.toString(),
            method_confirmation:"POST",
            extras_epayco:extras_epayco,
            checkout_version:2
        }
        const apiKey = "{$public_key}";
        const privateKey = "{$private_key}";
        const externalCheckout = data.external == "true"?true:false;
        var openChekout = function () {
            //handler.open(data);
            makePayment(bearerToken,data, externalCheckout)
        }
        var makePayment = function (bearerToken, info, external) {
            const headers = { "Content-Type": "application/json" } ;
            headers["Authorization"] = "Bearer "+bearerToken;
            var payment =   function (){
                return  fetch("https://eks-apify-service.epayco.io/payment/session/create", {
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
                        const handlerNew = window.ePayco.checkout.configure({
                            sessionId: session.data.sessionId,
                            external: external,
                        });
                        handlerNew.openNew()
                    }else{
                        handler.open(data);
                    }
                })
                .catch(error => {
                    console.error(error.message);
                    handler.open(data);
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
