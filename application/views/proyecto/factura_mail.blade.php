
<body>
<h3>Petición de emisión de factura</h3>
<hr/>

Solicitud de factura enviada por <i>{{ $username }}</i>

<blockquote>

	<table>
	<tr>
		<td class="camp">Cliente</td>
		<td class="desc">{{ $proyecto[0]['clt_nom'] }}</td>
	</tr>
	<tr>
		<td class="camp">Proyecto</td>
		<td class="desc">{{ $proyecto[0]['spj_libelle'] }}</td>
	</tr>
	<tr>
		<td class="camp">Glosa factura</td>
		<td class="desc">{{ $lote['lot_libelle'] }}</td>
	</tr>
	<tr>
		<td class="camp">Fecha facturación</td>
		<td class="desc">{{ ViewFormat::dateFromDB($lote['lot_fecha']) }}</td>
	</tr>
	<tr>
		<td class="camp">Importe CLP</td>
		<td class="desc">{{ ViewFormat::NFL($lote['lot_montant_euro']) }}</td>
	</tr>
	<tr>
		<td class="camp">Valor UF</td>
		<td class="desc">{{ ViewFormat::NFL($lote['valor_uf'],2) }}</td>
	</tr>
	<tr>
		<td class="camp">Importe UF</td>
		<td class="desc">{{ ViewFormat::NFL($lote['lot_montant_uf'],2) }}</td>
	</tr>
	</table>

</blockquote>

</body>
