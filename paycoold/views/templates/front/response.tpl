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
                  </head>

                  <body>
                    <div class="container">
                        <div class="row" style="margin-top:20px">
                            <div class="col-lg-8 col-lg-offset-2 ">
                                <h4 style="text-align:left"> Respuesta de la Transacción </h4>
                                <hr>
                            </div>
                            <div class="col-lg-8 col-lg-offset-2 ">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td>Referencia</td>
                                                <td id="referencia"></td>
                                            </tr>
                                            <tr>
                                                <td class="bold">Fecha</td>
                                                <td id="fecha" class=""></td>
                                            </tr>
                                            <tr>
                                                <td>Respuesta</td>
                                                <td id="respuesta"></td>
                                            </tr>
                                            <tr>
                                                <td>Motivo</td>
                                                <td id="motivo"></td>
                                            </tr>
                                            <tr>
                                                <td class="bold">Banco</td>
                                                <td class="" id="banco">
                                                </tr>
                                                <tr>
                                                    <td class="bold">Recibo</td>
                                                    <td id="recibo"></td>
                                                </tr>
                                                <tr>
                                                    <td class="bold">Total</td>
                                                    <td class="" id="total">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                 <div class="col-lg-8 col-lg-offset-2 ">
                                    <a class="btn btn-primary" href="{$base_url}">Inicio</a>
                                    <a class="btn btn-success" href="index.php?controller=history">Mis Compras</a>
                                 </div>
                            </div>
                        </div>
                        <footer>
                            <div class="row">
                                <div class="container">
                                    <div class="col-lg-8 col-lg-offset-2">
                                        <img src="https://369969691f476073508a-60bf0867add971908d4f26a64519c2aa.ssl.cf5.rackcdn.com/btns/epayco/pagos_procesados_por_epayco_260px.png" style="margin-top:10px; float:left"> <img src="https://369969691f476073508a-60bf0867add971908d4f26a64519c2aa.ssl.cf5.rackcdn.com/btns/epayco/credibancologo.png" height="40px" style="margin-top:10px; float:right">
                                    </div>
                                </div>
                            </div>
                        </footer>
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
                    var ref_payco = "{$ref_payco|escape:'htmlall':'UTF-8'}";
                    //Url Rest Metodo get, se pasa la llave y la ref_payco como paremetro
                    var urlapp = "{$url|unescape: 'html' nofilter}";
                    
                    $.get(urlapp, function(response) {


                        if (response.success) {

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

                            $('#fecha').html(response.data.x_transaction_date);
                            $('#respuesta').html(response.data.x_response);
                            $('#referencia').text(response.data.x_id_invoice);
                            $('#motivo').text(response.data.x_response_reason_text);
                            $('#recibo').text(response.data.x_transaction_id);
                            $('#banco').text(response.data.x_bank_name);
                            $('#autorizacion').text(response.data.x_approval_code);
                            $('#total').text(response.data.x_amount + ' ' + response.data.x_currency_code);


                        } else {
                            alert("Error consultando la información");
                        }
                    });

});
</script>
</body>
</html>


