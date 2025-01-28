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

        console.log('ticket payco')
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
        function epaycoFormHandlerTicket() {
            var epayco_submit_ticket = false;
            const current =  document.querySelector(".ep-checkout-ticket-container");

            const ticketContentName = current.querySelector('input-name').querySelector('input');
            const nameHelpers =  current.querySelector('input-helper').querySelector("div");
            const verifyName = (nameElement) => {
                if (nameElement === '') {
                    current.querySelector('input-name').querySelector("input").classList.add("ep-error");
                    nameHelpers.style.display = 'flex';
                }
            }

            const ticketContentEmail = current.querySelector('input-email').querySelector('input');
            const emailHelpers =  current.querySelector('input-email').querySelector("input-helper").querySelector("div");
            const verifyEmail = (emailElement) => {
                if (emailElement === '') {
                    current.querySelector('input-email').querySelector("input").classList.add("ep-error");
                    emailHelpers.style.display = 'flex';
                }
            }

            const ticketContentCellphone = current.querySelector('input-cellphone').querySelector('input');
            const cellphoneHelpers =  current.querySelector('input-cellphone').querySelector("input-helper").querySelector("div");
            //const cellphoneType = document.getElementsByName('epayco_ticket[cellphone]')[0].value;
            const cellphoneType = ticketContentCellphone.parentElement.parentElement.querySelector(".ep-input-select-bank");
            const verifyCellphone = (cellphone) => {
                if (cellphone === '') {
                    current.querySelector('input-cellphone').querySelector("input").classList.add("ep-error");
                    current.querySelector('input-cellphone').querySelector("select").parentElement.classList.add("ep-error");
                    cellphoneHelpers.style.display = 'flex';
                }
            }

            //const doc_type = document.getElementsByName('epayco_ticket[documentType]')[0];
            const doc_number_value = current.querySelector('input-document').querySelector('input');
            const doc_type = doc_number_value.parentElement.parentElement.querySelector(".ep-input-select-bank");
            const documentHelpers =  current.querySelector('input-document').querySelector("input-helper").querySelector("div");
            const verifyDocument = (ticketContentDocument) => {
                if (ticketContentDocument === '') {
                    current.querySelector('input-document').querySelector("input").classList.add("ep-error");
                    current.querySelector('input-document').querySelector("select").parentElement.classList.add("ep-error");
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
                "epayco_ticket[name]": ticketContentName.value,
                "epayco_ticket[email]": ticketContentEmail.value,
                "epayco_ticket[cellphoneType]": cellphoneType.value,
                "epayco_ticket[cellphone]": ticketContentCellphone.value,
                "epayco_ticket[identificationtype]": doc_type.value,
                "epayco_ticket[doc_number]": doc_number_value.value,

            };
            var paymentOptionSelected;
            const paymentselpers =  document.querySelector(".ep-checkout-ticket-container").querySelectorAll(".ep-input-radio-radio")[0].parentElement.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.querySelector('input-helper').querySelector('div');
            document.querySelector(".ep-checkout-ticket-container").querySelectorAll(".ep-input-radio-radio").forEach((e => {
                if (e.checked) {
                    paymentOptionSelected = e.value;
                }
            }))

            if(paymentOptionSelected !==''&& paymentOptionSelected !== undefined){
                m(paymentselpers, "none")
                nn["epayco_ticket[payment_method_id]"] = paymentOptionSelected;
            }else{
                m(paymentselpers, "flex")
            }

            function m(e, t) {
                e && e.style && (e.style.display = t)
            }
            function d(e) {
                return e && "flex" === e.style.display
            }
            console.log(nn)
            "" === ticketContentName.value && verifyName(ticketContentName.value);
            "" === ticketContentEmail.value && verifyEmail(ticketContentEmail.value);
            "" === ticketContentCellphone.value && verifyCellphone(ticketContentCellphone.value);
            //"" === cellphoneType && verifyCellphone(cellphoneType);
            "Type"||"Tipo" === doc_type.value && verifyDocument(doc_number_value.value);
            "" === doc_number_value.value && verifyDocument(doc_number_value.value);
            !termanAndContictionContent.checked && termanAndContictionHelpers.classList.add("ep-error");

            let validation = d(nameHelpers)  ||d(emailHelpers) || d(cellphoneHelpers)|| d(documentHelpers) || d(paymentselpers) || !termanAndContictionContent.checked;

            if (  validation  ) {
                disableFinishOrderButton();
                uncheckConditionTerms();
            } else {
                epayco_submit_ticket = true;
            }

            return epayco_submit_ticket;
        }


        waitForElement('#payment-confirmation').then(() => {
            const ticketForm = document.getElementById('ep_ticket_checkout');
            ticketForm.onsubmit = () => {
                const ticketRadioInput = document.getElementById('ep_ticket_checkout').parentNode.previousElementSibling.querySelector('input');
                const ticketIsSelected = ticketRadioInput.checked;
                if (!epaycoFormHandlerTicket()) return false;
                ticketForm.submit();
            }
        })

        $('form#ep_ticket_checkout').submit(function () {
            return epaycoFormHandlerTicket();
        });
    })
})(jQuery);