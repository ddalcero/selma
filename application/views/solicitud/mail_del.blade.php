<table class="table-striped table-condensed table">
	<tr>
		<td><h2>Solicitud eliminada</h2></td>
		<td>{{ $solicitud->id }} ({{ HTML::link('mailto:'.$email,$username) }})</td>
	</tr>
	<tr>
		<td>Fecha Solicitud</td>
		<td>{{ ViewFormat::DateTimeFromDB($solicitud->fecha_sol) }}</td>
	</tr>
	<tr>
		<td>Cliente</td>
		<td><b>{{ $solicitud->cliente }}</b> ({{ $solicitud->clt_id }})</td>
	</tr>
	<tr>
		<td>Importe</td>
		<td><b>{{ ViewFormat::NFL($solicitud->importe_clp) }}</b> CLP ({{ ViewFormat::NFL($solicitud->total_uf,2) }} UF)
		</td>
	</tr>
	@if ($solicitud->tasa_iva > 0)
	<tr>
		<td>IVA</td>
		<td>{{ ViewFormat::NFL($solicitud->tasa_iva,2) }}%</td>
	</tr>
	<tr>
		<td>TOTAL</td>
		<td><b>{{ ViewFormat::NFL($solicitud->total) }}</b></td>
	</tr>
	@endif
</table>