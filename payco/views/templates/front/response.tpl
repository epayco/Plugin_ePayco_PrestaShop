{*
    * 2007-2012 PrestaShop
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
    *  @author ePayco SAS <desarrollo@epayco.co>
    *  @copyright  2011-2017 ePayco SAS
    *  @version  Release: $Revision: 100 $
    *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
    *  International Registered Trademark & Property of PrestaShop SA
    *}

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Formulario Respuesta ePayco</title>
        <!-- Bootstrap -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script
            src="https://eks-cms-backend-platforms-service.epayco.io/plugin/DetailPurchase.js"
            defer>
        </script>

        </head>

        <body>
        <div id="epayco-cms-detail-purchase"></div>
            
            <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js"></script>
            <!-- Include all compiled plugins (below), or include individual files as needed -->
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            <script>
                function getQueryParam(param) {
                    location.search.substr(1)
                    .split("&")
                .some(function(item) { // returns first occurence and stops
                    return item.split("=")[0] == param && (param = item.split("=")[1])
                })
                return param
            }
            $(document).ready(function() {
                //llave publica del comercio
                //Referencia de payco que viene por url
                var ref_payco = getQueryParam('ref_payco');
                if(ref_payco == "ref_payco"){
                    let count = window.location.search.search('ref_payco') + 10;
                    ref_payco = window.location.search.slice( count );
                }
                //Url Rest Metodo get, se pasa la llave y la ref_payco como paremetro
                var urlapp = "https://eks-checkout-service.epayco.io/validation/v1/reference/"+ref_payco;
                
                $.get(urlapp, function(response,error) {
                    if (response.success) {
                        if (window.DetailPurchase) {
                            DetailPurchase.config({
                                referencePayco: response.data.x_ref_payco,  // ID único de la transacción
                                sendEmail: true,         // Enviar comprobante por correo
                                lang: response.data.x_extra3,   // Idioma del componente
                                returnUrl: window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/')) + "/index.php?controller=history"
                                buttonText:"Mis Compras",
                                returnButton: true // Muestra botón de retorno a la tienda
                            });
                        } else {
                            alert("En este momento no podemos mostrarte el detalle de la orden, por favor contactar con el administrador")
                            console.error("DetailPurchase no está cargado todavía");
                        }
                        if (response.data.x_cod_response == 1) {
                            console.log('transacción Aceptada');
                        }
                        //Transaccion Rechazada
                        if (response.data.x_cod_response == 2) {
                            console.log('transacción rechazada');
                        }
                        //Transaccion Pendiente
                        if (response.data.x_cod_response == 3) {
                            console.log('transacción pendiente');
                        }
                        //Transaccion Fallida
                        if (response.data.x_cod_response == 4) {
                            console.log('transacción fallida');
                        }
                    } else {
                        alert("Error consultando la información");
                    }
                });
            });
        </script>
    </body>

</html>
