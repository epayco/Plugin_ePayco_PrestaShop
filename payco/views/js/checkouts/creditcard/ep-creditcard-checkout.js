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
        const creditcardForm = document.getElementById('ep_creditcard_checkout');
        function uncheckConditionTerms() {
            const conditionTermsCheckbox = document.getElementById('conditions_to_approve[terms-and-conditions]');

            if (!conditionTermsCheckbox) return;

            conditionTermsCheckbox.checked = false;
        }

        function disableFinishOrderButton() {
            const finishOrderButton = document.querySelector('#payment-confirmation button');

            finishOrderButton.setAttribute('disabled', 'disabled');
        }

        function  epaycoFormHandler() {

            //creditcardForm.parentElement.classList.add("loader_epayco")
            var epayco_submit = false;
            ePaycoSubscription.setPublicKey(ePaycoPublicKey)
            ePaycoSubscription.setLanguage(lenguaje)
            var CustomContent = document.getElementsByClassName("ep-checkout-creditcard-container")[0];
            var creditcardContent_ = document.getElementsByName('epayco_creditcard[name]')?? document.getElementsByName('epayco_creditcard[nameError]');

            const current =  document.querySelector(".ep-checkout-creditcard-container");
            const customContentName = current.querySelector('input-name-epayco').querySelector('input');
            const nameHelpers =  current.querySelector('input-helper-epayco').querySelector("div");
            const verifyName = (nameElement) => {
                if (nameElement.value === '') {
                    current.querySelector('input-name-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    nameHelpers.style.display = 'flex';
                }
            }

            const cardNumberContentName = current.querySelector('input-card-number').querySelector('input');
            const cardNumberHelpers =  current.querySelector('input-card-number').querySelector("input-helper-epayco").querySelector("div");
            const verifyCardNumber = (nameElement) => {
                if (nameElement.value === '') {
                    current.querySelector('input-card-number').querySelector(".ep-input").classList.add("ep-error");
                    cardNumberHelpers.style.display = 'flex';
                }
            }

            const cardExpirationContentName = current.querySelector('input-card-expiration-date').querySelector('input');
            const cardExpirationHelpers =  current.querySelector('input-card-expiration-date').querySelector("input-helper-epayco").querySelector("div");
            const verifyCardExpiration = (nameElement) => {
                if (nameElement.value === '') {
                    current.querySelector('input-card-expiration-date').querySelector(".ep-input").classList.add("ep-error");
                    cardExpirationHelpers.style.display = 'flex';
                }
            }
            const cardSecurityContentName = current.querySelector('input-card-security-code').querySelector('input');
            const cardSecurityHelpers =  current.querySelector('input-card-security-code').querySelector("input-helper-epayco").querySelector("div");
            const verifyCardSecurity = (nameElement) => {
                if (nameElement.value === '') {
                    current.querySelector('input-card-security-code').querySelector(".ep-input").classList.add("ep-error");
                    cardSecurityHelpers.style.display = 'flex';
                }
            }

            const cardContentDocument = current.querySelector('input-document-epayco').querySelector('input');
            const doc_type = document.querySelector('input-document-epayco').querySelector(".ep-input-select-select");
            const documentHelpers =  current.querySelector('input-document-epayco').querySelector("input-helper-epayco").querySelector("div");
            const verifyDocument = (cardContentDocument) => {
                if (cardContentDocument.value === '') {
                    current.querySelector('input-document-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    current.querySelector('input-document-epayco').querySelector("select").parentElement.classList.add("ep-error");
                    documentHelpers.style.display = 'flex';
                }
            }

            const verifyTypeDocument= (cardContentTypeDocument) =>{
                if (("Type" == cardContentTypeDocument ||"Tipo"  == cardContentTypeDocument)) {
                    current.querySelector('input-document-epayco').querySelector("input").classList.add("ep-error");
                    current.querySelector('input-document-epayco').querySelector("select").parentElement.classList.add("ep-error");
                    documentHelpers.style.display = 'flex';
                }
            }

            const customContentAddress = current.querySelector('input-address-epayco').querySelector('input');
            const addressHelpers =  current.querySelector('input-address-epayco').querySelector("input-helper-epayco").querySelector("div");
            const verifyAddress = (addressElement) => {
                if (addressElement.value === '') {
                    current.querySelector('input-address-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    addressHelpers.style.display = 'flex';
                }
            }

            const customContentEmail = current.querySelector('input-email-epayco').querySelector('input');
            const emailHelpers =  current.querySelector('input-email-epayco').querySelector("input-helper-epayco").querySelector("div");
            const verifyEmail = (emailElement) => {
                if (emailElement.value === '') {
                    current.querySelector('input-email-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    emailHelpers.style.display = 'flex';
                }
            }

            const customContentCellphone = current.querySelector('input-cellphone-epayco').querySelector('input');
            const cellphoneHelpers =  current.querySelector('input-cellphone-epayco').querySelector("input-helper-epayco").querySelector("div");
            const cellphoneType = customContentCellphone.parentElement.parentElement.querySelector(".ep-input-select-select");
            const verifyCellphone = (customContentCellphone) => {
                if (customContentCellphone.value === '') {
                    current.querySelector('input-cellphone-epayco').querySelector("input").parentElement.classList.add("ep-error");
                    current.querySelector('input-cellphone-epayco').querySelector("select").parentElement.classList.add("ep-error");
                    cellphoneHelpers.style.display = 'flex';
                }
            }
            /*
            const countryContentCountry = current.querySelector('input-country-epayco').querySelector('input');
            const countryHelpers =  current.querySelector('input-country-epayco').querySelector("input-helper-epayco").querySelector("div");
            const verifyCountry = (countryContentCountry) => {
                if (countryContentCountry.value === '') {
                    current.querySelector('input-country-epayco').querySelector("input").classList.add("ep-error");
                    current.querySelector('input-country-epayco').querySelector("select").parentElement.classList.add("ep-error");
                    countryHelpers.style.display = 'flex';
                }
            }*/
            const termanAndContictionContent = current.querySelector('terms-and-conditions').querySelector('input');
            const termanAndContictionHelpers = current.querySelector('terms-and-conditions').querySelector(".ep-terms-and-conditions-container");
            termanAndContictionContent.addEventListener('click', function() {
                if (termanAndContictionContent.checked) {
                    termanAndContictionHelpers.classList.remove("ep-error")
                }
            });
            const customContentInstallments =document.getElementById('epayco_creditcard[installmet]').value;

            //const countryType = countryContentCountry.parentElement.parentElement.querySelector(".ep-input-select-bank");
            const doc_number_value =cardContentDocument.value;
            "" === customContentName.value && verifyName(customContentName);
            "" === cardNumberContentName.value && verifyCardNumber(cardNumberContentName);
            "" === cardExpirationContentName.value && verifyCardExpiration(cardExpirationContentName);
            "" === cardSecurityContentName.value && verifyCardSecurity(cardSecurityContentName);
            ("Type" == doc_type.value ||"Tipo"  == doc_type.value) && verifyTypeDocument(doc_type.value);
            "" === cardContentDocument.value && verifyDocument(cardContentDocument);
            "" === customContentAddress.value && verifyAddress(customContentAddress);
            "" === customContentEmail.value && verifyEmail(customContentEmail);
            "" === customContentCellphone.value && verifyCellphone(customContentCellphone);
            //"" === countryContentCountry.value && verifyCountry(countryContentCountry);
            !termanAndContictionContent.checked && termanAndContictionHelpers.classList.add("ep-error");
            let validDoctype = ("Type" == doc_type.value ||"Tipo"  == doc_type.value)  ? true : false;
            let validation = d(nameHelpers) || d(cardNumberHelpers) || d(cardExpirationHelpers) || d(cardSecurityHelpers) || d(documentHelpers) || d(addressHelpers) || d(emailHelpers) || d(cellphoneHelpers)  || !termanAndContictionContent.checked || validDoctype;

            const nn = {
                "epayco_creditcard[name]": customContentName.value,
                "epayco_creditcard[address]": customContentAddress.value,
                "epayco_creditcard[email]": customContentEmail.value,
                "epayco_creditcard[identificationtype]": doc_type.value,
                "epayco_creditcard[doc_number]": doc_number_value,
                //"epayco_creditcard[countryType]": countryType.value,
                "epayco_creditcard[cellphoneType]": cellphoneType.value,
                "epayco_creditcard[cellphone]": customContentCellphone.value,
                //"epayco_creditcard[country]": countryContentCountry.value,
                "epayco_creditcard[installmet]": customContentInstallments,
            };

            function m(e, t) {
                e && e.style && (e.style.display = t)
            }

            function d(e) {
                return e && "flex" === e.style.display
            }

            if (  validation  ) {
                disableFinishOrderButton();
                uncheckConditionTerms();
                creditcardForm.parentElement.classList.remove("loader_epayco")
                return epayco_submit;
            } else {
                const request =  createToken(CustomContent)
                    .then((resultado) => {
                        nn["epayco_creditcard[cardTokenId]"]=resultado
                        document.querySelector('#cardTokenId').value = resultado;
                        creditcardForm.submit()
                    })
                    .catch((error) => console.error(error));
                //if(!request) return epayco_submit;
                //epayco_submit = true;
            }

        }



        async function  createToken($form) {
            return await new Promise(function(resolve, reject) {
                resolve("79d170cf1c873bf3201aee2")
                /*ePaycoSubscription.token.create($form, function(data) {
                    creditcardForm.parentElement.classList.remove("loader_epayco")
                    if(data.status=='error'){
                        const parsedError = handleCardFormErrors(data);
                        console.error('ePayco cardForm error: ', parsedError);
                        reject(false)
                    }else{
                        resolve(data.data.token)
                    }
                });*/
            });
        }

        function handleCardFormErrors(cardFormErrors) {
            if (cardFormErrors.length) {
                const errors = [];
                cardFormErrors.forEach((e) => {
                    errors.push(e.description || e.message);
                });
                return errors.join(',');
            }
            return cardFormErrors.description || cardFormErrors.message;
        }


        waitForElement('#payment-confirmation').then(() => {
            creditcardForm.onsubmit = () => {
                const creditcardRadioInput = document.getElementById('ep_creditcard_checkout').parentNode.previousElementSibling.querySelector('input');
                const creditcardIsSelected = creditcardRadioInput.checked;
                if (!epaycoFormHandler()) return false;
                creditcardForm.submit();
            }
        })

        $('form#ep_creditcard_checkout').submit(function () {
            return epaycoFormHandler();
        });
    })
})(jQuery);






