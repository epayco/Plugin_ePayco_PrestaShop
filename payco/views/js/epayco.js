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

(function () {
    console.log('payco')
    document.addEventListener('DOMContentLoaded', function () {
        var form_credentials = document.querySelector("#module_form");
        form_credentials.style = "margin: 10px 0px";
        form_credentials.style = "display: none";
        form_credentials.classList.add("dropdown-hidden");
        var fieldset = document.querySelector("#fieldset_0");
        fieldset.classList.add("ep-settings-credentials");
        var ep_settings  = document.querySelector("#ep-settings-step-one");
        ep_settings.insertAdjacentElement('afterend', form_credentials)
        //form_store_group[i].insertAdjacentHTML('afterend', form_store_append);
        function mpSettingsAccordionStart() {
            let e;
            const t = document.getElementsByClassName("ep-settings-title-align");
            for (e = 0; e < t.length; e++) t[e].addEventListener("click", (function () {
                this.classList.toggle("active");
                {
                    let e = null;
                    for (let t = 0; t < this.childNodes.length; t++) if (this.childNodes[t]?.classList?.contains("ep-settings-margin-left")) {
                        e = this.childNodes[t];
                        break
                    }
                    e?.childNodes[1]?.classList?.toggle("ep-arrow-up")
                }
                const e = this.nextElementSibling;
                //e.style.display = window.getComputedStyle(e).display === "none" ? "block" : "none";
                if (e.className.indexOf('dropdown-hidden') > 0) {
                    //e.style.display = "block"
                    e.classList.remove("dropdown-hidden");
                    e.classList.add("dropdown-visible");
                    e.id !== "ep-step-3" ? form_credentials.style = "display: block" : "";
                    //form_credentials.style = "display: block";
                } else {
                    e.classList.remove("dropdown-visible");
                    e.classList.add("dropdown-hidden");
                    e.id !== "ep-step-3" ? form_credentials.style = "display: none" : "";
                    form_credentials.style = "display: none";
                    //e.style.display = "none"
                }
                //"block" === e.style.display ? e.style.display = "none" : e.style.display = "block"
            }))
        }

        function mpStatusPaymentMethod(e) {
            const field = document.getElementsByName(e);
            var field_status_ID;
            for (let i = 0; i < field.length; i++) {
                if (field[i].checked) {
                    field_status_ID = field[i].id;                } 
            }
            return field_status_ID.split('_')[3] =='off' ? 'no' : 'yes';
        }

        function mpGetPaymentMethods() {
            const ticket = document.getElementsByName("EPAYCO_TICKET_CHECKOUT");
            var ticket_status_ID;
            for (let i = 0; i < ticket.length; i++) {
                if (ticket[i].checked) {
                    ticket_status_ID = ticket[i].id;                } 
            }
            const ticket_status = mpStatusPaymentMethod("EPAYCO_TICKET_CHECKOUT");
            const creditcard_status = mpStatusPaymentMethod("EPAYCO_CREDITCARD_CHECKOUT");
            const pse_status = mpStatusPaymentMethod("EPAYCO_PSE_CHECKOUT");
            const standard_status = mpStatusPaymentMethod("EPAYCO_STANDARD_CHECKOUT");
            const daviplata_status = mpStatusPaymentMethod("EPAYCO_DAVIPLATA_CHECKOUT");
            const response = {
                "success": true,
                "data": [
                  {
                    "id": "epayco-checkout",
                    "title_gateway": "Checkout ePayco",
                    "description": "Ofrezca a sus cliente una experiencia de pago completa con múltiples opciones: Tarjetas, transferencias bancarias, monederos digitales y efectivo. ¡Todo en una plataforma segura y fácil de usar!",
                    "title": "Checkout ePayco",
                    "enabled":standard_status,
                    "icon": "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/new/checkout.png",
                    "link": "#standard_checkout",
                    "badge_translator": {
                      "yes": "Activo",
                      "no": "Inactivo"
                    }
                  },
                  {
                    "id": "epayco-creditcard",
                    "title_gateway": "Tarjeta de crédito y/o débito",
                    "description": "Acepta pagos rápidos y seguros directamente desde tu tienda con tarjetas de crédito y débito de cualquier banco. Sin redirecciones, garantizando una experiencia de compra sin interrupciones. (Visa, Mastercard, Amex y Dinners)",
                    "title": "Tarjeta de crédito y/o débito",
                    "enabled": creditcard_status,
                    "icon": "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/new/tarjeta.png",
                    "link": "#creditcard_checkout",
                    "badge_translator": {
                      "yes": "Activo",
                      "no": "Inactivo"
                    }
                  },
                  {
                    "id": "woo-epayco-daviplata",
                    "title_gateway": "DaviPlata",
                    "description": "Conéctate con millones de usuarios de DaviPlata en Colombia. Los clientes pueden pagar directamente desde tu tienda sin pasos adicionales, se forma rápida y sencilla.",
                    "title": "DaviPlata",
                    "enabled": daviplata_status,
                    "icon": "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/new/daviplata.png",
                    "link": "#daviplata_checkout",
                    "badge_translator": {
                      "yes": "Activo",
                      "no": "Inactivo"
                    }
                  },
                  {
                    "id": "epayco-pse",
                    "title_gateway": "PSE",
                    "description": "Permita que sus clientes paguen con transferencia bancarias directas desde cualquier banco colombiano, todo sin salir de su tienda en línea. Seguro, rápido y sin interrupciones.",
                    "title": "PSE",
                    "enabled": pse_status,
                    "icon": "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/new/pse.png",
                    "link": "#pse_checkout",
                    "badge_translator": {
                      "yes": "Activo",
                      "no": "Inactivo"
                    }
                  },
                  {
                    "id": "epayco-ticket",
                    "title_gateway": "Efectivo",
                    "description": "Añada la opción de pago en efectivo directamente en su tienda. Perfecto para los clientes que prefieren pagar en lugares físicos, sin complicaciones ni redirecciones.",
                    "title": "Efectivo",
                    "enabled": ticket_status,
                    "icon": "https://multimedia-epayco-preprod.s3.us-east-1.amazonaws.com/plugins-sdks/efectivo.png",
                    "link": "#ticket_checkout",
                    "badge_translator": {
                      "yes": "Activo",
                      "no": "Inactivo"
                    }
                  }
                ]
              }

            const paymentContainer = document.getElementById("ep-payment");
    
            // Eliminar todos los bloques de métodos de pago existentes
            document.querySelectorAll(".ep-settings-payment-block").forEach(function(element) {
                element.remove();
            });
    
            // Insertar nuevos métodos de pago
            response.data.reverse().forEach(function(paymentMethod) {
                paymentContainer.insertAdjacentElement("afterend", createMpPaymentMethodComponent(paymentMethod));
            });
    
            // Llamar al callback de melidata si está disponible
            if (window.melidata && window.melidata.client && window.melidata.client.stepPaymentMethodsCallback) {
                window.melidata.client.stepPaymentMethodsCallback();
            }
        }

        function createMpPaymentMethodComponent(paymentMethod) {
            const badgeClass = paymentMethod.enabled === "yes" ? "ep-settings-badge-active" : "ep-settings-badge-inactive";
            const badgeText = paymentMethod.enabled === "yes" ? paymentMethod.badge_translator.yes : paymentMethod.badge_translator.no;
            const container = document.createElement("div");
        
            container.style.display = "flex";
            container.style.flexDirection = "column";
        
            const paymentMethodComponent = getPaymentMethodComponent(paymentMethod, badgeClass, badgeText);
            container.appendChild(paymentMethodComponent);
        
            return container;
        }

        function getPaymentMethodComponent(e, t, n) {
            const s = `\n    
                <a href="${e.link}" class="ep-settings-link ep-settings-font-color" role="tab" data-toggle="tab">\n      
                    <div class="ep-block ep-block-flex ep-settings-payment-block ep-settings-align-div">\n        
                        <div class="ep-settings-align-div">\n          
                            <div class="ep-settings-icon">\n            
                                <img src="${e.icon}" alt="mp gateway icon" />\n         
                            </div>\n\n          
                            <span class="ep-settings-subtitle-font-size ep-settings-margin-title-payment">\n 
                                <b>${e.title_gateway}</b> - ${e.description}\n          
                            </span>\n\n   
                            <div style="display: flex;">\n        
                                <span class="${t}">${n}</span>\n   
                                <div class="ep-settings-icon-body">\n          
                                    <div class="ep-settings-icon-config"></div>\n        
                                </div>\n    
                            </div>\n     
                        </div>\n    
                    </div>\n   
                </a>\n  `;
            return (new DOMParser).parseFromString(s, "text/html").firstChild
        }

        mpSettingsAccordionStart();
        mpGetPaymentMethods();
    });
})();