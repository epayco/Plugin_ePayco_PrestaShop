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
* @author PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2024 PrestaShop SA
* @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<form id="ep_creditcard_checkout"  method="post" action="{$redirect|escape:'htmlall':'UTF-8'}">
    <div class='ep-checkout-container'>
        <div class="ep-checkout-creditcard-container" style="max-width: 452px;margin: auto;">
            <div class="ep-checkout-creditcard-content">
                <div class="ep-checkout-creditcard-test-mode">
                    {if $payment_method_info["test"]}
                        <test-mode
                                title="{l s='Offline Methods in Test Mode' mod='payco'}"
                                description="You can test the flow to generate an invoice, but you cannot finalize the payment."
                                link-text="See the rules for the test mode."
                                link-src="">
                        </test-mode>
                        <div class="ep-test-mode-credit-card">
                            <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/icon-info.png" style="height: 25px" >
                            <div style="display: grid;grid-template-rows: repeat(2, 1fr);gap: 8px;">
                                <p>Por favor, utiliza la siguiente información de tarjeta de prueba en modo de prueba:</p>
                                <p style="font-weight: bold; color:black">Número: 4575 6231 8229 0326</p>
                                <div style="width: 50%;display: grid;grid-template-columns: repeat(3, 1fr);gap: 0px;padding: 0px">
                                    <p style="border-right: 1px solid #000 !important; padding: 0px 10px 0px 0px;color:black"><strong>MM</strong>:12</p>
                                    <p style="border-right: 1px solid #000 !important; padding: 0px 10px 0px;color:black"><strong>AA</strong>:2025</p>
                                    <p style="padding: 0px 10px 0px;color:black"><strong>CVV</strong>:123</p>
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
                <div style="margin-top: 10px; font-weight: bold; display: flex; align-items: center;">
                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 1.616V12.385C18 12.845 17.846 13.2293 17.538 13.538C17.23 13.8467 16.8457 14.0007 16.385 14H1.615C1.155 14 0.771 13.846 0.463 13.538C0.155 13.23 0.000666667 12.8453 0 12.384V1.616C0 1.15533 0.154333 0.771 0.463 0.463C0.771667 0.155 1.15567 0.000666667 1.615 0H16.385C16.845 0 17.229 0.154333 17.537 0.463C17.845 0.771667 17.9993 1.156 18 1.616ZM1 3.808H17V1.616C17 1.462 16.936 1.32067 16.808 1.192C16.68 1.06333 16.539 0.999333 16.385 1H1.615C1.46167 1 1.32067 1.064 1.192 1.192C1.06333 1.32 0.999333 1.46133 1 1.616V3.808ZM1 6.192V12.385C1 12.5383 1.064 12.6793 1.192 12.808C1.32 12.9367 1.461 13.0007 1.615 13H16.385C16.5383 13 16.6793 12.936 16.808 12.808C16.9367 12.68 17.0007 12.539 17 12.385V6.192H1Z" fill="black"/>
                    </svg>
                    <p style="margin-left: 10px;color: black; font-weight: bold !important;">{l s='Card data' mod='payco'}</p>
                </div>
                <div id="ep-custom-checkout-form-container" style="margin: 10px;">
                    <div class='ep-checkout-custom-card-form'>
                        <div class='ep-checkout-custom-card-row' id="ep-card-holder-div">
                            <input-name
                                    labelMessage="{l s='Name' mod='payco'}"
                                    helperMessage="{l s='Invalid name' mod='payco'}"
                                    placeholder="Ex: John Doe"
                                    inputName='epayco_creditcard[name]'
                                    flagError='epayco_creditcard[nameError]'
                                    validate=true
                                    hiddenId= "hidden-name-custom"
                            >
                            </input-name>
                        </div>

                        <div class='ep-checkout-custom-card-row'>
                            <input-card-number
                                    labelMessage="{l s='Card Number' mod='payco'}"
                                    helperMessage="{l s='Invalid  Card Number' mod='payco'}"
                                    placeholder="0000 0000 0000 0000"
                                    inputName='epayco_creditcard[card]'
                                    flagError='epayco_creditcard[cardError]'
                                    validate=true
                                    hiddenId= "ep-card-number-helper"
                            >
                            </input-card-number>
                        </div>

                        <div class='ep-checkout-custom-card-row ep-checkout-custom-dual-column-row'>
                            <div class='ep-checkout-custom-card-column'>
                                <input-card-expiration-date
                                        class="ep-checkout-custom-left-card-input"
                                        labelMessage="{l s='Expiration' mod='payco'}"
                                        helperMessage="{l s='Required data' mod='payco'}"
                                        placeholder="mm/yy"
                                        inputName='epayco_creditcard[expirationDate]'
                                        flagError='epayco_creditcard[expirationDateError]'
                                        validate=true
                                        hiddenId= "hidden-expiration-date-helper"
                                >
                                </input-card-expiration-date>
                            </div>

                            <div class='ep-checkout-custom-card-column'>
                                <input-card-security-code
                                        class="ep-checkout-custom-left-card-input"
                                        labelMessage="{l s='Security Code' mod='payco'}"
                                        helperMessage="{l s='Required data' mod='payco'}"
                                        placeholder="***"
                                        inputName='epayco_creditcard[securityCode]'
                                        flagError='epayco_creditcard[securityCodeError]'
                                        validate=true
                                        hiddenId= "hidden-security-code-helper"
                                >
                                </input-card-security-code>
                            </div>
                            <div class='ep-checkout-custom-card-column'>
                                <input-installment
                                        name="epayco_creditcard[installmet]"
                                        label="{l s='Fees' mod='payco'}"
                                        optional="false"
                                        options="{$payment_method_info["fees"]}"
                                >
                                </input-installment>
                            </div>
                        </div>
                    </div>

                </div>
                <hr>
                <div style="margin-top: 10px; font-weight: bold; display: flex; align-items: center;">
                    <svg width="21" height="16" viewBox="0 0 21 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.013 8.528H17.7695V7.38514H13.013V8.528ZM13.013 5.36229H17.7695V4.21943H13.013V5.36229ZM3.2305 11.7806H10.9492V11.5909C10.9492 10.9242 10.6069 10.408 9.9225 10.0423C9.23806 9.67657 8.29383 9.49371 7.08983 9.49371C5.88583 9.49371 4.94122 9.67657 4.256 10.0423C3.57078 10.408 3.22894 10.9242 3.2305 11.5909V11.7806ZM7.08983 7.648C7.58217 7.648 7.99672 7.48305 8.3335 7.15314C8.67105 6.82247 8.83983 6.416 8.83983 5.93371C8.83983 5.45143 8.67105 5.04533 8.3335 4.71543C7.99594 4.38552 7.58139 4.22019 7.08983 4.21943C6.59828 4.21867 6.18372 4.384 5.84617 4.71543C5.50861 5.04686 5.33983 5.45295 5.33983 5.93371C5.33983 6.41448 5.50861 6.82095 5.84617 7.15314C6.18372 7.48533 6.59828 7.65028 7.08983 7.648ZM1.88533 16C1.34789 16 0.8995 15.824 0.540167 15.472C0.180833 15.12 0.000777778 14.6804 0 14.1531V1.84686C0 1.32038 0.180056 0.881142 0.540167 0.529143C0.900278 0.177143 1.34828 0.000761905 1.88417 0H19.1158C19.6525 0 20.1005 0.176381 20.4598 0.529143C20.8192 0.881904 20.9992 1.32114 21 1.84686V14.1543C21 14.68 20.8199 15.1192 20.4598 15.472C20.0997 15.8248 19.6517 16.0008 19.1158 16H1.88533ZM1.88533 14.8571H19.1158C19.2947 14.8571 19.4592 14.784 19.6093 14.6377C19.7594 14.4914 19.8341 14.3299 19.8333 14.1531V1.84686C19.8333 1.67086 19.7587 1.50933 19.6093 1.36229C19.46 1.21524 19.2955 1.1421 19.1158 1.14286H1.88417C1.70528 1.14286 1.54078 1.216 1.39067 1.36229C1.24056 1.50857 1.16589 1.6701 1.16667 1.84686V14.1543C1.16667 14.3295 1.24133 14.4907 1.39067 14.6377C1.54 14.7848 1.7045 14.8579 1.88417 14.8571" fill="black"/>
                    </svg>
                    <p style="margin-left: 10px; color:black; font-weight: bold !important;">{l s='Customer data' mod='payco'}</p>
                </div>
                <div id="ep-custom-checkout-form-container" style="margin: 10px;">

                    <div class="ep-checkout-creditcard-input-document">
                        <input-document
                                label-message="{l s='Document' mod='payco'}"
                                helper-message="{l s='Invalid Document' mod='payco'}"
                                input-name='epayco_creditcard[document]'
                                hidden-id="documentType"
                                input-data-checkout="document_number"
                                select-id="documentType"
                                input-id="documentTypeNumber"
                                select-name="epayco_creditcard[documentType]"
                                select-data-checkout=document_type"
                                flag-error="documentTypeError"
                                documents='{$payment_method_info["documents"]}'
                                validate=true
                                placeholder="0000000000"
                        >
                        </input-document>
                    </div>

                    <div class='ep-checkout-creditcard-input-cellphone'>
                        <input-address
                                labelMessage="{l s='Address' mod='payco'}"
                                helperMessage="{l s='Invalid address' mod='payco'}"
                                placeholder="Street 123"
                                inputName='epayco_creditcard[address]'
                                flagError='epayco_creditcard[addressError]'
                                validate=true
                                hiddenId= "hidden-adress-creditcard"
                        >
                        </input-address>
                    </div>

                    <div class="ep-checkout-creditcard-input-document">
                        <input-email
                                labelMessage="{l s='Email' mod='payco'}"
                                helperMessage="{l s='Invalid email' mod='payco'}"
                                placeholder="jonhdoe@example.com"
                                inputName='epayco_creditcard[email]'
                                flagError='epayco_creditcard[emailError]'
                                validate=true
                                hiddenId= "hidden-email-creditcard"
                        >
                        </input-email>
                    </div>

                    <div class='ep-checkout-creditcard-input-cellphone'>
                        <input-cellphone
                                label-message="{l s='Cellphone' mod='payco'}"
                                helper-message="{l s='Invalid Cellphone' mod='payco'}"
                                input-name='epayco_creditcard[cellphone]'
                                hidden-id="cellphoneType"
                                input-data-checkout="cellphone_number"
                                select-id="cellphoneType"
                                input-id="cellphoneTypeNumber"
                                select-name="epayco_creditcard[cellphoneType]"
                                select-data-checkout="cellphone_type"
                                flag-error="cellphoneTypeError"
                                validate=true
                                placeholder="0000000000"
                        >
                        </input-cellphone>
                    </div>

                    <div class="ep-checkout-creditcard-input-document">
                        <input-country
                                label-message="{l s='Country' mod='payco'}"
                                helper-message="{l s='Invalid City' mod='payco'}"
                                input-name='epayco_creditcard[country]'
                                hidden-id="countryType"
                                input-data-checkout="country_number"
                                select-id="countryType"
                                input-id="countryTypeNumber"
                                select-name="epayco_creditcard[countryType]"
                                select-data-checkout="doc_type"
                                flag-error="countryTypeError"
                                validate=true
                                placeholder="{l s='City' mod='payco'}"
                        >
                    </div>
                </div>
                <!-- NOT DELETE LOADING-->
                <div id="ep-box-loading"></div>

            </div>
            <div style="margin: 14px !important;">
                <terms-and-conditions
                        label="{l s='I confirm and accept the' mod='payco'}"
                        description="{l s=' of ePayco.' mod='payco'}"
                        link-text="{l s='Terms and conditions' mod='payco'}"
                        link-src="https://epayco.com/terminos-y-condiciones-usuario-pagador-comprador/"
                        link-condiction-text="{l s=' personal data processing policy' mod='payco'}"
                        and_the="{l s=' and the' mod='payco'}"
                        link-condiction-src="https://epayco.com/tratamiento-de-datos/"
                >
                </terms-and-conditions>
            </div>
            <div style="display: flex;justify-content: center; align-items: center;padding: 15px;">
                <p>Secure by</p>
                <img width="65px" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/logo.png">
            </div>
        </div>
    </div>
    <div id="epayco-utilities" style="display:none;">
        <input type="hidden" id="cardTokenId" name="epayco_creditcard[cardTokenId]" />
    </div>
</form>

<script type="text/javascript" src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="https://cms.epayco.io/js/library.js"/>
<script type="text/javascript" src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/front.js?v={$version|escape:'htmlall':'UTF-8'}"/>
<script type="text/javascript" src="{$module_dir|escape:'htmlall':'UTF-8'}views/js/checkouts/creditcard/ep-creditcard-checkout.js?v={$version|escape:'htmlall':'UTF-8'}"/>


{if $public_key != ''}
    <div hidden="true" id="public_key">{$public_key|escape:'htmlall':'UTF-8'}</div>
{/if}
