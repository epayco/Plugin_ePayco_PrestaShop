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
        function epaycoFormHandlerDaviplata() {
            var epayco_submit_daviplata = false;
            const current =  document.querySelector(".ep-checkout-ticket-container");

            const ticketContentName = current.querySelector('input-name-epayco').querySelector('input');
            const nameHelpers =  current.querySelector('input-helper-epayco').querySelector("div");
            const verifyName = (nameElement) => {
                if (nameElement === '') {
                    current.querySelector('input-name-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    nameHelpers.style.display = 'flex';
                }
            }

            const ticketContentEmail = current.querySelector('input-email-epayco').querySelector('input');
            const emailHelpers =  current.querySelector('input-email-epayco').querySelector("input-helper-epayco").querySelector("div");
            const verifyEmail = (emailElement) => {
                if (emailElement === '') {
                    current.querySelector('input-email-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    emailHelpers.style.display = 'flex';
                }
            }

            const ticketContentCellphone = current.querySelector('input-cellphone-epayco').querySelector('input');
            const cellphoneHelpers =  current.querySelector('input-cellphone-epayco').querySelector("input-helper-epayco").querySelector("div");
            //const cellphoneType = document.getElementsByName('epayco_daviplata[cellphone]')[0].value;

            const cellphoneType = ticketContentCellphone.parentElement.parentElement.querySelector(".ep-input-select-select");
            const verifyCellphone = (cellphone) => {
                if (cellphone === '') {
                    current.querySelector('input-cellphone-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    current.querySelector('input-cellphone-epayco').querySelector("select").parentElement.classList.add("ep-error");
                    cellphoneHelpers.style.display = 'flex';
                }
            }
            //const doc_type = document.getElementsByName('epayco_daviplata[documentType]')[0];
            const doc_number_value = current.querySelector('input-document-epayco').querySelector('input');
            const doc_type = doc_number_value.parentElement.parentElement.querySelector(".ep-input-select-select");
            const documentHelpers =  current.querySelector('input-document-epayco').querySelector("input-helper-epayco").querySelector("div");
            const verifyDocument = (ticketContentDocument) => {
                if (ticketContentDocument === '' || ("Tipo" === doc_type.value || "Type" === doc_type)  ) {
                    current.querySelector('input-document-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    current.querySelector('input-document-epayco').querySelector("select").parentElement.classList.add("ep-error");
                    documentHelpers.style.display = 'flex';
                }
            }

            const termanAndContictionContent = current.querySelector('terms-and-conditions').querySelector('input');
            const termanAndContictionHelpers = current.querySelector('terms-and-conditions').querySelector(".ep-terms-and-conditions-container");
            termanAndContictionContent.addEventListener('click', function() {
                if (termanAndContictionContent.checked) {
                    termanAndContictionHelpers.classList.remove("ep-error")
                }
            });



            const nn = {
                "epayco_daviplata[name]": ticketContentName.value,
                "epayco_daviplata[email]": ticketContentEmail.value,
                "epayco_daviplata[cellphoneType]": cellphoneType.value,
                "epayco_daviplata[cellphone]": ticketContentCellphone.value,
                "epayco_daviplata[identificationtype]": doc_type.value,
                "epayco_daviplata[doc_number]": doc_number_value.value,

            };



            function m(e, t) {
                e && e.style && (e.style.display = t)
            }
            function d(e) {
                return e && "flex" === e.style.display
            }

            "" === ticketContentName.value && verifyName(ticketContentName.value);
            "" === ticketContentEmail.value && verifyEmail(ticketContentEmail.value);
            "" === ticketContentCellphone.value && verifyCellphone(ticketContentCellphone.value);
            //"" === cellphoneType && verifyCellphone(cellphoneType);
            "Type"||"Tipo" === doc_type.value && verifyDocument(doc_number_value.value);
            "" === doc_number_value.value;
            verifyDocument(doc_number_value.value);
            !termanAndContictionContent.checked && termanAndContictionHelpers.classList.add("ep-error");

            let validation = d(nameHelpers)  ||d(emailHelpers) || d(cellphoneHelpers)|| d(documentHelpers)  || !termanAndContictionContent.checked || "Tipo" === doc_type.value || "Type" === doc_type;

            if (  validation  ) {
                disableFinishOrderButton();
                uncheckConditionTerms();
            } else {
                epayco_submit_daviplata = true;
            }

            return epayco_submit_daviplata;
        }


        waitForElement('#payment-confirmation').then(() => {
            const daviplataForm = document.getElementById('ep_daviplata_checkout');
            daviplataForm.onsubmit = () => {
                const daviplataRadioInput = document.getElementById('ep_daviplata_checkout').parentNode.previousElementSibling.querySelector('input');
                const ticketIsSelected = daviplataRadioInput.checked;
                if (!epaycoFormHandlerDaviplata()) return false;
                daviplataForm.submit();
            }
        })

        $('form#ep_daviplata_checkout').submit(function () {
            return epaycoFormHandlerDaviplata();
        });
    })
})(jQuery);