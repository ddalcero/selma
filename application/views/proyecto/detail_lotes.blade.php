		@if (count($lotes)>0)
		<div class="row-fluid">
			<div class="span10">
				<table class="table-striped table-hover table-condensed table">
					<thead>
						<th>#</th>
						<th>Nombre lote</th>
						<th>Fecha fact.</th>
						<th>Importe $</th>
						<th>Importe UF</th>
						<th>Status</th>
					</thead>
					<tbody>
						@foreach ($lotes as $lote)
						<tr class="lotes">
							<td class="column-lot_id">{{ $lote['lot_id'] }}</td>
							<td class="column-lot_libelle">{{ $lote['lot_libelle'] }}</td>
							<td class="column-lot_fecha">{{ ViewFormat::dateFromDB($lote['lot_fecha']) }}</td>
							<td class="column-lot_montant_euro">{{ ViewFormat::NFL($lote['lot_montant_euro']) }}</td>
							<td class="column-lot_montant_uf">{{ ViewFormat::NFL($lote['lot_montant_uf'],2) }}</td>
							<td class="columnd-lot_status">
								@if ($lote['fsi_id']>0)
								{{ Label::success('Facturado') }}
								{{ Label::normal(ViewFormat::NFL($lote['valor_uf'],2))}}

								@else
								{{ Label::warning('Pendiente')}}
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3" class="rightCell">TOTAL</td>
							<td class="rightCell"><strong>{{ ViewFormat::NFL($total['total_clp'],0) }}</strong></td>
							<td class="rightCell"><strong>{{ ViewFormat::NFL($total['total_uf'],2) }}</strong></td>
							<td class="rightCell"> </td>
						</tr>
					</tfoot>
					</table>
			</div>
		</div>
		@else
			<p class="text-warning">No se han encontrado lotes de facturación en este periodo</p>
		@endif
