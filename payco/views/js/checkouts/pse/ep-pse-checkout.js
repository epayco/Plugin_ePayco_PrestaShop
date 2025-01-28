/**
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
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */

/* global Epayco, Option, jQuery, $ */
/* eslint no-return-assign: 0 */

(function ($) {
    'use strict';
    $(function () {

        console.log('pse payco')
        function uncheckConditionTerms() {
            const conditionTermsCheckbox = document.getElementById('conditions_to_approve[terms-and-conditions]');

            if (!conditionTermsCheckbox) return;

            conditionTermsCheckbox.checked = false;
        }

        function disableFinishOrderButton() {
            const finishOrderButton = document.querySelector('#payment-confirmation button');

            finishOrderButton.setAttribute('disabled', 'disabled');
        }
        // Handler form submit
        function epaycoFormHandlerPse() {
            var epayco_submit_pse = false;
            //let pseContent = document.querySelector("form.checkout").getElementsByClassName("ep-checkout-pse-content")[0];
            const current =  document.querySelector(".ep-checkout-pse-container");

            const pseContentName = current.querySelector('input-name').querySelector('input');
            const nameHelpers =  current.querySelector('input-helper').querySelector("div");
            const verifyName = (nameElement) => {
                if (nameElement === '') {
                    current.querySelector('input-name').querySelector("input").classList.add("ep-error");
                    nameHelpers.style.display = 'flex';
                }
            }

            const pseContentAddress = current.querySelector('input-address').querySelector('input');
            const addressHelpers =  current.querySelector('input-address').querySelector("input-helper").querySelector("div");
            const verifyAddress = (addressElement) => {
                if (addressElement === '') {
                    current.querySelector('input-address').querySelector("input").classList.add("ep-error");
                    addressHelpers.style.display = 'flex';
                }
            }

            const pseContentEmail = current.querySelector('input-email').querySelector('input');
            const emailHelpers =  current.querySelector('input-email').querySelector("input-helper").querySelector("div");
            const verifyEmail = (emailElement) => {
                if (emailElement === '') {
                    current.querySelector('input-email').querySelector("input").classList.add("ep-error");
                    emailHelpers.style.display = 'flex';
                }
            }

            const pseContentCellphone = current.querySelector('input-cellphone').querySelector('input');
            const cellphoneHelpers =  current.querySelector('input-cellphone').querySelector("input-helper").querySelector("div");
            //const cellphoneType = document.getElementsByName('epayco_pse[cellphone]')[0].value;
            const cellphoneType = pseContentCellphone.parentElement.parentElement.querySelector(".ep-input-select-bank");
            const verifyCellphone = (cellphone) => {
                if (cellphone === '') {
                    current.querySelector('input-cellphone').querySelector("input").classList.add("ep-error");
                    current.querySelector('input-cellphone').querySelector("select").parentElement.classList.add("ep-error");
                    cellphoneHelpers.style.display = 'flex';
                }
            }

            const person_type_value = document.getElementsByName('epayco_pse[person_type]')[1];
            //const doc_type = document.getElementsByName('epayco_pse[documentType]')[0];
            const doc_number_value = current.querySelector('input-document').querySelector('input');
            const doc_type = doc_number_value.parentElement.parentElement.querySelector(".ep-input-select-bank");
            const documentHelpers =  current.querySelector('input-document').querySelector("input-helper").querySelector("div");
            const verifyDocument = (pseContentDocument) => {
                if (pseContentDocument === '') {
                    current.querySelector('input-document').querySelector("input").classList.add("ep-error");
                    current.querySelector('input-document').querySelector("select").parentElement.classList.add("ep-error");
                    documentHelpers.style.display = 'flex';
                }
            }

            //const pseContentCountry = document.getElementsByName('epayco_pse[country]')[0];
            const pseContentCountry = current.querySelector('input-country').querySelector('input');
            const countryType = pseContentCountry.parentElement.parentElement.querySelector(".ep-input-select-bank");
            //const countryType = document.getElementsByName('epayco_pse[countryType]')[0];
            const countryHelpers =  current.querySelector('input-country').querySelector("input-helper").querySelector("div");
            const verifyCountry = (pseContentCountry) => {
                if (pseContentCountry === '') {
                    current.querySelector('input-country').querySelector("input").classList.add("ep-error");
                    current.querySelector('input-country').querySelector("select").parentElement.classList.add("ep-error");
                    countryHelpers.style.display = 'flex';
                }
            }
            var paymentOptionSelected;

            document.querySelector(".ep-checkout-pse-container").querySelectorAll(".ep-input-radio-radio").forEach((e => {
                if (e.checked) {
                    paymentOptionSelected = e.value;
                }
            }))
            const termanAndContictionContent = current.querySelector('terms-and-conditions').querySelector('input');
            const termanAndContictionHelpers = current.querySelector('terms-and-conditions').querySelector(".ep-terms-and-conditions-container");
            termanAndContictionContent.addEventListener('click', function() {
                if (termanAndContictionContent.checked) {
                    termanAndContictionHelpers.classList.remove("ep-error")
                }
            });

            const bank = document.getElementsByName('epayco_pse[bank]')[1].value;
            const bankHelper = document.getElementsByName('epayco_pse[bank]')[0].querySelector('input-helper').querySelector('div');
            if("0" === bank){
                m(bankHelper, "flex")
            }else{
                m(bankHelper, "none")
            }
            function m(e, t) {
                e && e.style && (e.style.display = t)
            }
            function d(e) {
                return e && "flex" === e.style.display
            }

            const nn = {
                "epayco_pse[name]": pseContentName.value,
                "epayco_pse[address]": pseContentAddress.value,
                "epayco_pse[email]": pseContentEmail.value,
                "epayco_pse[cellphoneType]": cellphoneType.value,
                "epayco_pse[cellphone]": pseContentCellphone.value,
                "epayco_pse[person_type]": person_type_value.value,
                "epayco_pse[identificationtype]": doc_type.value,
                "epayco_pse[doc_number]": doc_number_value.value,
                "epayco_pse[countryType]": countryType.value,
                "epayco_pse[country]": pseContentCountry.value,
                "epayco_pse[bank]": bank
            };
            console.log(nn)
            "" === pseContentName.value && verifyName(pseContentName.value);
            "" === pseContentEmail.value && verifyEmail(pseContentEmail.value);
            "" === pseContentAddress.value && verifyAddress(pseContentAddress.value);
            "" === pseContentCellphone.value && verifyCellphone(pseContentCellphone.value);
            //"" === cellphoneType && verifyCellphone(cellphoneType);
            "Type"||"Tipo" === doc_type.value && verifyDocument(doc_number_value.value);
            "" === doc_number_value.value && verifyDocument(doc_number_value.value);
            "" === pseContentCountry.value && verifyCountry(pseContentCountry.value);
            !termanAndContictionContent.checked && termanAndContictionHelpers.classList.add("ep-error");

            let validation = d(nameHelpers) || d(addressHelpers) ||d(emailHelpers) || d(cellphoneHelpers)|| d(documentHelpers) ||  d(bankHelper) || d(countryHelpers) || !termanAndContictionContent.checked;

            if (  validation  ) {
                disableFinishOrderButton();
                uncheckConditionTerms();
            } else {
                epayco_submit_pse = true;
            }

            return epayco_submit_pse;
        }


        waitForElement('#payment-confirmation').then(() => {
            const pseForm = document.getElementById('ep_pse_checkout');
            pseForm.onsubmit = () => {
                const pseRadioInput = document.getElementById('ep_pse_checkout').parentNode.previousElementSibling.querySelector('input');
                const pseIsSelected = pseRadioInput.checked;
                if (!epaycoFormHandlerPse()) return false;
                pseForm.submit();
            }
        })

        $('form#ep_pse_checkout').submit(function () {
            return epaycoFormHandlerPse();
        });
    })
})(jQuery);