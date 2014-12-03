
<body>
<h3>Petición de emisión de factura {{ $tipo_factura }} {{ $link }}</h3>
<hr/>

Solicitud de factura {{ $tipo_factura }} enviada por <i>{{ $username }}</i>

<blockquote>

<table>
	<tr>
		<td class="camp" width="140">Cliente</td>
		<td class="desc">{{ $proyecto[0]['clt_nom'] }}</td>
	</tr>
	<tr>
		<td class="camp" width="140">Fecha facturación</td>
		<td class="desc">{{ ViewFormat::dateFromDB($lote['lot_fecha']) }}</td>
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
		<td class="camp" valign="top">Detalle</td>
		<td class="desc" valign="top">{{ nl2br($lote['lot_libelle_fac_clt']) }}<br/><hr/></td>
	</tr>
	</table>
	<table>
	<tr>
		<td class="camp" width="140"><b>Importe CLP</b></td>
		<td class="desc" align="right"><b>{{ ViewFormat::NFL($lote['lot_montant_euro']) }}</b></td>
	</tr>
	@if ($lote['lot_tva'])
	<tr>
		<td class="camp">IVA {{ ViewFormat::NFL($lote['lot_taux_tva'],2) }}%</td>
		<td class="desc" align="right">{{ ViewFormat::NFL($lote['lot_montant_euro']*$lote['lot_taux_tva']/100) }}</td>
	</tr>
	<tr>
		<td class="camp"><b>TOTAL CLP</b></td>
		<td class="desc" align="right"><b>{{ ViewFormat::NFL($lote['lot_montant_euro']*(1+($lote['lot_taux_tva']/100))) }}</b></td>
	</tr>
	@endif
	<tr>
		<td class="camp">Importe UF</td>
		<td class="desc">{{ ViewFormat::NFL($lote['lot_montant_uf'],2) }} </td>
	</tr>
	<tr>
		<td class="camp">Valor UF</td>
		<td class="desc">{{ ViewFormat::NFL($lote['valor_uf'],2) }}</td>
	</tr>
	</table>
</table>

<p>Acceso al proyecto: {{ Request::server('SERVER_NAME') }} </p>
<p>Gestión de facturación: {{ $link }} </p>

</blockquote>

</body>
