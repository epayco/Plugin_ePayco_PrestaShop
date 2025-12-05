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
    <script src="https://epayco-checkout-testing.s3.amazonaws.com/checkout.preprod-v2.js"></script>
    <script>
        const params = JSON.parse(atob("{$checkout}"));
        let {
            sessionId,
            type,
            test
        } = params;
        const checkout = ePayco.checkout.configure({
            sessionId: sessionId,
            type: type,
            test: {$test}
        });
        var openChekout = function () {
            checkout.open();
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
<div style="
        display: flex;
        align-items: center;
        flex-direction: column;
    ">
    <div>
    <img style="width: 80px;" src="https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/warning.png" alt="" />
    </div>
    <div 
    style="text-align: center;font-size: large;font-weight: 900;">
        <p class="warning">
            {l s='Hemos notado un problema con tu orden, solicitamos contactar a nuestro departamento de Soporte' mod='payco'}
            {l s='{$errorMessage}' mod='payco'}.
        </p>
    </div>
</div>
{/if}

