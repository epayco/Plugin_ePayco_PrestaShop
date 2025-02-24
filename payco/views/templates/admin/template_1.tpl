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
<div class="ep-settings">
    <div class="ep-settings-header">
        <div class="ep-settings-header-img"></div>
        <div>
            <div class="ep-settings-header-logo"></div>
                <div class="ep-settings-header-title">
                    <p style="font-weight: 900;color: black;margin:0px 50px;font-size: 19px;line-height: 20px">{l s='OPTIMIZE YOUR STORE WITH THE' mod='payco'}</p>
                    <p style="font-weight: 900;color: #DF5C1F;margin:0px 50px;font-size: 29px">{l s='ePayco PLUGIN' mod='payco'}</p>
                    <p class="ep-settings-context">{l s='Facilitate payments in your online store with the ePayco plugin. With this integration, you will be able to offer your customers a fast, secure and frictionless payment experience.' mod='payco'}</p>
                </div>
        </div>
    </div>
    <p style="font-size: 24px;margin: 10px 0px 10px;font-weight: 900;"> {l s='Configuration' mod='payco'}</p>
        
    <!-- forms rendered via class from payco.php -->
    <div class="ep-settings-credentials">
        <div class="ep-settings-title-align" id="ep-settings-step-one">
            <div class="ep-settings-title-container">
                <span class="ep-settings-font-color ep-settings-title-blocks ep-settings-margin-right">
                    1. {l s='Enter your credentials to integrate your store with ePayco' mod='payco'}
                </span>
                <img class="ep-settings-margin-left ep-settings-margin-right" id="ep-settings-icon-credentials">
            </div>
            <div class="ep-settings-title-container ep-settings-margin-left">
                <img class="ep-settings-icon-open" id="ep-credentials-arrow-up">
            </div>

        </div>

    </div>
    {html_entity_decode($credentials|escape:'html':'UTF-8')}

        <div class="ep-settings-payment" style="margin: 10px 0px">
            <div id="ep-settings-step-three" class="ep-settings-title-align">
                <div class="ep-settings-title-container">
                    <span class="ep-settings-font-color ep-settings-title-blocks ep-settings-margin-right">
                        2. {l s='Activate and set up payment methods' mod='payco'}                
                    </span>
                    <img class="ep-settings-margin-left ep-settings-margin-right" id="ep-settings-icon-payment">
                </div>
                <div class="ep-settings-title-container ep-settings-margin-left">
                    <img class="ep-settings-icon-open" id="ep-payments-arrow-up">
                </div>
            </div>
            <div id="ep-step-3" class="ep-settings-block-align-top dropdown-hidden">
                <p id="ep-payment" class="ep-settings-subtitle-font-size ep-settings-title-color" hidden="hidden"></p>
            </div>
        </div>

        <!-- Nav tabs checkouts -->
        <!--<ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#standard_checkout" role="tab" data-toggle="tab">{l s='Checkout' mod='payco'}</a></li>
            <li><a href="#creditcard_checkout" role="tab" data-toggle="tab" id="tab-custom">{l s='Credit Card Checkout' mod='payco'}</a></li>
            <li><a href="#pse_checkout" role="tab" data-toggle="tab">{l s='Pse checkout' mod='payco'}</a></li>
            <li><a href="#ticket_checkout" role="tab" data-toggle="tab">{l s='Ticket checkout' mod='payco'}</a></li>
        </ul>-->

        <!-- Tab panes checkouts -->
        <div class="tab-content">
            <div class="tab-pane" id="standard_checkout">{html_entity_decode($standard_form|escape:'html':'UTF-8')}</div>
            <div class="tab-pane" id="creditcard_checkout">{html_entity_decode($creditcard_form|escape:'html':'UTF-8')}</div>
            <div class="tab-pane" id="pse_checkout">{html_entity_decode($pse_form|escape:'html':'UTF-8')}</div>
            <div class="tab-pane" id="ticket_checkout">{html_entity_decode($ticket_form|escape:'html':'UTF-8')}</div>
        </div>

    <br>

</div>

<!-- JavaScript -->
<!-- <script type="text/javascript" src="{$module_dir}views/js/admin/ep-admin-settings.min.js"></script> -->
<script type="text/javascript">
    window.onload = function() {
        console.log("template_1 loaded");
        }
</script>        
