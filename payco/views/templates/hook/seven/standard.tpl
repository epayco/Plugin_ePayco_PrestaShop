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
<div>
    <div id="loader_epayco" class="loader_epayco"></div>
    <div class="epayco-payment-option espacio" style="display: flex !important; justify-content: center !important; align-items: center !important; margin: 0 auto !important;">
        <img src="{$logo_url}" class="epayco-payment-logo" alt="Loguito" style="max-width: 100% !important;height: auto !important;max-height: 480px !important;">
    </div>

    <section class="espacio" style="text-align: left !important; margin-top: 20px !important; word-wrap: break-word !important;">
        <p style="font-size: 14px !important;margin-bottom: 20px !important;text-align: left !important;word-wrap: break-word !important;">
            ePayco: Paga con Tarjeta de crédito/débito nacional e internacional, PSE, Daviplata, Nequi, Paypal, Efectivo, Safetypay y muchos más.
        </p>
    </section>

    <form id="ep_standard_checkout" class="mp-checkout-form" method="post" action="{$redirect|escape:'html':'UTF-8'}">
        <div class="row mp-frame-checkout-seven">
        </div>
    </form>
</div>

<script type="text/javascript" src='https://epayco-checkout-testing.s3.amazonaws.com/checkout.preprod.js'></script>
<script>
    window.addEventListener('load', (event) => {
        const loader = document.getElementById('loader_epayco');
        loader.style.display = 'none';
        const checkout = document.getElementById('checkout');
        document.forms['ep_standard_checkout'].onsubmit = function (e) {
            e.preventDefault();
            loader.style.display = 'flex';
            checkout.classList.add("loader_epayco")
            fetch('index.php?fc=module&module=payco&controller=standard')
            .then( response => response.json())
            .then(function(response) {
                loader.style.display = 'none';
                checkout.classList.remove("loader_epayco")
                if (response.code == 200) {
                    if(response.preference.session.success){
                        let _external = response.preference.external === 'true';
                        const handlerNew = window.ePayco.checkout.configure({
                            sessionId: response.preference.session.data.sessionId,
                            external: _external
                        });
                        handlerNew.openNew()
                    }else{
                        window.location.href = 'pedido';
                    }
                }else{
                    window.location.href = 'pedido';
                }
            })
            .catch(function(error) {
                loader.style.display = 'none';
                checkout.classList.remove("loader_epayco")
                console.log(error)
            });
        };
    });
</script>

