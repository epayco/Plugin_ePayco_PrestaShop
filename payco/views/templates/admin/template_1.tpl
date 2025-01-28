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

<div class="panel ep-panel-landing" style="padding: 0px !important;">
    <div class="ep-settings-header">
        <div class="ep-settings-header-img"></div>
        <div style="float: right; padding-right: 30px">
            <div class="ep-settings-header-logo"></div>
            <hr class="ep-settings-header-hr">
            <p class="ep-settings-header-title">{l s='Accept payment safely' mod='payco'} <br> {l s=' with ePayco' mod='payco'}</p>
        </div>
    </div>
</div>

<!-- forms rendered via class from payco.php -->
{html_entity_decode($credentials|escape:'html':'UTF-8')}

    <!-- Nav tabs checkouts -->
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#standard_checkout" role="tab" data-toggle="tab">{l s='Checkout' mod='payco'}</a></li>
        <li><a href="#creditcard_checkout" role="tab" data-toggle="tab" id="tab-custom">{l s='Credit Card Checkout' mod='payco'}</a></li>
        <li><a href="#pse_checkout" role="tab" data-toggle="tab">{l s='Pse checkout' mod='payco'}</a></li>
        <li><a href="#ticket_checkout" role="tab" data-toggle="tab">{l s='Ticket checkout' mod='payco'}</a></li>
    </ul>

    <!-- Tab panes checkouts -->
    <div class="tab-content">
        <div class="tab-pane active" id="standard_checkout">{html_entity_decode($standard_form|escape:'html':'UTF-8')}</div>
        <div class="tab-pane" id="creditcard_checkout">{html_entity_decode($creditcard_form|escape:'html':'UTF-8')}</div>
        <div class="tab-pane" id="pse_checkout">{html_entity_decode($pse_form|escape:'html':'UTF-8')}</div>
        <div class="tab-pane" id="ticket_checkout">{html_entity_decode($ticket_form|escape:'html':'UTF-8')}</div>
    </div>

<br>
