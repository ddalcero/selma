
{{ Table::striped_condensed_open() }}
{{ Table::headers('Haberes', '', 'Descuentos', '') }}
<tbody>
<tr>
	<td>Salario base</td>
	<td>{{ ViewFormat::NFL($salario->base) }}</td>
	<td>AFP ({{ $salario->afp }})</td>
	<td>{{ ViewFormat::NFL($salario->afp_total) }}</td>
</tr>
<tr>
	<td>Gratificación legal</td>
	<td>{{ ViewFormat::NFL($salario->gratificacion) }}</td>
	<td>Salud</td>
	<td>{{ ViewFormat::NFL($salario->salud) }}</td>
</tr>
<tr>
	<td>Otros no imponibles</td>
	<td>{{ ViewFormat::NFL($salario->no_imponibles) }}</td>
	<td>Cesantía</td>
	<td>{{ ViewFormat::NFL($salario->cesantia) }}</td>
</tr>
<tr>
	<td>Total imponible</td>
	<td>{{ ViewFormat::NFL($salario->imponible) }}</td>
	<td>Impuesto único</td>
	<td>{{ ViewFormat::NFL($salario->impuesto_unico) }}</td>
</tr>
</tbody>
<tfoot>
<tr>
	<th>Total haberes</th>
	<th>{{ ViewFormat::NFL($salario->total_haberes) }}</th>
	<th>Total descuentos</th>
	<th>{{ ViewFormat::NFL($salario->total_descuentos) }}</th>
</tr>
</tfoot>
{{ Table::close() }}

<div class="row-fluid">
	<div class="span12">
		<p class="lead">Beneficios</p>
		<dl class="dl-horizontal">
			<dt>Salud</dt>
			<dd>Seguro complementario de salud</dd>
			<dt>Aguinaldos</dt>
			<dd>Aguinaldo de Fiestas Patrias y Navidad, proporcional a la fecha de ingreso</dd>
			@if ($salario->tickets_dia > 0)
			<dt>Colación</dt>
			<dd>Tickets restaurant, $ {{ ViewFormat::NFL($salario->tickets_dia) }} por día trabajado</dd>
			@endif
		</dl>
	</div>
	<div class="span12">
		<p class="lead">Coste empresa</p>
			<dl class="dl-horizontal">
				<dt>Coste total</dt>
				<dd>$ {{ ViewFormat::NFL($salario->coste_empresa) }}</dd>
			</dl>
	</div>
</div>
<!--
<?php print_r($salario) ?>
-->
