<div class="col-xs-12 col-sm-12 col-md-12">
<div class="wrap">
<div class="box cheque-box">
	<h1 class="page-heading"> Transaccion {$respuesta}</h1>

	<h3 class="page-subheading" >
		<img src="/modules/payco/payco.jpg" alt="{l s='payco' mod='payco'}" width="86" height="90" style="float:left;" />
		<br>
			Apreciado cliente, la transaccion con Referencia. {$refpayco} fue recibida por nuestro sistema.
	</h3>
		<br>
		<br>
		<br>
<table border="0">
				<tbody>
					<tr>
						<td style="border: solid 1px;" colspan="2">
							<h3 style="color:black;">Datos de compra:</h3>
						</td>
					</tr>	
					<tr>
						<td style="border: solid 1px;"><strong> Codigo de Referencia: </strong>&nbsp;</td>
						<td width="240" style="border: solid 1px;">
							{$ref_venta}
						</td>
					</tr>
					<tr>
						<td style="border: solid 1px;"><strong> Valor: </strong></td>
						<td style="border: solid 1px;">{$valor}
						</td>
					</tr>
					<tr>
						<td style="border: solid 1px;"><strong>Codigo de autorizacion: </strong></td>
						<td style="border: solid 1px;">{$codaprovacion}
						</td>
					</tr>
					<tr>
						<td style="border: solid 1px;"><strong>No. Recibo: </strong></td>
						<td style="border: solid 1px;">{$numero_transaccion}
						</td>
					</tr>
					<tr>
						<td style="border: solid 1px;"><strong> Moneda: </strong></td>
						<td style="border: solid 1px;">
							{$moneda}
						</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table border="0">
				<tbody>
					<tr>
						<td colspan="2" style="border: solid 1px;">
						<h3 style="color:black;">Datos de la transaccion:</h3>
						</td>
					</tr>
					<tr>
						<td width="240" style="border: solid 1px;"><strong> Fecha de Procesamiento: </strong>&nbsp;</td>
						<td width="240" style="border: solid 1px;">
							{$fecha}
						</td>
					</tr>
					<tr>
						<td style="border: solid 1px;"><strong>Ref.payco: </strong></td>
						<td style="border: solid 1px;">
							{$refpayco}
						</td>
					</tr>
					<tr>
						<td style="border: solid 1px;"><strong> Banco o Franquicia: </strong></td>
						<td style="border: solid 1px;">
							{$franquicia}
							<br /></td>
						</tr>
						<tr>
							<td style="border: solid 1px;"><strong> Motivo: </strong></td>
							<td style="border: solid 1px;">
								{$mensaje}
							</td>
						</tr>
					</tbody>
				</table>
</div>
</div>
</div>