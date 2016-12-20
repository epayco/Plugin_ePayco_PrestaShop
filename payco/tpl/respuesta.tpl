<div class="">
	<h1> Transaccion {$respuesta}</h1>

	<h3> Apreciado cliente, la transaccion con Referencia. {$refpayco}
		
		fue recibida por nuestro sistema.</h3>
		<h2>Datos de compra:</h3>
			<table border="0">
				<tbody>
					<tr>
						<td width="240"><strong> Código de Referencia: </strong>&nbsp;</td>
						<td width="240">
							{$ref_venta}
						</td>
					</tr>
					<tr>
						<td><strong> Valor: </strong></td>
						<td>{$valor}
						</td>
					</tr>
					<tr>
						<td><strong>Codigo de autorizacion: </strong></td>
						<td>{$codaprovacion}
						</td>
					</tr>
					<tr>
						<td><strong>No. Recibo: </strong></td>
						<td>{$numero_transaccion}
						</td>
					</tr>
					<tr>
						<td><strong> Moneda: </strong></td>
						<td>
							{$moneda}
						</td>
					</tr>
				</tbody>
			</table><h2>Datos de la transaccion:</h2>
			<table border="0">
				<tbody>
					<tr>
						<td width="240"><strong> Fecha de Procesamiento: </strong>&nbsp;</td>
						<td width="240">
							{$fecha}
						</td>
					</tr>
					<tr>
						<td><strong>Ref.payco: </strong></td>
						<td>
							{$refpayco}
						</td>
					</tr>
					<tr>
						<td><strong> Banco o Franquicia: </strong></td>
						<td>
							{$franquicia}
							<br /></td>
						</tr>
						<tr>
							<td><strong> Motivo: </strong></td>
							<td>
								{$mensaje}
							</td>
						</tr>
					</tbody>
				</table>
</div>
