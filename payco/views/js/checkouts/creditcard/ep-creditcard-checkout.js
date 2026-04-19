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

        function showPaymentError(message, data) {
            const container = document.querySelector('.ep-checkout-creditcard-container');
            if (!container) {
                alert(message);
                return;
            }
            let errorBox = container.querySelector('.ep-payment-error-message');
            if (!errorBox) {
                errorBox = document.createElement('div');
                errorBox.className = 'ep-payment-error-message';
                errorBox.setAttribute('role', 'alert');
                errorBox.setAttribute('data-testid', 'ep-payment-error');
                errorBox.style.cssText = 'background:#fdecea;border:1px solid #f5c2c0;color:#b71c1c;padding:12px 16px;margin:12px 0;border-radius:6px;font-size:14px;font-weight:500;display:flex;align-items:flex-start;gap:8px;';
                container.insertBefore(errorBox, container.firstChild);
            }
            let refInfo = '';
            if (data && data.ref_payco) {
                refInfo = ' <span style="opacity:0.75;font-weight:400;">(Ref ePayco: ' + data.ref_payco + ')</span>';
            }
            errorBox.innerHTML = '<span aria-hidden="true" style="font-size:18px;line-height:1;">&#9888;</span><span>' + message + refInfo + '</span>';
            errorBox.style.display = 'flex';
            try { errorBox.scrollIntoView({behavior: 'smooth', block: 'center'}); } catch (e) {}
        }

        function clearPaymentError() {
            const errorBox = document.querySelector('.ep-payment-error-message');
            if (errorBox) { errorBox.style.display = 'none'; }
        }

        function resetPaymentFormForRetry() {
            if (creditcardForm && creditcardForm.parentElement) {
                creditcardForm.parentElement.classList.remove('loader_epayco');
            }
            const confirmBtn = document.querySelector('#payment-confirmation button');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.removeAttribute('disabled');
                confirmBtn.textContent = confirmBtn.dataset.originalText || 'Confirmar mi pedido';
            }
            const termsCheckbox = document.getElementById('conditions_to_approve[terms-and-conditions]');
            if (termsCheckbox) { termsCheckbox.checked = true; }
        }

        function uncheckConditionTerms() {
            const conditionTermsCheckbox = document.getElementById('conditions_to_approve[terms-and-conditions]');

            if (!conditionTermsCheckbox) return;

            conditionTermsCheckbox.checked = false;
        }

        function disableFinishOrderButton() {
            const finishOrderButton = document.querySelector('#payment-confirmation button');

            finishOrderButton.setAttribute('disabled', 'disabled');
        }

        async function  epaycoFormHandler() {

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
            const cellphoneType = customContentCellphone.parentElement.parentElement.querySelector(".ep-country-selected")?.querySelector("span")?.innerHTML;
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
                "epayco_creditcard[cellphoneType]": cellphoneType,
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
                clearPaymentError();
                creditcardForm.parentElement.classList.add("loader_epayco")
                try {
                    const resultado = await createToken(CustomContent);

                    if (!resultado) {
                        throw new Error('No se pudo generar el token de la tarjeta');
                    }

                    nn["epayco_creditcard[cardTokenId]"] = resultado;
                    document.querySelector('#cardTokenId').value = resultado;

                    var formData = new FormData(creditcardForm);
                    Object.keys(nn).forEach(function(key) {
                        formData.set(key, nn[key]);
                    });
                    formData.set('epayco_creditcard[cardTokenId]', resultado);

                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', creditcardForm.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onload = function() {
                        var contentType = xhr.getResponseHeader('Content-Type') || '';
                        var jsonBody = null;
                        if (contentType.indexOf('application/json') !== -1) {
                            try { jsonBody = JSON.parse(xhr.responseText); } catch (e) { jsonBody = null; }
                        }

                        if (jsonBody && jsonBody.success === false) {
                            var friendly = jsonBody.error_message || 'No pudimos procesar tu pago. Por favor intenta nuevamente.';
                            showPaymentError(friendly, jsonBody);
                            resetPaymentFormForRetry();
                            return;
                        }

                        if (xhr.status >= 200 && xhr.status < 400) {
                            var responseUrl = xhr.responseURL;
                            if (responseUrl && responseUrl !== creditcardForm.action) {
                                window.location.href = responseUrl;
                            } else {
                                creditcardForm.submit();
                            }
                        } else {
                            console.error('Error en el pago:', xhr.status);
                            showPaymentError('Ocurrio un error procesando tu pago. Por favor intenta nuevamente.');
                            resetPaymentFormForRetry();
                        }
                    };
                    xhr.onerror = function() {
                        console.error('Error de red al procesar el pago');
                        showPaymentError('Error de red al procesar el pago. Verifica tu conexion e intenta nuevamente.');
                        resetPaymentFormForRetry();
                    };
                    xhr.send(formData);
                    epayco_submit = true;
                    return epayco_submit;
                } catch (error) {
                    console.error('ePayco token error:', error);
                    showPaymentError('No pudimos validar los datos de tu tarjeta. Verificalos e intenta nuevamente.');
                    resetPaymentFormForRetry();
                    return epayco_submit;
                }
            }

        }



        async function  createToken($form) {
            return await new Promise(function(resolve, reject) {
                ePaycoSubscription.token.create($form, function(data) {

                    creditcardForm.parentElement.classList.remove("loader_epayco")
                    if(data.status=='error' || !data.status){
                        const parsedError = handleCardFormErrors(data);
                        console.error('ePayco cardForm error: ', parsedError);
                        reject(false)
                    }else{
                        resolve(data.data.token)
                    }
                });
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
            var epaycoProcessing = false;

            // Intercept ALL click events at document level in capture phase
            // This runs BEFORE jQuery delegation handlers
            document.addEventListener('click', function(e) {
                var target = e.target;
                // Check if click is on the payment confirmation button or its children
                var confirmBtn = document.querySelector('#payment-confirmation button');
                if (!confirmBtn) return;
                if (target !== confirmBtn && !confirmBtn.contains(target)) return;

                // Check if ePayco is the selected payment method.
                // DOM: .payment-option (radio) + .js-payment-option-form > .payment-method-wrapper > form#ep_creditcard_checkout
                var jsPaymentOptionForm = creditcardForm.closest('.js-payment-option-form');
                var creditcardRadioInput = jsPaymentOptionForm
                    ? (jsPaymentOptionForm.previousElementSibling && jsPaymentOptionForm.previousElementSibling.querySelector('input[type="radio"]'))
                    : null;
                // Fallback to legacy structure
                if (!creditcardRadioInput && creditcardForm.parentNode.previousElementSibling) {
                    creditcardRadioInput = creditcardForm.parentNode.previousElementSibling.querySelector('input[type="radio"]');
                }
                if (!creditcardRadioInput || !creditcardRadioInput.checked) return;

                // Prevent PS from handling the click
                if (!epaycoProcessing) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    epaycoProcessing = true;
                    if (!confirmBtn.dataset.originalText) {
                        confirmBtn.dataset.originalText = confirmBtn.textContent;
                    }
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'Procesando...';
                    epaycoFormHandler()
                        .catch(function (err) {
                            console.error('epaycoFormHandler failed:', err);
                            alert('Ocurrió un error inesperado al procesar el pago.');
                        })
                        .finally(function () {
                            epaycoProcessing = false;
                        });
                }
            }, true);
        })

        $('form#ep_creditcard_checkout').submit(function (e) {
            e.preventDefault();
            return false;
        });
    })
})(jQuery);






