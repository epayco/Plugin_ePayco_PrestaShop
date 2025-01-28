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
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }
            build() {
                this.appendChild(this.createContainer())
            }
            createContainer() {
                const t = document.createElement("div");
                return t.classList.add("ep-terms-and-conditions-container"),
                    t.setAttribute("data-cy", "terms-and-conditions-container"),
                    //t.appendChild(this.createText()),
                    //t.appendChild(this.createLink()),
                    t.appendChild(this.createLabel()),
                    t
            }
            createText() {
                const t = document.createElement("span");
                return t.classList.add("ep-terms-and-conditions-text"), t.innerHTML = this.getAttribute("description"), t
            }
            createLink() {
                const t = document.createElement("a");
                return t.classList.add("ep-terms-and-conditions-link"), t.innerHTML = this.getAttribute("link-text"), t.href = this.getAttribute("link-src"), t.target = "blank", t
            }
            createLabel(){
                const t = document.createElement("label");
                const s = this.createInput(t);
                const pp = document.createElement("p");
                let label =this.getAttribute("label")+
                    "<a target='_blank' href=\""+
                    this.getAttribute("link-src")+"\">"+
                    this.getAttribute("link-text")+"</a>"+
                    this.getAttribute("and_the")+
                    "<a target='_blank' href=\""+
                    this.getAttribute("link-condiction-src")+"\">"+
                    this.getAttribute("link-condiction-text")+"</a>"+
                    this.getAttribute("description")

                ;
                pp.innerHTML = label;
                return t.appendChild(s),
                    t.appendChild(pp),
                    t
            }
            createInput(t) {
                const n = document.createElement("input");
                return n.type = "checkbox",
                    t.appendChild(n),
                    n.addEventListener("click", (() => {
                        if (n.checked) {
                            n.parentElement.parentElement.classList.remove("ep-error")
                        } else {
                            n.parentElement.parentElement.classList.add("ep-error")
                        }
                    })),
                    n
            }
        }
        customElements.define("terms-and-conditions", t)
    })(),
    (() => {
        class t extends HTMLElement {
            static get observedAttributes() {
                return ["title", "description", "retryButtonText"]
            }

            connectedCallback() {
                this.build()
            }

            attributeChangedCallback() {
                this.firstElementChild && (this.removeChild(this.firstElementChild), this.build())
            }

            build() {
                const t = this.createAlertDetails(), e = this.createCardContent();
                t.appendChild(e), this.appendChild(t)
            }

            createAlertDetails() {
                const t = document.createElement("div");
                return t.classList.add("mp-alert-details-card"), t
            }

            createCardContent() {
                const t = document.createElement("div");
                t.classList.add("mp-alert-details-card-content");
                const e = document.createElement("div");
                e.classList.add("mp-alert-details-card-content-left");
                const i = document.createElement("div");
                i.classList.add("mp-alert-details-card-content-right"), t.appendChild(e), t.appendChild(i);
                const n = this.createBadge(), s = this.createTitle(), a = this.createDescription(),
                    r = this.createRetryButton();
                return e.appendChild(n), i.appendChild(s), i.appendChild(a), i.appendChild(r), t
            }

            createBadge() {
                const t = document.createElement("div");
                return t.innerHTML = "x", t.classList.add("mp-alert-details-badge"), t
            }

            createTitle() {
                const t = document.createElement("p");
                return t.innerHTML = this.getAttribute("title"), t.classList.add("mp-alert-details-title"), t
            }

            createDescription() {
                const t = document.createElement("p");
                return t.innerHTML = this.getAttribute("description"), t.classList.add("mp-alert-details-description"), t
            }

            createRetryButton() {
                const t = this.getAttribute("retryButtonText"), e = document.createElement("button");
                return e.classList.add("mp-alert-details-retry-button"), e.innerHTML = t, e.onclick = () => document.location.reload(), e
            }
        }
        customElements.define("alert-details", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }
            build() {
                const t = this.createContainer();
                this.appendChild(t)
            }
            createContainer() {
                const t = document.createElement("div");
                const e = this.createLabel(this.getAttribute("label-message")),
                    i = this.createHelper(this.getAttribute("helper-message")),
                    n = this.createHiddenField(this.getAttribute("hidden-id")),
                    s = this.createInputDocument(i, n);
                return t.classList.add("ep-input-select-container"),
                    t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }
            createInputDocument(i, n) {
                const t = document.createElement("div");
                t.classList.add("ep-input-element"),
                    t.style.display="flex",
                    t.style.gap="25px",
                    t.setAttribute("data-cy", "input-document-container"),
                    t.setAttribute("id", "form-checkout__identificationNumber-container");
                const ii = document.createElement("div");
                ii.classList.add("ep-input-select-input"),
                    ii.style.width="125px",
                ii.setAttribute("id",this.getAttribute("select-id"));
                const aa = document.createElement("div");
                aa.style.width="260px",
                    aa.setAttribute("id",this.getAttribute("input-id"));
                const e = this.createLabel(this.getAttribute("label-message")),
                    s = this.createInput(i,ii, n, aa, t),
                    c = this.createDocument(aa, t);
                return t.appendChild(s),
                    t.appendChild(aa),
                    t
            }
            createLabel(t) {
                const e = document.createElement("input-label");
                return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
            }
            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "mp-doc-cellphone-helper"), e
            }
            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }
            createInput(t,i,e,aa, tt) {
                var documents;
                var type;
                //const lang = wc_epayco_checkout_components_params.lang;
                const lang = "en";
                    if(lang === 'es'){
                        type = 'Tipo';
                        documents = [
                                {"id":"Tipo"},
                                {"id":"CC"},
                                {"id":"CE"},
                                {"id":"NIT"},
                                {"id":"TI"},
                                {"id":"PPN"},
                                {"id":"SSN"},
                                {"id":"LIC"},
                                {"id":"DNI"}
                                ];
                    }else{
                        type = 'Type';
                        documents = [
                                {"id":"Type"},
                                {"id":"CC"},
                                {"id":"CE"},
                                {"id":"NIT"},
                                {"id":"TI"},
                                {"id":"PPN"},
                                {"id":"SSN"},
                                {"id":"LIC"},
                                {"id":"DNI"}
                                ];
                    }
                const n = JSON.parse(this.getAttribute("documents")),
                //const n = documents,
                    s = this.getAttribute("validate"),
                    r = this.createSelect(i, t, n, s, aa, tt);
                return r.addEventListener("change", ((e) => {
                        const selectedValue = e.target.value;
                        if(selectedValue == 'Type' || selectedValue == 'Tipo'){
                            aa.querySelector("input").classList.add("ep-error");
                            tt.querySelector("select").classList.add("ep-error");
                        }else{
                            let n = i.parentElement.querySelector("input");
                            switch (i.querySelector("select").value) {
                                case "NIT":
                                    n.value = n.value.replace(/\D/g, '');
                                    n.maxLength = "10";
                                    n.minLength = "7";
                                    break;
                                case "CC":
                                    n.value = n.value.replace(/\D/g, '');
                                    n.maxLength = "15";
                                    n.minLength = "5";
                                    break;
                                case "CE":
                                    n.value = n.value.replace(/\D/g, '');
                                    n.maxLength = "8";
                                    n.minLength = "4";
                                    break;
                                case "TI":
                                    n.value = n.value.replace(/\D/g, '');
                                    n.maxLength = "20";
                                    n.minLength = "4";
                                    break;
                                case "PPN":
                                    n.maxLength = "12";
                                    n.minLength = "4";
                                    break;
                                case "SSN":
                                    n.value = n.value.replace(/\D/g, '');
                                    n.maxLength = "9";
                                    n.minLength = "9";
                                    break;
                                case "LIC":
                                    n.value = n.value.replace(/\D/g, '');
                                    n.maxLength = "20";
                                    n.minLength = "1";
                                    break;
                                case "DNI":
                                    n.maxLength = "20";
                                    n.minLength = "1";
                                    break;
                            }
                            if (n.value.length < n.minLength) {
                                aa.querySelector("input").classList.add("ep-error");
                                tt.querySelector("select").classList.add("ep-error");
                                tt.parentElement.querySelector("input-helper > div").style.display = "flex";
                            } else {
                                aa.querySelector("input").classList.remove("ep-error");
                                tt.querySelector("select").classList.remove("ep-error");
                                tt.parentElement.querySelector("input-helper > div").style.display = "none";
                            }
                        }
                        i.classList.remove("ep-focus");
                    })),
                    i.appendChild(r),
                    i
            }
            createSelect(i, ee, n, s) {
                const t = document.createElement("select"),
                    e = this.getAttribute("name");
                t.classList.add("ep-input-select-bank"),
                    t.setAttribute("id", e),
                    t.setAttribute("name", e),
                    t.setAttribute("data-checkout", this.getAttribute("select-data-checkout")),
                    t.setAttribute("data-cy", "select-countrye");
                if (this.getAttribute("default-option")) {
                    const e = document.createElement("option");
                    e.setAttribute("selected", "selected"),
                        e.setAttribute("hidden", "hidden"),
                        e.innerHTML = this.getAttribute("default-option"),
                        t.appendChild(e)
                }
                return n && 0 !== i.length && n.forEach((e => {
                    t.appendChild(this.createOption(e))
                })),n && (t.addEventListener("focus", (() => {
                    i.classList.add("ep-focus"),
                        ee.firstElementChild.style.display = "none"
                })), t.addEventListener("focusout", (() => {
                    i.classList.remove("ep-focus"),
                        ee.firstElementChild.style.display = "none"
                }))),
                    t
            }
            createOption(t) {
                const e = document.createElement("option");
                return e.innerHTML = t.id, e.value = t.id, e
            }
            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("input-name")),
                    n.setAttribute("data-checkout", this.getAttribute("input-data-checkout")),
                    n.setAttribute("data-cy", "input-document"),
                    n.classList.add("ep-cellphone"),
                    n.type = "text",
                    n.inputMode = "text",
                    n.placeholder = this.getAttribute("placeholder"),
                    n.addEventListener("focus", (() => {
                        i.querySelector("select").parentElement.classList.add("ep-focus"),
                            t.querySelector("input").classList.add("ep-focus")
                        i.parentElement.querySelector("input-helper > div").style.display = "none"
                    })),
                    n.addEventListener("input", (() => {
                        switch (i.querySelector("select").value) {
                            case "NIT":
                                n.value = n.value.replace(/\D/g, '');
                                n.maxLength = "10";
                                n.minLength = "7";
                                break;
                            case "CC":
                                n.value = n.value.replace(/\D/g, '');
                                n.maxLength = "15";
                                n.minLength = "5";
                                break;
                            case "CE":
                                n.value = n.value.replace(/\D/g, '');
                                n.maxLength = "8";
                                n.minLength = "4";
                                break;
                            case "TI":
                                n.value = n.value.replace(/\D/g, '');
                                n.maxLength = "20";
                                n.minLength = "4";
                                break;
                            case "PPN":
                                n.maxLength = "12";
                                n.minLength = "4";
                                break;
                            case "SSN":
                                n.value = n.value.replace(/\D/g, '');
                                n.maxLength = "9";
                                n.minLength = "9";
                                break;
                            case "LIC":
                                n.value = n.value.replace(/\D/g, '');
                                n.maxLength = "20";
                                n.minLength = "1";
                                break;
                            case "DNI":
                                n.maxLength = "20";
                                n.minLength = "1";
                                break;
                        }
                        if (n.value.length < n.minLength) {
                            i.querySelector("select").parentElement.classList.add("ep-error"),
                                t.querySelector("input").classList.add("ep-error"),
                                i.parentElement.querySelector("input-helper > div").style.display = "flex";
                        } else {
                            t.querySelector("input").classList.remove("ep-error"),
                                i.querySelector("select").parentElement.classList.remove("ep-error"),
                                i.parentElement.querySelector("input-helper > div").style.display = "none";
                        }
                    })),
                    n.addEventListener("change", (() => {
                        //n.value = n.value.replace(/\D/g, '');
                    }))
                    , n.addEventListener("focusout", (() => {
                    var type;
                    //const lang = wc_epayco_checkout_components_params.lang;
                    const lang = "en";
                    if(lang === 'es'){
                        type = 'Tipo';
                    }else{
                        type = 'Type';
                    }
                    void 0 !== ((n.value !=='' &&  i.querySelector("select").value !==  type && n.value.length >= n.minLength)  ? (
                                t.querySelector("input").classList.remove("ep-error"),
                                    i.querySelector("select").parentElement.classList.remove("ep-error"),
                                    i.parentElement.querySelector("input-helper > div").style.display = "none",
                                    n.setAttribute("name", this.getAttribute("input-name"))) :
                            (
                                i.querySelector("select").parentElement.classList.add("ep-error"),
                                    t.querySelector("input").classList.add("ep-error"),
                                    i.parentElement.querySelector("input-helper > div").style.display = "flex",
                                    n.setAttribute("name", this.getAttribute("flag-error"))
                            )
                    )
                })),
                    t.appendChild(n),
                    n
            }
        }
        customElements.define("input-document", t)
    })(),
    (() => {
    class t extends HTMLElement {
        connectedCallback() {
            this.build()
        }
        async build() {
            const t = this.createContainer();
            this.appendChild(t)
        }
        createContainer() {
            const t = document.createElement("div");
            const e = this.createLabel(this.getAttribute("label-message")),
                i = this.createHelper(this.getAttribute("helper-message")),
                n = this.createHiddenField(this.getAttribute("hidden-id")),
                s =  this.createInputDocument(i, n);
            return t.classList.add("ep-input-select-container"),
                t.appendChild(e),
                t.appendChild(s),
                t.appendChild(n),
                t.appendChild(i),
                t
        }
        createInputDocument(i, n) {
            const t = document.createElement("div");
            t.classList.add("em-input-element"),
                t.style.display="flex",
                t.style.gap="25px",
                t.setAttribute("data-cy", "input-document-container"),
                t.setAttribute("id", "form-checkout__identificationCellphone-container");
            const ii = document.createElement("div");
            //ii.classList.add("ep-input"),
            ii.classList.add("ep-input-select-input"),
            ii.style.width="125px",
            ii.setAttribute("id",this.getAttribute("select-id"));
            const aa = document.createElement("div");
            aa.style.width="260px",
            //aa.classList.add("ep-input"),
                aa.setAttribute("id",this.getAttribute("input-id"));
            const e = this.createLabel(this.getAttribute("label-message")),
                s = this.createInput(i,ii, n,aa,t),
                c = this.createDocument(aa, t);
            return t.appendChild(s),
                t.appendChild(aa),
                t
        }
        createLabel(t) {
            const e = document.createElement("input-label");
            return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
        }
        createHelper(t) {
            const e = document.createElement("input-helper");
            return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "ep-doc-cellphone-helper"), e
        }
        createHiddenField(t) {
            const e = document.createElement("input");
            return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
        }
        createInput(t,i,e,aa,tt) {
            let cellphoneList = [
                {
                    "name": "Colombia",
                    "name_es": "Colombia",
                    "continent_en": "South America",
                    "continent_es": "América del Sur",
                    "capital_en": "Bogota",
                    "capital_es": "Bogotá",
                    "dial_code": "+57",
                    "id": "CO",
                    "code_3": "COL",
                    "tld": ".co",
                    "km2": 1141748,
                    "emoji": "🇨🇴"
                },
                {
                    "name": "Afghanistan",
                    "name_es": "Afganistán",
                    "continent_en": "Africa",
                    "continent_es": "África",
                    "capital_en": "Kabul",
                    "capital_es": "Kabul",
                    "dial_code": "+93",
                    "id": "AF",
                    "code_3": "AFG",
                    "tld": ".af",
                    "km2": 652230,
                    "emoji": "🇦🇫"
                }
            ]
            const n = cellphoneList.map(item => `${item.emoji} ${item.dial_code}`),
                //const n = JSON.parse(this.getAttribute("documents")),
                s = this.getAttribute("validate"),
                r = this.createSelect(i, t, n, s);
            return i.appendChild(r),
                r.addEventListener("change", ((e) => {
                    i.classList.remove("ep-focus"),
                        i.classList.remove("ep-error");
                    const selectedValue = e.target.value;
                    const codigoPais = selectedValue.split("+")[1];
                    let n = i.parentElement.querySelector("input");
                    const regexColombia = /^3\d{9}$/;
                    if(codigoPais == 57){
                        if (regexColombia.test(n.value)) {
                            aa.querySelector("input").classList.remove("ep-error");
                            tt.querySelector("select").parentElement.classList.remove("ep-error");
                            tt.parentElement.querySelector("input-helper > div").style.display = "none";
                        } else {
                            aa.querySelector("input").classList.add("ep-error");
                            tt.querySelector("select").parentElement.classList.add("ep-error");
                            tt.parentElement.querySelector("input-helper > div").style.display = "flex";
                        }
                    }else{
                        const digitCount = i.querySelector("select").value.replace(/[^0-9]/g, "").length;
                        const cellphoneDigits = digitCount+n.value.length;
                        if (cellphoneDigits < 10) {
                            aa.querySelector("input").classList.add("ep-error");
                            tt.querySelector("select").parentElement.classList.add("ep-error");
                            tt.parentElement.querySelector("input-helper > div").style.display = "flex";
                        } else {
                            aa.querySelector("input").classList.remove("ep-error");
                            tt.querySelector("select").parentElement.classList.remove("ep-error");
                            tt.parentElement.querySelector("input-helper > div").style.display = "none";
                        }
                    }
                    i.classList.remove("ep-focus");
                    //i.classList.remove("mp-error");
                })),
                i
        }


        createSelect(i, ee, n, s) {
            const t = document.createElement("select"),
                e = this.getAttribute("name");
            t.classList.add("ep-input-select-bank"),
                t.setAttribute("id", e),
                t.setAttribute("name", e),
                t.setAttribute("data-checkout", this.getAttribute("select-data-checkout")),
                t.setAttribute("data-cy", "select-countrye");
            if (this.getAttribute("default-option")) {
                const e = document.createElement("option");
                e.setAttribute("selected", "selected"),
                    e.setAttribute("hidden", "hidden"),
                    e.innerHTML = this.getAttribute("default-option"),
                    t.appendChild(e)
            }
            return n && 0 !== i.length && n.forEach((e => {
                t.appendChild(this.createOption(e))
            })),n && (t.addEventListener("focus", (() => {
                i.classList.add("ep-focus"),
                    ee.firstElementChild.style.display = "none"
            })), t.addEventListener("focusout", (() => {
                i.classList.remove("ep-focus"),
                    ee.firstElementChild.style.display = "none"
            }))),
                t
        }
        createOption(t) {
            const e = document.createElement("option");
            return e.innerHTML = t, e.value = t, e
        }
        createDocument(t, i) {
            const n = document.createElement("input");
            var cellphoneValidated = false;
            return n.setAttribute("name", this.getAttribute("input-name")),
                n.setAttribute("data-checkout", this.getAttribute("input-data-checkout")),
                n.setAttribute("data-cy", "input-cellphone"),
                n.classList.add("ep-cellphone"),
                n.type = "text",
                n.inputMode = "text",
                n.maxLength = "15",
                n.minLength = "7",
                n.placeholder = this.getAttribute("placeholder"),
                n.addEventListener("focus", (() => {
                    i.querySelector("select").parentElement.classList.add("ep-focus"),
                        //t.querySelector("input").parentElement.classList.add("ep-focus")
                        n.classList.add("ep-focus")
                    i.parentElement.querySelector("input-helper > div").style.display = "none"
                })),
                n.addEventListener("input", (() => {
                    const codigoPais = i.querySelector("select").value.split("+")[1];
                    n.value = n.value.replace(/\D/g, '');
                    const regexColombia = /^3\d{9}$/;
                    if(codigoPais == 57){
                        if (regexColombia.test(n.value)) {
                            //t.querySelector("input").parentElement.classList.remove("ep-error"),
                            n.classList.remove("ep-error"),
                            i.querySelector("select").parentElement.classList.remove("ep-error"),
                            i.parentElement.querySelector("input-helper > div").style.display = "none";
                            cellphoneValidated = true;
                        } else {
                            i.querySelector("select").parentElement.classList.add("ep-error"),
                            //t.querySelector("input").parentElement.classList.add("ep-error"),
                            n.classList.add("ep-error"),
                            i.parentElement.querySelector("input-helper > div").style.display = "flex";
                            cellphoneValidated = false;
                        }
                    }else{
                        const digitCount = i.querySelector("select").value.replace(/[^0-9]/g, "").length;
                        const cellphoneDigits = digitCount+n.value.length;
                        if (cellphoneDigits < 10) {
                            i.querySelector("select").parentElement.classList.add("ep-error"),
                            //t.querySelector("input").parentElement.classList.add("ep-error"),
                            n.classList.add("ep-error"),
                            i.parentElement.querySelector("input-helper > div").style.display = "flex";
                            cellphoneValidated = false;
                        } else {
                            //t.querySelector("input").parentElement.classList.remove("ep-error"),
                            n.classList.remove("ep-error"),
                            i.querySelector("select").parentElement.classList.remove("ep-error"),
                            i.parentElement.querySelector("input-helper > div").style.display = "none";
                            cellphoneValidated = true;
                        }
                    }
                })),
                n.addEventListener("focusout", (() => {
                    //t.querySelector("input").parentElement.classList.add("ep-error");
                    n.classList.add("ep-error");
                    i.querySelector("select").parentElement.classList.remove("ep-error");
                    void 0 !== ((n.value !=='' && cellphoneValidated) ? (
                                //t.querySelector("input").parentElement.classList.remove("ep-error"),
                                n.classList.remove("ep-error"),
                                    i.querySelector("select").parentElement.classList.remove("ep-error"),
                                    i.parentElement.querySelector("input-helper > div").style.display = "none",
                                    n.setAttribute("name", this.getAttribute("input-name"))) :
                            (
                                i.querySelector("select").parentElement.classList.add("ep-error"),
                                    //t.querySelector("input").parentElement.classList.add("ep-error"),
                                    n.classList.add("ep-error"),
                                    i.parentElement.querySelector("input-helper > div").style.display = "flex",
                                    n.setAttribute("name", this.getAttribute("flag-error"))
                            )
                    )
                })),
                t.appendChild(n),
                n
        }


    }
    customElements.define("input-cellphone", t)
})(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }
            build() {
                const t = this.createContainer();
                this.appendChild(t)
            }
            createContainer() {
                const t = document.createElement("div");
                const e = this.createLabel(this.getAttribute("label-message")),
                    i = this.createHelper(this.getAttribute("helper-message")),
                    n = this.createHiddenField(this.getAttribute("hidden-id")),
                    s = this.createInputDocument(i, n);
                return t.classList.add("ep-input-select-container"),
                    t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }
            createInputDocument(i, n) {
                const t = document.createElement("div");
                t.classList.add("ep-input-element"),
                    t.style.display="flex",
                    t.style.gap="25px",
                    t.setAttribute("data-cy", "input-country-container"),
                    t.setAttribute("id", "form-checkout__identificationCountry-container");
                const ii = document.createElement("div");
                    ii.classList.add("ep-input-select-input"),
                    ii.style.maxWidth="155px",
                    ii.setAttribute("id",this.getAttribute("select-id"));
                const aa = document.createElement("div");
                    aa.style.maxWidth="230px",
                    aa.setAttribute("id",this.getAttribute("input-id"));
                const e = this.createLabel(this.getAttribute("label-message")),
                    s =  this.createInput(i,ii, n),
                    c = this.createDocument(aa, t);
                return t.appendChild(s),
                    t.appendChild(aa),
                    t
            }
            createLabel(t) {
                const tt = document.createElement("div");
                tt.style.display="grid",
                tt.style.gridTemplateColumns="repeat(2, 1fr)",
                tt.style.gap="30px";
                const e = document.createElement("input-label");
                e.setAttribute("message", t),
                e.style.width="155px",
                e.setAttribute("isOptional", "false");
                const ee = document.createElement("input-label");
                ee.style.width="230px",
                ee.setAttribute("message", this.getAttribute("placeholder")),
                    ee.setAttribute("isOptional", "false");
                return tt.appendChild(e),
                    tt.appendChild(ee),
                    tt
            }
            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "ep-doc-country-helper"), e
            }
            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }

            createInput(t,i,e) {
                let countryList = [
                    {
                        "name": "Colombia",
                        "name_es": "Colombia",
                        "continent_en": "South America",
                        "continent_es": "América del Sur",
                        "capital_en": "Bogota",
                        "capital_es": "Bogotá",
                        "dial_code": "+57",
                        "id": "CO",
                        "code_3": "COL",
                        "tld": ".co",
                        "km2": 1141748,
                        "emoji": "🇨🇴"
                    },
                    {
                        "name": "Afghanistan",
                        "name_es": "Afganistán",
                        "continent_en": "Africa",
                        "continent_es": "África",
                        "capital_en": "Kabul",
                        "capital_es": "Kabul",
                        "dial_code": "+93",
                        "id": "AF",
                        "code_3": "AFG",
                        "tld": ".af",
                        "km2": 652230,
                        "emoji": "🇦🇫"
                    }
                ]
                const lang = "en";
                const n = countryList.map(item => {
                        if(lang == 'es'){
                            return  `${item.name_es}`
                        }else{
                            return  `${item.name}`
                        }
                    }),
                    s = this.getAttribute("validate"),
                    r = this.createSelect(i, t, countryList, s);
                return i.appendChild(r),
                    i
            }

            createSelect(i, ee, n, s) {
                const t = document.createElement("select"),
                    e = this.getAttribute("name");
                t.classList.add("ep-input-select-bank"),
                    t.setAttribute("id", e),
                    t.setAttribute("name", e),
                    t.setAttribute("data-checkout", this.getAttribute("select-data-checkout")),
                    t.setAttribute("data-cy", "select-countrye");
                if (this.getAttribute("default-option")) {
                    const e = document.createElement("option");
                     e.setAttribute("selected", "selected"),
                     e.setAttribute("hidden", "hidden"),
                     e.innerHTML = this.getAttribute("default-option"),
                     t.appendChild(e)
                }
                return n && 0 !== i.length && n.forEach((e => {
                    t.appendChild(this.createOption(e))
                })),n && (t.addEventListener("focus", (() => {
                    i.classList.add("ep-focus"),
                        ee.firstElementChild.style.display = "none"
                })), t.addEventListener("focusout", (() => {
                    i.classList.remove("ep-focus"),
                        ee.firstElementChild.style.display = "none"
                }))),
                    t
            }


            createOption(t) {
                const e = document.createElement("option");
                return e.innerHTML = t.name, e.value = t.id, e
            }
            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("input-name")),
                    n.setAttribute("data-checkout", this.getAttribute("input-data-checkout")),
                    n.setAttribute("data-cy", "input-country"),
                    n.classList.add("ep-cellphone"),
                    n.type = "text",
                    n.inputMode = "text",
                    n.placeholder = this.getAttribute("placeholder"),
                    n.addEventListener("focus", (() => {
                        i.querySelector("select").parentElement.classList.add("ep-focus"),
                           // t.querySelector("input").parentElement.classList.add("ep-focus")
                        i.parentElement.querySelector("input-helper > div").style.display = "none"
                    })),
                    n.addEventListener("input", (() => {
                        //n.value = n.value.replace(/\D/g, '');
                        if (n.value =='') {
                            i.querySelector("select").parentElement.classList.add("ep-error"),
                                t.querySelector("input").classList.add("ep-error"),
                                i.parentElement.querySelector("input-helper > div").style.display = "flex";
                        } else {
                               t.querySelector("input").classList.remove("ep-error"),
                                i.querySelector("select").parentElement.classList.remove("ep-error"),
                                i.parentElement.querySelector("input-helper > div").style.display = "none";
                        }
                    }))
                    , n.addEventListener("focusout", (() => {
                    t.querySelector("input").classList.add("ep-error");
                    i.querySelector("select").parentElement.classList.remove("ep-error");
                    void 0 !== (n.value !=='' ? (
                                   t.querySelector("input").classList.remove("ep-error"),
                                    i.querySelector("select").parentElement.classList.remove("ep-error"),
                                    i.parentElement.querySelector("input-helper > div").style.display = "none",
                                    n.setAttribute("name", this.getAttribute("input-name"))) :
                            (
                                i.querySelector("select").parentElement.classList.add("ep-error"),
                                    t.querySelector("input").classList.add("ep-error"),
                                    i.parentElement.querySelector("input-helper > div").style.display = "flex",
                                    n.setAttribute("name", this.getAttribute("flag-error"))
                            )
                    )
                })),
                    t.appendChild(n),
                    n
            }


        }
        customElements.define("input-country", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                const t = this.createInputDocument();
                this.appendChild(t)
            }

            createInputDocument() {
                const t = document.createElement("div");
                t.classList.add("ep-input-element"),
                    t.setAttribute("data-cy", "input-name-container");
                const e = this.createLabel(this.getAttribute("labelMessage")),
                    i = this.createHelper(this.getAttribute("helperMessage")),
                    n = this.createHiddenField(this.getAttribute("hiddenId")),
                    s = this.createInput(i, n);
                return t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }

            createLabel(t) {
                const e = document.createElement("input-label");
                return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
            }

            createInput(t, e) {
                const i = document.createElement("div");
                //i.classList.add("ep-input"),
                i.setAttribute("id", "form-checkout__identificationName-container");
                const s = this.getAttribute("validate"),
                    c = this.createDocument(i, t);
                i.appendChild(c);
                return i
            }


            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }

            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("inputName")),
                    n.setAttribute("data-checkout", this.getAttribute("inputDataCheckout")),
                    n.setAttribute("data-cy", "card[name]"),
                    n.classList.add("ep-cellphone"),
                    n.setAttribute("data-payco", "card[name]"),
                    n.placeholder = this.getAttribute("placeholder"),
                    n.type = "text",
                    n.inputMode = "text",
                    n.addEventListener("input", (() => {
                        const regex = /^[A-Za-z\s]*$/;
                        if (!regex.test(n.value)) {
                            n.value = n.value.replace(/[^A-Za-z\s]/g, '');
                        }
                    })),
                    n.addEventListener("focus", (() => {
                        n.classList.add("ep-focus"),
                        t.classList.remove("ep-error"),
                        i.firstElementChild.style.display = "none"
                    })),
                    n.addEventListener("focusout", (() => {
                        n.classList.remove("ep-focus");
                        if (n.value !== '') {
                            if(n.value.length >= 2){
                                n.classList.remove("ep-error");
                                i.firstElementChild.style.display = "none";
                                n.setAttribute("name", this.getAttribute("inputName"));
                            }else{
                                n.classList.add("ep-error");
                                i.firstElementChild.style.display = "flex";
                                n.setAttribute("name", this.getAttribute("flagError"));
                            }
                        } else {
                            n.classList.add("ep-error");
                            i.firstElementChild.style.display = "flex";
                            n.setAttribute("name", this.getAttribute("flagError"));
                        }
                    })), n
            }

            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("inputId", "mp-doc-name-helper"), e
            }
        }
        customElements.define("input-name", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                const t = this.createInputDocument();
                this.appendChild(t)
            }

            createInputDocument() {
                const t = document.createElement("div");
                t.classList.add("ep-input-element"),
                    t.setAttribute("data-cy", "input-address-container");
                const e = this.createLabel(this.getAttribute("labelMessage")),
                    i = this.createHelper(this.getAttribute("helperMessage")),
                    n = this.createHiddenField(this.getAttribute("hiddenId")),
                    s = this.createInput(i, n);
                return t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }

            createLabel(t) {
                const e = document.createElement("input-label");
                return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
            }

            createInput(t, e) {
                const i = document.createElement("div");
                //i.classList.add("ep-input"),
                i.setAttribute("id", "form-checkout__identificationAddress-container");
                const s = this.getAttribute("validate"),
                    c = this.createDocument(i, t);
                i.appendChild(c);
                return i
            }


            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }

            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("inputName")),
                    n.setAttribute("data-checkout", this.getAttribute("inputDataCheckout")),
                    n.setAttribute("data-cy", "input-address"),
                    n.classList.add("ep-cellphone"),
                    n.placeholder = this.getAttribute("placeholder"),
                    n.type = "text",
                    n.inputMode = "text",
                    n.addEventListener("focus", (() => {
                        n.classList.add("ep-focus"),
                        n.classList.remove("ep-error"),
                        i.firstElementChild.style.display = "none"
                    })),
                    n.addEventListener("focusout", (() => {
                        n.classList.remove("ep-focus");
                        void 0 !== (n.value !=='' ? (t.classList.remove("ep-error"),
                                    i.firstElementChild.style.display = "none",
                                    n.setAttribute("eame", this.getAttribute("inputName"))) :
                                (
                                    n.classList.add("ep-error"),
                                        i.firstElementChild.style.display = "flex",
                                        n.setAttribute("name", this.getAttribute("flagError"))
                                )
                        )
                    })), n
            }

            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "ep-doc-address-helper"), e
            }
        }
        customElements.define("input-address", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                const t = this.createInputDocument();
                this.appendChild(t)
            }

            createInputDocument() {
                const t = document.createElement("div");
                t.classList.add("ep-input-element"),
                    t.setAttribute("data-cy", "input-email-container");
                const e = this.createLabel(this.getAttribute("labelMessage")),
                    i = this.createHelper(this.getAttribute("helperMessage")),
                    n = this.createHiddenField(this.getAttribute("hiddenId")),
                    s = this.createInput(i, n);
                return t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }

            createLabel(t) {
                const e = document.createElement("input-label");
                return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
            }

            createInput(t, e) {
                const i = document.createElement("div");
                //i.classList.add("ep-input"),
                    i.setAttribute("id", "form-checkout__identificationEmail-container");
                const s = this.getAttribute("validate"),
                    c = this.createDocument(i, t);
                i.appendChild(c);
                return i
            }


            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }

            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("inputName")),
                    n.setAttribute("data-checkout", this.getAttribute("inputDataCheckout")),
                    n.setAttribute("data", "email"),
                    n.classList.add("ep-cellphone"),
                    n.setAttribute("data-payco", "card[email]"),
                    n.placeholder = this.getAttribute("placeholder"),
                    n.type = "text",
                    n.inputMode = "text",
                    n.addEventListener("focus", (() => {
                        n.classList.add("ep-focus"),
                        n.classList.remove("ep-error"),
                        i.firstElementChild.style.display = "none"
                    })),
                    n.addEventListener("input", (() => {
                        if (!this.validateEmail(n.value)) {
                            n.classList.add("ep-error"),
                            //t.classList.add("ep-error"),
                            i.firstElementChild.style.display = "flex",
                            n.setAttribute("name", this.getAttribute("flagError"))
                            //i.firstElementChild.textContent = "Por favor, introduce un email válido."; // Mensaje de error
                        } else {
                            //t.classList.remove("ep-error");
                            n.classList.remove("ep-error");
                            i.firstElementChild.style.display = "none",
                            n.setAttribute("name", this.getAttribute("inputName"))
                        }
                    })),
                    n.addEventListener("focusout", (() => {
                        //t.classList.remove("ep-focus");
                            n.classList.remove("ep-focus");
                        void 0 !== (n.value !=='' && this.validateEmail(n.value) ? (t.classList.remove("ep-error"),
                                    i.firstElementChild.style.display = "none",
                                    n.setAttribute("name", this.getAttribute("inputName"))) :
                                (
                                    //t.classList.add("ep-error"),
                                    n.classList.add("ep-error"),
                                    i.firstElementChild.style.display = "flex",
                                    n.setAttribute("name", this.getAttribute("flagError"))
                                )
                        )
                    })), n
            }

            validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Expresión regular básica para validar emails
                return re.test(String(email).toLowerCase());
            }

            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "ep-doc-name-helper"), e
            }

        }
        customElements.define("input-email", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                const t = this.createInputDocument();
                this.appendChild(t)
            }

            createInputDocument() {
                const t = document.createElement("div");

                t.classList.add("ep-input-element-secure"),
                    t.setAttribute("data-cy", "input-card-number-container");
                const e = this.createLabel(this.getAttribute("labelMessage")),
                    i = this.createHelper(this.getAttribute("helperMessage")),
                    n = this.createHiddenField(this.getAttribute("hiddenId")),
                    s = this.createInput(i, n);
                return t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }

            createLabel(t) {
                const e = document.createElement("input-label");
                return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
            }

            createInput(t, e) {
                const i = document.createElement("div");
                i.classList.add("ep-input"),
                    i.style.border = "1px solid rgba(0, 0, 0, .25)",
                    i.setAttribute("id", "form-checkout__cardNumber-container");
                const s = this.getAttribute("validate"),
                    c = this.createDocument(i, t);
                i.appendChild(c);
                const l = document.createElement("img");
                l.src = 'https://secure.epayco.co/img/credit-cards/disable.png',
                    l.id = 'card-logo',
                    l.classList.add('card-logo'),
                    l.style.marginRight = '10px';
                    l.style.width = '8%';
                i.appendChild(l);
                return i
            }


            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }

            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("input-card-number")),
                    n.setAttribute("data-checkout", this.getAttribute("input-data-checkout")),
                    n.setAttribute("data-payco", "card[number]"),
                    //n.classList.add("ep-cellphone"),
                    n.style.outline = "none",
                    n.style.flex = "48px",
                    n.style.border = "0px solid rgba(0, 0, 0, .25)",
                    n.style.borderRadius = "8px",
                    n.placeholder = this.getAttribute("placeholder"),
                    n.type = "text",
                    n.inputMode = "numeric",
                    n.addEventListener("focus", (() => {
                       // t.classList.add("ep-focus"),
                        t.classList.remove("ep-border"),
                            t.classList.remove("ep-error"),
                            i.firstElementChild.style.display = "none"
                    })),
                    n.addEventListener("input", (() => {
                        n.style.border = "0px solid rgba(0, 0, 0, .25)"
                        n.classList.add("ep-border")
                        const cardLogo = document.getElementById('card-logo');
                        n.value = n.value.replace(/\D/g, '');
                        const cardNumber = n.value.replace(/\s+/g, '');
                        if (/^4[0-9]{6,}$/.test(cardNumber)) {
                            cardLogo.src = 'https://secure.epayco.io/img/credit-cards/vs.png';
                            //cardLogo.style.display = 'block';
                        } else if (/^5[1-5][0-9]{5,}$/.test(cardNumber)) {
                            cardLogo.src = 'https://secure.epayco.io/img/credit-cards/mc.png';
                            //cardLogo.style.display = 'block';
                        } else if (/^3[47][0-9]{5,}$/.test(cardNumber)) {
                            cardLogo.src = 'https://secure.epayco.io/img/credit-cards/amex.png';
                            //cardLogo.style.display = 'block';
                        } else if (/^6(?:011|5[0-9]{2})[0-9]{3,}$/.test(cardNumber)) {
                            cardLogo.src = 'img/discover.png';
                            //cardLogo.style.display = 'block';
                        }

                        if (cardNumber === '') {
                            cardLogo.src = 'https://secure.epayco.co/img/credit-cards/disable.png';
                        }

                    })),
                    n.addEventListener("focusout", (() => {
                       // t.classList.remove("ep-focus");
                            t.classList.remove("ep-border");
                        void 0 !== (n.value !=='' ? (t.classList.remove("ep-error"),
                                    i.firstElementChild.style.display = "none",
                                    n.setAttribute("name", this.getAttribute("inputName"))) :
                                (
                                    t.classList.add("ep-error"),
                                        i.firstElementChild.style.display = "flex",
                                        n.setAttribute("name", this.getAttribute("flagError"))
                                )
                        )
                    })), n
            }



            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "mp-card-number-helper"), e
            }
        }
        customElements.define("input-card-number", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                const t = this.createInputDocument();
                this.appendChild(t)
            }

            createInputDocument() {
                const t = document.createElement("div");
                t.classList.add("ep-input-element-secure"),
                    t.setAttribute("data-cy", "input-security-code-container");
                const e = this.createLabel(this.getAttribute("labelMessage")),
                    i = this.createHelper(this.getAttribute("helperMessage")),
                    n = this.createHiddenField(this.getAttribute("hiddenId")),
                    s = this.createInput(i, n);
                return t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }

            createLabel(t) {
                const e = document.createElement("input-label");
                return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
            }

            createInput(t, e) {
                const i = document.createElement("div");
                i.style.height = "40px !important",
                    i.classList.add("ep-input"),
                    i.setAttribute("id", "form-checkout__securityCode-container");
                const s = this.getAttribute("validate"),
                    c = this.createDocument(i, t);
                i.appendChild(c);
                return i
            }


            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }

            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("inputName")),
                    n.setAttribute("data-checkout", this.getAttribute("input-data-checkout")),
                    n.setAttribute("data-payco", "card[cvc]"),
                    n.placeholder = this.getAttribute("placeholder"),
                    //n.classList.add("ep-cellphone"),
                    n.style="-webkit-text-security: disc;",
                    n.type = "text",
                    n.inputMode = "numeric",
                    n.autoComplete="off",
                    n.maxLength="4",
                    n.addEventListener("click",(()=>{
                        n.classList.add("ep-border")
                    })),
                    n.addEventListener("focus", (() => {
                       // t.classList.add("ep-focus"),
                        n.classList.remove("ep-border"),
                            t.classList.remove("ep-error"),
                            i.firstElementChild.style.display = "none"
                    })),
                    n.addEventListener("input", (() => {
                        n.classList.remove("ep-border");
                        n.value = n.value.replace(/\D/g, '');
                        if(n.value.length > 0){
                            if(n.value.length<3){
                                //securycode.lastChild.innerText="csciil"
                                n.style.border = "0px solid rgba(0, 0, 0, .25)",
                                t.classList.add("ep-error")
                                n.classList.remove("ep-border"),
                                i.firstElementChild.style.display = "flex"
                                n.setAttribute("name", this.getAttribute("flagError"))
                            }else{
                                i.firstElementChild.style.display = "none"
                                n.setAttribute("name", this.getAttribute("inputName"))
                            }
                        }else{
                            i.firstElementChild.style.display = "flex"
                            n.setAttribute("name", this.getAttribute("flagError"))
                        }
                    })),
                    n.addEventListener("focusout", (() => {
                       // t.classList.remove("ep-focus");
                        n.style.border = "1px solid rgba(0, 0, 0, .25)",
                        n.classList.remove("ep-border");
                        void 0 !== (n.value !=='' || n.value.length > 2 ? (t.classList.remove("ep-error"),
                                    i.firstElementChild.style.display = "none",
                                    n.setAttribute("name", this.getAttribute("inputName"))) :
                                (
                                    t.classList.add("ep-error"),
                                        i.firstElementChild.style.display = "flex",
                                        n.setAttribute("name", this.getAttribute("flagError"))
                                )
                        )
                    })), n
            }



            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "mp-security-code-helper"), e
            }
        }
        customElements.define("input-card-security-code", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                const t = this.createInputDocument();
                this.appendChild(t)
            }

            createInputDocument() {
                const t = document.createElement("div");
                t.classList.add("ep-input-element-secure"),
                    t.setAttribute("data-cy", "input-card-expiration-container");
                const e = this.createLabel(this.getAttribute("labelMessage")),
                    i = this.createHelper(this.getAttribute("helperMessage")),
                    n = this.createHiddenField(this.getAttribute("hiddenId")),
                    s = this.createInput(i, n);
                return t.appendChild(e),
                    t.appendChild(s),
                    t.appendChild(n),
                    t.appendChild(i),
                    t
            }

            createLabel(t) {
                const e = document.createElement("input-label");
                return e.setAttribute("message", t), e.setAttribute("isOptional", "false"), e
            }

            createInput(t, e) {
                const i = document.createElement("div");
                i.style.height = "40px !important",
                    i.classList.add("ep-input"),
                    i.setAttribute("id", "form-checkout__expirationDate-container");
                const s = this.getAttribute("validate"),
                    c = this.createDocument(i, t);
                i.appendChild(c);
                return i
            }


            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }

            createDocument(t, i) {
                const n = document.createElement("input");
                return n.setAttribute("name", this.getAttribute("inputName")),
                    n.setAttribute("data-checkout", this.getAttribute("input-data-checkout")),
                    n.setAttribute("data-payco", "card[date_exp]"),
                    //n.classList.add("ep-cellphone"),
                    n.placeholder = this.getAttribute("placeholder"),
                    n.type = "text",
                    n.inputMode = "numeric",
                    n.autoComplete="off",
                    n.maxLength="5",
                    n.addEventListener("click",(()=>{
                        n.classList.add("ep-border")
                    })),
                    n.addEventListener("focus", (() => {
                       // t.classList.add("ep-focus"),
                        n.classList.remove("ep-border"),
                            t.classList.remove("ep-error"),
                            i.firstElementChild.style.display = "none"
                    })),
                    n.addEventListener("input", (() => {
                        n.classList.remove("ep-border"),
                        this.validateDate(n)
                    })),
                    n.addEventListener("focusout", (() => {
                        n.classList.remove("ep-border");
                       // t.classList.remove("ep-focus");
                        void 0 !== ((n.value !=='' && this.validateDate(n)) ? (t.classList.remove("ep-error"),
                                    i.firstElementChild.style.display = "none",
                                    n.setAttribute("name", this.getAttribute("inputName"))) :
                                (
                                    t.classList.add("ep-error"),
                                        i.firstElementChild.style.display = "flex",
                                        n.setAttribute("name", this.getAttribute("flagError"))
                                )
                        )
                    })), n
            }

            validateDate(n) {
                var input = n.value
                var regex = /^(\d{2})\/(\d{2})$/;
                var partes = input.match(regex);
                input = input.replace(/\D/g, '');
                if (input.length > 2) {
                    input = input.slice(0, 2) + '/' + input.slice(2);
                }
                if (input.length > 5) {
                    input = input.slice(0, 5) + '/' + input.slice(5);
                }
                n.value = input.slice(0, 5);
                if (partes) {
                    var mes = parseInt(partes[1], 10);
                    var ano = parseInt(partes[2], 10);
                    var fechaActual = new Date();
                    var anoActual = fechaActual.getFullYear();
                    var mesActual = fechaActual.getMonth() + 1;
                    let a = document.querySelector(".ep-checkout-custom-card-column").querySelector("input-helper > div");
                    var expiration;
                    //const lang = wc_epayco_checkout_components_params.lang;
                    const lang = "en";
                    if(lang === 'es'){
                        expiration = 'Fecha invalida';
                    }else{
                        expiration = 'Invalid date';
                    }
                    if (2000+ano < anoActual) {
                        a.style.display = "flex";
                        a.lastChild.innerText=expiration
                        return false
                    }else{
                        if( 2000+ano === anoActual ){
                            if(mes < mesActual){
                                a.style.display = "flex";
                                a.lastChild.innerText=expiration
                                return false
                            }else{
                                a.style.display = "none";
                                return true
                            }
                        }else{
                            a.style.display = "none";
                            return true
                        }
                    }
                }
            }



            createHelper(t) {
                const e = document.createElement("input-helper");
                return e.setAttribute("isVisible", !1), e.setAttribute("message", t), e.setAttribute("input-id", "mp-card-expiration-helper"), e
            }
        }
        customElements.define("input-card-expiration-date", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                this.appendChild(this.createHelper())
            }

            createHelper() {
                const t = document.createElement("div");
                t.classList.add("ep-helper"),
                    t.setAttribute("id", this.getAttribute("input-id")),
                    t.setAttribute("data-cy", "helper-container"),
                    this.validateVisibility(t);
                const e = this.createIcon(), i = this.getAttribute("message"), n = this.createHelperMessage(i);
                return t.appendChild(e), t.appendChild(n), t
            }

            createIcon() {
                const t = document.createElement("div");
                return t.innerHTML = "x", t.classList.add("ep-helper-icon"), t
            }

            createHelperMessage(t) {
                const e = document.createElement("div");
                return e.innerHTML = t, e.classList.add("ep-helper-message"), e.setAttribute("data-cy", "helper-message"), e
            }

            validateVisibility(t) {
                let e = this.getAttribute("isVisible");
                "string" == typeof e && (e = "false" !== e),
                    t.style.display = e ? "flex" : "none"
            }
        }
        customElements.define("input-helper", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                this.appendChild(this.createLabel())
            }

            createLabel() {
                const t = document.createElement("div");
                t.classList.add("ep-input-label"), t.setAttribute("data-cy", "input-label");
                const e = this.getAttribute("message");
                t.innerHTML = e;
                let i = this.getAttribute("isOptional");
                if ("string" == typeof i && (i = "false" !== i), !i) {
                    const e = document.createElement("b");
                    e.innerHTML = "*", e.style = "color: red", t.appendChild(e)
                }
                return t
            }
        }
        customElements.define("input-label", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                this.appendChild(this.createContainer())
            }

            createContainer() {
                const t = document.createElement("div");
                return t.classList.add("ep-input-radio-container"), t.appendChild(this.createRadio()), t.appendChild(this.createLabel()), t
            }

            createRadio() {
                const t = document.createElement("input"), e = this.getAttribute("dataRate");
                return t.classList.add("ep-input-radio-radio"), t.type = "radio", t.id = this.getAttribute("identification"), t.name = this.getAttribute("name"), t.value = this.getAttribute("value"), t.setAttribute("data-cy", "input-radio"), e && t.setAttribute("dataRate", e), t
            }

            createLabel() {
                const t = document.createElement("label");
                return t.classList.add("ep-input-radio-label"), t.htmlFor = this.getAttribute("identification"), t
            }
        }
        customElements.define("input-radio", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                this.appendChild(this.createContainer())
            }

            createContainer() {
                const t = document.createElement("div");
                return t.classList.add("ep-input-select-container"),
                    t.appendChild(this.createLabel()),
                    t.appendChild(this.createInput()),
                    t.appendChild(this.createHelper()),
                    t
            }

            createInput() {
                const t = document.createElement("div");
                return t.classList.add("ep-input-select-input"),
                    t.appendChild(this.createSelect()), t
            }

            createSelect() {
                const t = document.createElement("select"),
                    e = this.getAttribute("name");
                t.classList.add("ep-input-select-bank"),
                    t.setAttribute("id", e),
                    t.setAttribute("name", e);
                const i = this.getAttribute("options") && JSON.parse(this.getAttribute("options"));
                if (this.getAttribute("default-option")) {
                    const e = document.createElement("option");
                    e.setAttribute("selected", "selected"), e.setAttribute("hidden", "hidden"), e.innerHTML = this.getAttribute("default-option"), t.appendChild(e)
                }
                return i && 0 !== i.length && i.forEach((e => {
                    t.appendChild(this.createOption(e))
                })),t
            }

            createOption(t) {
                const e = document.createElement("option");
                return e.innerHTML = t.description, e.value = t.id, e
            }

            createLabel() {
                const t = document.createElement("input-label"), e = this.getAttribute("optional");
                return t.setAttribute("message", this.getAttribute("label")), "false" === e ? t.setAttribute("isOptional", e) : t.setAttribute("isOptional", "true"), t
            }

            createHelper() {
                const t = document.createElement("input-helper");
                return t.setAttribute("isVisible", !1),
                    t.setAttribute("message", this.getAttribute("helper-message")),
                    t.setAttribute("input-id", "ep-doc-number-helper"),
                    t
            }

            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }
        }
        customElements.define("input-select", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                this.appendChild(this.createContainer())
            }

            createContainer() {
                const t = document.createElement("div");
                return t.classList.add("ep-input-select-container"),
                    t.appendChild(this.createLabel()),
                    t.appendChild(this.createInput()),
                    t.appendChild(this.createHelper()),
                    t
            }

            createInput() {
                const t = document.createElement("div");
                return t.classList.add("ep-input-select-input"),
                    t.appendChild(this.createSelect()), t
            }

            createSelect() {
                const t = document.createElement("select"),
                    e = this.getAttribute("name");
                t.classList.add("ep-input-select-bank"),
                    t.setAttribute("id", e),
                    t.setAttribute("name", e);
                const i = this.getAttribute("options") && JSON.parse(this.getAttribute("options"));
                if (this.getAttribute("default-option")) {
                    const e = document.createElement("option");
                    e.setAttribute("selected", "selected"), e.setAttribute("hidden", "hidden"), e.innerHTML = this.getAttribute("default-option"), t.appendChild(e)
                }
                return i && 0 !== i.length && i.forEach((e => {
                    t.appendChild(this.createOption(e))
                })),
                    t.addEventListener("change", (() => {
                        //const lang = wc_epayco_checkout_components_params.lang;
                        const lang = "en";
                        var text;
                        if(lang === 'es'){
                            text = 'Selecciona un banco';
                        }else{
                            text = 'Select a bank';
                        }
                        if(t.value == 0){
                            t.parentElement.classList.add("ep-error"),
                            t.parentElement.parentElement.querySelector("input-helper > div").style.display = "flex";
                            t.parentElement.parentElement.querySelector("input-helper > div").lastChild.innerHTML=text
                        }else{
                            t.parentElement.classList.remove("ep-error"),
                            t.parentElement.parentElement.querySelector("input-helper > div").style.display = "none";
                        }
                })),
                    t
            }

            createOption(t) {
                const e = document.createElement("option");
                return e.innerHTML = t.description, e.value = t.id, e
            }

            createLabel() {
                const t = document.createElement("input-label"), e = this.getAttribute("optional");
                return t.setAttribute("message", this.getAttribute("label")), "false" === e ? t.setAttribute("isOptional", e) : t.setAttribute("isOptional", "true"), t
            }

            createHelper() {
                const t = document.createElement("input-helper");
                return t.setAttribute("isVisible", !1),
                    t.setAttribute("message", this.getAttribute("helper-message")),
                    t.setAttribute("input-id", "ep-doc-number-helper"),
                    t
            }

            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }
        }
        customElements.define("input-banks", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                this.appendChild(this.createContainer())
            }

            createContainer() {
                const t = document.createElement("div");
                return t.classList.add("ep-input-select-container"),
                    t.appendChild(this.createLabel()),
                    t.appendChild(this.createInput()),
                    t.appendChild(this.createHelper()),
                    t
            }

            createInput() {
                const t = document.createElement("div")
                t.style.height = "48px";
                return t.classList.add("ep-input-select-input"),
                    t.appendChild(this.createSelect()), t
            }

            createSelect() {


                const t = document.createElement("select"),
                    e = this.getAttribute("name");
                t.classList.add("ep-input-select-bank"),
                    t.setAttribute("id", e),
                    t.setAttribute("name", e);

                const i = [];
                for (let il = 1; il <= 48; il++) {
                    i.push({
                        "id": il,
                        "description": il.toString()
                    });
                }
                return i && 0 !== i.length && i.forEach((e => {
                    t.appendChild(this.createOption(e))
                })), t
            }

            createOption(t) {
                const e = document.createElement("option");
                return e.innerHTML = t.description, e.value = t.id, e
            }

            createLabel() {
                var fees;
                    //const lang = wc_epayco_checkout_components_params.lang;
                    const lang = "en";
                    if(lang === 'es'){
                        fees = 'Cuotas';
                    }else{
                        fees = 'Fees';
                    }
                const t = document.createElement("input-label"),
                e = this.getAttribute("optional");
                return t.setAttribute("message", fees), "false" === e ? t.setAttribute("isOptional", e) : t.setAttribute("isOptional", "true"), t
            }

            createHelper() {
                const t = document.createElement("input-helper");
                return t.setAttribute("isVisible", !1),
                    t.setAttribute("message", this.getAttribute("helper-message")),
                    t.setAttribute("input-id", "ep-doc-number-helper"),
                    t
            }

            createHiddenField(t) {
                const e = document.createElement("input");
                return e.setAttribute("type", "hidden"), e.setAttribute("id", t), e
            }
        }
        customElements.define("input-installment", t)
    })(),
    (() => {
        class t extends HTMLElement {
            static get observedAttributes() {
                return ["columns", "name", "button-name", "bank-interest-text"]
            }

            constructor() {
                super(), this.index = 0, this.limit = 5, this.offset = this.limit, this.columns = null, this.total = 0
            }

            connectedCallback() {
                this.build()
            }

            attributeChangedCallback() {
                this.firstElementChild && (this.removeChild(this.firstElementChild), this.build())
            }

            build() {
                this.appendChild(this.createContainer())
            }

            setColumns() {
                return this.columns = JSON.parse(this.getAttribute("columns")), this
            }

            setTotal() {
                return this.total = this.columns.length, this
            }

            createContainer() {
                const t = document.createElement("div");
                return this.setColumns(), this.columns && (this.setTotal(), t.classList.add("ep-input-table-container"), t.setAttribute("data-cy", "input-table-container"), t.appendChild(this.createList()), t.appendChild(this.createBankInterestDisclaimer())), t
            }

            createList() {
                const t = document.createElement("div");
                t.classList.add("ep-input-table-list"),
                    t.setAttribute("data-cy", "input-table-list");
                const e = this.createLink();
                return e.onclick = () => this.handleLinkClick(t, e),
                    this.appendItems(this.columns, t, e, !1),
                    t.appendChild(e),
                    t
            }

            handleLinkClick(t, e) {
                this.appendItems(this.columns, t, e, true);
            }

            createItem(t) {
                const e = document.createElement("div");
                return e.classList.add("ep-input-table-item"), e.appendChild(this.createLabel(t)), e
            }

            createLabel(t) {
                const {id: e, value: i, rowText: n, rowObs: s, highlight: a, img: r, alt: c, dataRate: d} = t,
                    l = this.getAttribute("name"), o = document.createElement("div");
                return o.classList.add("ep-input-table-label"), o.appendChild(this.createOption(e, l, i, n, r, c, d)), s && o.appendChild(this.createRowObs(s, a)), o.onclick = () => {
                    document.getElementById(e).checked = !0
                }, o
            }

            createOption(t, e, i, n, s, a, r) {
                const c = document.createElement("div");
                return c.classList.add("ep-input-table-option"), c.appendChild(this.createRadio(t, e, i, r)), s ? c.appendChild(this.createRowTextWithImg(n, s, a)) : c.appendChild(this.createRowText(n)), c
            }

            createRadio(t, e, i, n) {
                const s = document.createElement("input-radio");
                return s.setAttribute("name", e),
                        s.setAttribute("value", i),
                        s.setAttribute("identification", t),
                        s.setAttribute("dataRate", n),
                        s.addEventListener("click", (() => {
                            s.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.querySelector('input-helper').querySelector('div').style.display='none'
                        })),
                        s
            }

            createRowText(t) {
                const e = document.createElement("span");
                return e.classList.add("ep-input-table-row-text"), e.innerHTML = t, e
            }

            createRowTextWithImg(t, e, i) {
                const n = document.createElement("span"), s = document.createElement("payment-method-logo");
                return s.setAttribute("src", e), s.setAttribute("alt", i), s.style.marginRight = "10px", n.classList.add("ep-input-table-row-text-image"), n.innerHTML = t, n.appendChild(s), n
            }

            createRowObs(t, e) {
                const i = document.createElement("span");
                return e ? i.classList.add("ep-input-table-row-obs-highlight") : i.classList.add("ep-input-table-row-obs"), i.innerHTML = t, i
            }

            createLink() {
                const t = document.createElement("span");
                t.classList.add("ep-input-table-container-link");
                const e = document.createElement("a");
                return e.setAttribute("id", "more-options"), e.classList.add("ep-input-table-link"), e.innerHTML = this.getAttribute("button-name"), t.appendChild(e), t
            }

            createBankInterestDisclaimer() {
                const t = document.createElement("div");
                t.classList.add("ep-input-table-bank-interest-container");
                const e = document.createElement("p");
                return e.classList.add("ep-input-table-bank-interest-text"), e.innerText = this.getAttribute("bank-interest-text"), t.appendChild(e), t
            }

            appendItems(t, e, i, n) {
                this.validateLimit();
                for (let s = this.index; s < this.limit; s += 1) n ? e.insertBefore(this.createItem(t[s]), i) : e.appendChild(this.createItem(t[s]));
                this.limit >= this.total && i.style.setProperty("display", "none", "important"), this.index += this.offset, this.limit += this.offset, this.validateLimit()
            }

            validateLimit() {
                this.limit > this.total && (this.limit = this.total)
            }
        }
        customElements.define("input-table", t)
    })(),
    (() => {
        class t extends HTMLElement {
            static get observedAttributes() {
                return ["src", "alt"]
            }

            connectedCallback() {
                this.build()
            }

            attributeChangedCallback() {
                this.firstElementChild && (this.removeChild(this.firstElementChild), this.build())
            }

            build() {
                this.appendChild(this.createContainer())
            }

            createContainer() {
                const t = document.createElement("div");
                return t.classList.add("ep-payment-method-logo-container"), t.appendChild(this.createImage()), t
            }

            createImage() {
                const t = document.createElement("img");
                return t.classList.add("ep-payment-method-logo-image"), t.alt = this.getAttribute("alt"), t.src = this.getAttribute("src"), t.onerror = t => t.target?.parentNode?.parentNode?.parentNode?.removeChild(t.target.parentNode.parentNode), t
            }
        }
        customElements.define("payment-method-logo", t)
    })(),
    (() => {
        class t extends HTMLElement {
            connectedCallback() {
                this.build()
            }

            build() {
                const t = this.createTestMode(), e = this.createCardHeader();
                t.appendChild(e), this.appendChild(t)
            }

            createTestMode() {
                const t = document.createElement("div");
                return t.classList.add("ep-test-mode-card"), t.setAttribute("data-cy", "test-mode-card"), t
            }


            createCardHeader() {
                const t = document.createElement("div");
                t.classList.add("ep-test-mode-card-content");
                const e = this.createBadge(), i = this.createTitle();
                return t.appendChild(e), t.appendChild(i), t
            }

            createBadge() {
                const t = document.createElement("div");
                return t.innerHTML = "!", t.classList.add("ep-test-mode-badge"), t
            }

            createTitle() {
                const t = document.createElement("p");
                return t.innerHTML = this.getAttribute("title"), t.classList.add("ep-test-mode-title"), t.setAttribute("data-cy", "test-mode-title"), t
            }
        }
        customElements.define("test-mode", t)
    })()
