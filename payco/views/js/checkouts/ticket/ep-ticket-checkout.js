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
        function epaycoFormHandlerTicket() {
            console.log('=== INICIANDO VALIDACIÓN DE FORMULARIO DE TICKET ===');
            var epayco_submit_ticket = false;
            
            const ticketContent = document.querySelector("form.checkout")?.getElementsByClassName("ep-checkout-ticket-content")[0] ||
                                  document.querySelector(".ep-checkout-ticket-container");
            
            if (!ticketContent) {
                console.error('❌ Ticket checkout container not found');
                return false;
            }
            console.log('✓ Contenedor encontrado');

            // Get values using getElementsByName - this works reliably
            const ticketNameElement = document.getElementsByName('epayco_ticket[name]')[0];
            const ticketContentName = ticketNameElement ? ticketNameElement.value : '';
            console.log('📝 NOMBRE valor:', ticketContentName);
            
            const ticketEmailElement = document.getElementsByName('epayco_ticket[email]')[0];
            const ticketContentEmail = ticketEmailElement ? ticketEmailElement.value : '';
            console.log('📧 EMAIL valor:', ticketContentEmail);
            
            const cellphoneElement = document.getElementsByName('epayco_ticket[cellphone]')[0];
            const cellphoneValue = cellphoneElement ? cellphoneElement.value : '';
            console.log('📱 CELULAR valor:', cellphoneValue);
            
            const documentElement = document.getElementsByName('epayco_ticket[document]')[0];
            const docNumberValue = documentElement ? documentElement.value : '';
            console.log('🆔 DOCUMENTO valor:', docNumberValue);

            // Get helpers
            const nameHelpers = ticketContent.querySelector('input-name-epayco')?.querySelector("input-helper-epayco")?.querySelector("div");
            const emailHelpers = ticketContent.querySelector('input-email-epayco')?.querySelector("input-helper-epayco")?.querySelector("div");
            const cellphoneHelpers = ticketContent.querySelector('input-cellphone-epayco')?.querySelector("input-helper-epayco")?.querySelector("div");
            const documentHelpers = ticketContent.querySelector('input-document-epayco')?.querySelector("input-helper-epayco")?.querySelector("div");
            
            // Verify name
            if (ticketContentName === '' || ticketContentName.length < 2) {
                if (nameHelpers) nameHelpers.style.display = 'flex';
            } else {
                if (nameHelpers) nameHelpers.style.display = 'none';
            }
            
            // Verify email
            if (ticketContentEmail === '') {
                if (emailHelpers) emailHelpers.style.display = 'flex';
            } else {
                if (emailHelpers) emailHelpers.style.display = 'none';
            }
            
            // Verify cellphone
            if (cellphoneValue === '') {
                if (cellphoneHelpers) cellphoneHelpers.style.display = 'flex';
            } else {
                if (cellphoneHelpers) cellphoneHelpers.style.display = 'none';
            }
            
            // Verify document
            if (docNumberValue === '') {
                if (documentHelpers) documentHelpers.style.display = 'flex';
            } else {
                if (documentHelpers) documentHelpers.style.display = 'none';
            }
            
            // Verify payment method
            let paymentOptionSelected;
            const allRadios = document.querySelectorAll('input[type="radio"][name="epayco_ticket[payment_method_id]"]');
            let hasValidPayment = false;
            
            console.log('💳 Radios encontrados:', allRadios.length);
            allRadios.forEach((radio, index) => {
                console.log(`  [${index}] ${radio.value} - checked: ${radio.checked}`);
                if (radio.checked) {
                    paymentOptionSelected = radio.value;
                    hasValidPayment = true;
                }
            });
            
            const paymentselpers = document.querySelector(".ep-checkout-ticket-container")?.querySelector('input-helper-epayco')?.querySelector('div');
            if (!hasValidPayment && paymentselpers) {
                paymentselpers.style.display = 'flex';
            } else if (paymentselpers) {
                paymentselpers.style.display = 'none';
            }
            
            // Verify terms - USAR EL CHECKBOX DE PRESTASHOP
            const termsCheckbox = document.getElementById('conditions_to_approve[terms-and-conditions]');
            let termsChecked = termsCheckbox ? termsCheckbox.checked : false;
            console.log('✅ TÉRMINOS DE PRESTASHOP:', termsChecked);
            
            // Check for errors
            let hasErrors = false;
            if (ticketContentName === '' || ticketContentName.length < 2) hasErrors = true;
            if (ticketContentEmail === '') hasErrors = true;
            if (cellphoneValue === '') hasErrors = true;
            if (docNumberValue === '') hasErrors = true;
            if (!hasValidPayment) hasErrors = true;
            if (!termsChecked) hasErrors = true;
            
            console.log('=== RESULTADO DE VALIDACIÓN ===');
            console.log('VALORES RECOLECTADOS:', {
                nombre: ticketContentName,
                email: ticketContentEmail,
                celular: cellphoneValue,
                documento: docNumberValue,
                metodo_pago: paymentOptionSelected,
                terminos: termsChecked
            });
            
            if (hasErrors) {
                console.log('❌ VALIDACIÓN FALLIDA');
                disableFinishOrderButton();
                // NO desmarcar automáticamente - dejar que PrestaShop maneje sus propios términos
            } else {
                console.log('✅ VALIDACIÓN EXITOSA');
                epayco_submit_ticket = true;
            }
            
            console.log('=== FIN VALIDACIÓN ===');
            return epayco_submit_ticket;
        }


        // Configuración del formulario de pago
        $(function() {
            /**
             * Interceptar el click del botón "REALIZAR PEDIDO" de PrestaShop
             * Validar el formulario de ticket antes de permitir el pago
             */
            
            function handlePaymentButtonClick(e) {
                console.log('=== DETECTADO CLICK EN BOTÓN DE PAGO ===');
                
                const ticketForm = document.getElementById('ep_ticket_checkout');
                if (!ticketForm) {
                    console.warn('⚠️ Formulario de ticket no encontrado - permitiendo otros métodos de pago');
                    return true; // Permitir que continúe con otros métodos de pago
                }
                console.log('✓ Formulario de ticket encontrado');
                
                // Ejecutar validación del formulario de ticket
                console.log('👉 Ejecutando validación...');
                const isValid = epaycoFormHandlerTicket();
                
                if (!isValid) {
                    console.log('❌ VALIDACIÓN FALLIDA - Bloqueando envío del formulario');
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                console.log('✅ VALIDACIÓN EXITOSA - Enviando formulario de ticket');
                
                // Enviar el formulario de ticket
                ticketForm.submit();
                
                // Prevenir que continue el flujo normal
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            // Esperar a que el botón de pago esté disponible
            function initPaymentHandler() {
                // Buscar el botón de confirmación de pago de PrestaShop
                const paymentButton = document.querySelector('#payment-confirmation button[type="submit"]');
                
                if (!paymentButton) {
                    console.log('⏳ Esperando botón de pago (#payment-confirmation button[type="submit"])...');
                    // Reintentar después de un corto tiempo
                    setTimeout(initPaymentHandler, 300);
                    return;
                }
                
                console.log('=== INICIALIZACIÓN COMPLETADA ===');
                console.log('✓ Botón de pago encontrado');
                console.log('✓ Manejador de ticket asignado');
                console.log('Esperando que usuario haga click en "REALIZAR PEDIDO"...');
                
                // Remover listeners anteriores para evitar duplicados
                const newButton = paymentButton.cloneNode(true);
                paymentButton.parentNode.replaceChild(newButton, paymentButton);
                
                // Asignar el nuevo listener
                newButton.addEventListener('click', handlePaymentButtonClick);
                newButton.addEventListener('submit', handlePaymentButtonClick);
            }
            
            // Inicializar cuando el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPaymentHandler);
            } else {
                initPaymentHandler();
            }
            
            // Fallback: Manejador directo del formulario en caso de que se envíe por otro medio
            $(document).on('submit', 'form#ep_ticket_checkout', function(e) {
                console.log('📤 SUBMIT DIRECTO del formulario detectado');
                // El formulario se envía directamente, permitir
                return true;
            });
        });
    })
})(jQuery);