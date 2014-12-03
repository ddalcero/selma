		@if (count($lotes)>0)
		<div class="row-fluid">
			<div class="span12">
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
								{{ Button::mini_warning_link('#lote_modal'.$lote['lot_id'],'',array('data-toggle'=>'modal'))->with_icon('pencil') }}
								@if (Sentry::user()->has_access('mod_realizado'))
								{{ Button::mini_danger_link('/lote/'.$lote['lot_id'],'',array('data-method'=>'delete'))->with_icon('trash-o') }}
								@endif
								{{ Button::mini_link('/lote/mail/'.$lote['lot_id'],'Solicitar')->with_icon('certificate') }}
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
					@if (Sentry::user()->has_access('mod_realizado'))
					{{ Button::small_danger_link('/lote/new/'.$spj_id ,'Añadir Lote')->with_icon('plus'); }}
					{{ Button::small_danger_link('#lote_ajuste_modal','Añadir Lotes de Ajuste',array('data-toggle'=>'modal'))->with_icon('plus'); }}
					@endif
					<hr/>
			</div>
		</div>
		@foreach ($lotes as $lote)
		@include('plugins/lote_modal')
		@endforeach
		@include('plugins/lote_ajuste_modal')
		@else
			<p class="text-warning">No se han encontrado lotes de facturación en este periodo</p>
		@endif
