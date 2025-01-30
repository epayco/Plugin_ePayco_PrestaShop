{*
* 2007-2024 PrestaShop
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
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Alert -->
{if $message != ''}
    <div class='alert {$alert|escape:'html':'UTF-8'} alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
        {$message|escape:'html':'UTF-8'}
    </div>
{/if}


<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a href="#template_1" role="tab" data-toggle="tab" id="a_template_1">{l s='Set Up ePayco' mod='payco'}</a></li>
    <li class="ep-plugin-version"><a>{l s='Current version:' mod='payco'} <span>v{$ep_version|escape:'html':'UTF-8'}</span></a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="template_1">{include file='./template_1.tpl'}</div>
</div>

<!-- JavaScript -->
<script type="text/javascript">
    window.onload = function() {
        var element = document.querySelectorAll("#module_form");
        for (var i=0; i < element.length; i++) {
            element[i].id = "module_form_" + i;
        }

        // ----- credentials form ----- //
        var form_credentials_prepend = document.createElement("div");
        var form_credentials = document.querySelector("#module_form_0 .panel .form-wrapper");

        form_credentials_prepend.innerHTML = "<div class='row'>\
            <div class='col-md-12'>\
                <h4 class='ep-title-checkout-body'>{l s='Enter your credentials' mod='payco'}</h4>\
            </div>\
        </div>\
        form_credentials.insertBefore(form_credentials_prepend, form_credentials.firstChild);

        for (i=0; i < form_store_group.length; i++) {
            if(i == 1){
                form_store_group[i].insertAdjacentHTML('afterend', form_store_append);
            }
            else if(i == 2){
                form_store_group[i].querySelector("p").style.width = "400px";
            }
        }
    }

    //Ticket payments
    function completeTicketCheckbox(){
        var ticketCheck = document.getElementById("checkmeticket").checked;
        var ticketInputs = document.querySelectorAll(".payment-ticket-checkbox");
        for (var i=0; i < ticketInputs.length; i++) {
            if(ticketCheck == true){
                ticketInputs[i].checked = true;
            }
            else{
                ticketInputs[i].checked = false;
            }
        }
    }


    //Banner button
    function getCheckoutAnchor(tab, template, checkout) {
        var containerTab = document.getElementById(tab);
        var templateTab = document.getElementById(template);
        templateTab.click();

        if (containerTab) {
            containerTab.click();
            document.getElementById(checkout).scrollIntoView();
        }
    }
</script>
