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
{if $estado == 'Aceptada'}
    <div style="max-width: 1092px; margin: auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <!-- Barra superior negra con el logo -->
    <div style="background-color: #000; text-align: center; padding: 10px;">
        <img src="https://multimedia.epayco.co/plugins-sdks/logo-gris-1.png" alt="ePayco Logo"
             style="width: 100px;">
    </div>

    <!-- Contenedor principal -->
    <div style="padding: 20px;">

        <!-- Barra negra detrás del recibo -->
        <div style="
            background-color: #000;
            height: 30px;
            border-radius: 91px;
            position: relative;
            z-index: 0;
            max-width: 121%;
            margin: 0px auto -35px auto;
            width: 55%;
            " class="responsive-bar">
        </div>

        <!-- Contenedor del recibo -->
        <div style="
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            padding: 20px;
            position: relative;
            z-index: 1;
            max-width: 90%;
            margin: 20px auto;
            width: 50%;
        ">

            <!-- Encabezado -->
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="https://multimedia.epayco.co/plugins-sdks/check.png" alt="Éxito"
                     style="display: block; margin: auto; width: 70px; margin-bottom: 14px;">
                <h2 style="color: #67C940; font-size: 22px;">Transacción Aceptada</h2>
                <h4 style="color: #000; font-size: 17px; font-weight: bold;">Referencia ePayco #{$refPayco}</h4>
                <p style="color: #000; font-size: 14px;">{$fecha}</p>
            </div>

            <!-- Información de la transacción -->
            <div style="display: flex; flex-wrap: wrap; font-size: 14px; color: #333; line-height: 1.6;">
                <div style="width: 50%; padding: 5px 50px;">
                    <h5 style="font-weight: bold;">Medio de pago</h5>
                    <span style="color: #8b8383;">Método de pago</span><br>
                    <span>{$franquicia}</span>
                </div>
                <div style="width: 50%;padding: 5px 50px;margin-top:21px;">
                    <span style="color: #8b8383;">Autorización</span><br>
                    <span>{$autorizacion}</span>
                </div>
                <div style="width: 50%;padding: 5px 50px;margin-top: 3%;">
                    <span style="color: #8b8383;">Recibo</span><br>
                    <span>{$refPayco}</span>
                </div>
                <div style="width: 50%;padding: 5px 50px;margin-top: 13px;">
                    <span style="color: #8b8383;">Dirección IP</span><br>
                    <span>{$ip}</span>
                </div>
                <div style="width: 50%; padding: 5px 50px; margin-top:15px">
                    <span style="color: #8b8383;">Respuesta</span><br>
                    <span>{$respuesta}</span>
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap; font-size: 14px; color: #333; line-height: 1.6;">
                <div style="width: 54%;padding: 5px 50px;">
                    <h5 style="font-weight: bold;margin-bottom: 20px;margin-top: 7%;">Datos de la compra</h5>
                    <span style="color: #8b8383;">Referencia Comercio</span><br>
                    <span>{$refPayco}</span>
                </div>
                <div class="responsive-field" style="width: 49%;padding: 5px 64px;margin-top: 10%;margin-left: -8%;">
                    <span style="color: #8b8383;">Descripción</span><br>
                    <span>{$descripcion}</span>
                </div>
                <div style="width: 50%;padding: 5px 50px;margin-top: 3%;">
                    <span style="color: #8b8383;">Descuento</span><br>
                    {if $descuento == 0}
                        <span>$ 0 COP</span>
                    {else}
                        <span>$ {$descuento|number_format:2:',':'.'} COP</span>
                    {/if}
                </div>
                <div style="width: 50%;padding: 5px 50px;margin-top: 14px;margin-left: -1%;">
                    <span style="color: #8b8383;">Subtotal</span><br>
                    <span>$ {($valor - $descuento)|number_format:2:',':'.'} COP</span>
                </div>
                <div style="width: 50%; padding: 5px 50px;margin-top: 20px;">
                    <span style="color: #8b8383;">Valor total</span><br>
                    <span>$ {$valor|number_format:2:',':'.'} COP</span>
                </div>

                <!-- Botón para descargar el recibo -->

            </div>
        </div>
    </div>

    <p style="text-align: center;">
        <a href="{$baseurl}" target="_blank" class="btn btn-primary" style="
                       color: #fff;
                       background-color: #212121;
                       border-color: transparent;
                       max-width: 100%; /* Hace que el botón ocupe el 100% del contenedor en pantallas pequeñas */
                       width: 300px; /* Ajusta el tamaño del botón en pantallas grandes */
                       padding: 10px 20px;
                       display: inline-block;
                       margin: 0 auto; /* Centra el botón en su contenedor */
                       text-align: center;
                   ">
            Descargar Recibo ePayco
        </a>
    </p>

    <!-- Estilos para dispositivos móviles -->
    <style>
        @media (max-width: 768px) {
            div[style*="width: 50%;"] {
                width: 100% !important;
            }

            div[style*="max-width: 1092px;"] {
                padding: 10px !important;
            }

            .responsive-bar {
                width: 100% !important;
            }
        }
    </style>

    {elseif $estado == 'Pendiente'}
    <div style="max-width: 1092px; margin: auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

        <!-- Barra superior negra con el logo -->
        <div style="background-color: #000; text-align: center; padding: 10px;">
            <img src="https://multimedia.epayco.co/plugins-sdks/logo-gris-1.png" alt="ePayco Logo"
                 style="width: 100px;">
        </div>

        <!-- Contenedor principal -->
        <div style="padding: 20px;">

            <!-- Barra negra detrás del recibo -->
            <div style="
            background-color: #000;
            height: 30px;
            border-radius: 91px;
            position: relative;
            z-index: 0;
            max-width: 121%;
            margin: 0px auto -35px auto;
            width: 55%;
            " class="responsive-bar">
            </div>

            <!-- Contenedor del recibo -->
            <div style="
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            padding: 20px;
            position: relative;
            z-index: 1;
            max-width: 90%;
            margin: 20px auto;
            width: 50%;
        ">

                <!-- Encabezado -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="https://multimedia.epayco.co/plugins-sdks/warning.png" alt="Pendiente"
                         style="display: block; margin: auto; width: 70px; margin-bottom: 14px;">
                    <h2 style="color: #FFD100; font-size: 22px;">Transacción Pendiente</h2>
                    <h4 style="color: #000; font-size: 17px; font-weight: bold;">Referencia ePayco #{$refPayco}</h4>
                    <p style="color: #000; font-size: 14px;">{$fecha}</p>
                </div>

                <!-- Información de la transacción -->
                <div style="display: flex; flex-wrap: wrap; font-size: 14px; color: #333; line-height: 1.6;">
                    <div style="width: 50%; padding: 5px 50px;">
                        <h5 style="font-weight: bold;">Medio de pago</h5>
                        <span style="color: #8b8383;">Método de pago</span><br>
                        <span>{$franquicia}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top:21px;">
                        <span style="color: #8b8383;">Autorización</span><br>
                        <span>{$autorizacion}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 3%;">
                        <span style="color: #8b8383;">Recibo</span><br>
                        <span>{$refPayco}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 13px;">
                        <span style="color: #8b8383;">Dirección IP</span><br>
                        <span>{$ip}</span>
                    </div>
                    <div style="width: 50%; padding: 5px 50px; margin-top:15px">
                        <span style="color: #8b8383;">Respuesta</span><br>
                        <span>{$respuesta}</span>
                    </div>
                </div>

                <div style="display: flex; flex-wrap: wrap; font-size: 14px; color: #333; line-height: 1.6;">
                    <div style="width: 54%;padding: 5px 50px;">
                        <h5 style="font-weight: bold;margin-bottom: 20px;margin-top: 7%;">Datos de la compra</h5>
                        <span style="color: #8b8383;">Referencia Comercio</span><br>
                        <span>{$refPayco}</span>
                    </div>
                    <div class="responsive-field" style="width: 49%;padding: 5px 64px;margin-top: 10%;margin-left: -8%;">
                        <span style="color: #8b8383;">Descripción</span><br>
                        <span>{$descripcion}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 3%;">
                        <span style="color: #8b8383;">Descuento</span><br>
                        {if $descuento == 0}
                            <span>$ 0 COP</span>
                        {else}
                            <span>$ {$descuento|number_format:2:',':'.'} COP</span>
                        {/if}
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 14px;margin-left: -1%;">
                        <span style="color: #8b8383;">Subtotal</span><br>
                        <span>$ {($valor - $descuento)|number_format:2:',':'.'} COP</span>
                    </div>
                    <div style="width: 50%; padding: 5px 50px;margin-top: 20px;">
                        <span style="color: #8b8383;">Valor total</span><br>
                        <span>$ {$valor|number_format:2:',':'.'} COP</span>
                    </div>

                </div>
                <!-- Botón para descargar el recibo -->

            </div>

        </div>
    </div>

    <p style="text-align: center;">
        <a href="{$baseurl}" target="_blank" class="btn btn-primary" style="
                       color: #fff;
                       background-color: #212121;
                       border-color: transparent;
                       max-width: 100%; /* Hace que el botón ocupe el 100% del contenedor en pantallas pequeñas */
                       width: 300px; /* Ajusta el tamaño del botón en pantallas grandes */
                       padding: 10px 20px;
                       display: inline-block;
                       margin: 0 auto; /* Centra el botón en su contenedor */
                       text-align: center;
                   ">
            Descargar Recibo ePayco
        </a>
    </p>


    <!-- Estilos para dispositivos móviles -->
    <style>
        @media (max-width: 768px) {
            div[style*="width: 50%;"] {
                width: 100% !important;
            }

            div[style*="max-width: 1092px;"] {
                padding: 10px !important;
            }

            .responsive-bar {
                width: 100% !important;
            }
        }
    </style>

    {elseif $estado == 'Rechazada'}
    <div style="max-width: 1092px; margin: auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

        <!-- Barra superior negra con el logo -->
        <div style="background-color: #000; text-align: center; padding: 10px;">
            <img src="https://multimedia.epayco.co/plugins-sdks/logo-gris-1.png" alt="ePayco Logo"
                 style="width: 100px;">
        </div>

        <!-- Contenedor principal -->
        <div style="padding: 20px;">

            <div style="
            background-color: #000;
            height: 30px;
            border-radius: 91px;
            position: relative;
            z-index: 0;
            max-width: 121%;
            margin: 0px auto -35px auto;
            width: 55%;
            " class="responsive-bar">
            </div>

            <!-- Contenedor del recibo -->
            <div style="
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            padding: 20px;
            position: relative;
            z-index: 1;
            max-width: 90%;
            margin: 20px auto;
            width: 50%;
        ">

                <!-- Encabezado -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="https://multimedia.epayco.co/plugins-sdks/error.png" alt="Rechazada"
                         style="display: block; margin: auto; width: 70px; margin-bottom: 14px;">
                    <h2 style="color: #E1251B; font-size: 22px;">Transacción Rechazada</h2>
                    <h4 style="color: #000; font-size: 17px; font-weight: bold;">Referencia ePayco #{$refPayco}</h4>
                    <p style="color: #000; font-size: 14px;">{$fecha}</p>
                </div>

                <!-- Información de la transacción -->
                <div style="display: flex; flex-wrap: wrap; font-size: 14px; color: #333; line-height: 1.6;">
                    <div style="width: 50%; padding: 5px 50px;">
                        <h5 style="font-weight: bold;">Medio de pago</h5>
                        <span style="color: #8b8383;">Método de pago</span><br>
                        <span>{$franquicia}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top:21px;">
                        <span style="color: #8b8383;">Autorización</span><br>
                        <span>{$autorizacion}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 3%;">
                        <span style="color: #8b8383;">Recibo</span><br>
                        <span>{$refPayco}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 13px;">
                        <span style="color: #8b8383;">Dirección IP</span><br>
                        <span>{$ip}</span>
                    </div>
                    <div style="width: 50%; padding: 5px 50px; margin-top:15px">
                        <span style="color: #8b8383;">Respuesta</span><br>
                        <span>{$respuesta}</span>
                    </div>
                </div>

                <div style="display: flex; flex-wrap: wrap; font-size: 14px; color: #333; line-height: 1.6;">
                    <div style="width: 54%;padding: 5px 50px;">
                        <h5 style="font-weight: bold;margin-bottom: 20px;margin-top: 7%;">Datos de la compra</h5>
                        <span style="color: #8b8383;">Referencia Comercio</span><br>
                        <span>{$refPayco}</span>
                    </div>
                    <div class="responsive-field" style="width: 49%;padding: 5px 64px;margin-top: 10%;margin-left: -8%;">
                        <span style="color: #8b8383;">Descripción</span><br>
                        <span>{$descripcion}</span>
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 3%;">
                        <span style="color: #8b8383;">Descuento</span><br>
                        {if $descuento == 0}
                            <span>$ 0 COP</span>
                        {else}
                            <span>$ {$descuento|number_format:2:',':'.'} COP</span>
                        {/if}
                    </div>
                    <div style="width: 50%;padding: 5px 50px;margin-top: 14px;margin-left: -1%;">
                        <span style="color: #8b8383;">Subtotal</span><br>
                        <span>$ {($valor - $descuento)|number_format:2:',':'.'} COP</span>
                    </div>
                    <div style="width: 50%; padding: 5px 50px;margin-top: 20px;">
                        <span style="color: #8b8383;">Valor total</span><br>
                        <span>$ {$valor|number_format:2:',':'.'} COP</span>
                    </div>

                </div>
                <!-- Botón para descargar el recibo -->

            </div>
        </div>
    </div>

    <p style="text-align: center;">
        <a href="{$baseurl}" target="_blank" class="btn btn-primary" style="
                       color: #fff;
                       background-color: #212121;
                       border-color: transparent;
                       max-width: 100%; /* Hace que el botón ocupe el 100% del contenedor en pantallas pequeñas */
                       width: 300px; /* Ajusta el tamaño del botón en pantallas grandes */
                       padding: 10px 20px;
                       display: inline-block;
                       margin: 0 auto; /* Centra el botón en su contenedor */
                       text-align: center;
                   ">
            Descargar Recibo ePayco
        </a>
    </p>


    <!-- Estilos para dispositivos móviles -->
    <style>
        @media (max-width: 768px) {
            div[style*="width: 50%;"] {
                width: 100% !important;
            }

            div[style*="max-width: 1092px;"] {
                padding: 10px !important;
            }

            .responsive-bar {
                width: 100% !important;
            }
        }
    </style>

    {else}
    <div
            style="max-width: 600px; margin: auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: center; color: #d9534f; background-color: #fef2f2; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="font-size: 20px; font-weight: bold;">Su pago se encuentra en proceso de validacion. </h2>
        <p style="font-size: 14px;">Por favor, inténtelo mas tarde.</p>
    </div>
{/if}