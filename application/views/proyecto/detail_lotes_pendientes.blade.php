		@if (count($lotes)>0)
        @if ($currentClient="-") @endif
        @if ($currentProject="-") @endif
		<div class="row-fluid">
			<div class="span12">
				<table class="table-striped table-hover table-condensed table">
					<thead>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
						<th>#</th>
						<th>Nombre lote</th>
						<th>Fecha fact.</th>
						<th>Importe $</th>
						<th>Importe UF</th>
						<th>Status</th>
					</thead>
					<tbody>
						@foreach ($lotes as $lote)
                            @if ($currentClient<>$lote['clt_nom'])
                                <tr class="lotes"><td colspan="8"><strong>{{ $lote['clt_nom'] }}</strong></td></tr>
                                @if ($currentClient=$lote['clt_nom']) @endif
                            @endif
                            @if ($currentProject<>$lote['spj_libelle'])
                                <tr class="lotes">
                                    <td>&nbsp;</td>
                                    <td colspan="7">
                                        <a href="/facturar/{{ $lote['spj_id'] }}">{{ $lote['spj_libelle'] }}</a></td>
                                </tr>
                                @if ($currentProject=$lote['spj_libelle']) @endif
                            @endif
                            <tr class="lotes">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
							<td class="column-lot_id">{{ $lote['lot_index'] }}</td>
							<td class="column-lot_libelle">{{ $lote['lot_libelle'] }}</td>
							<td class="column-lot_fecha">{{ ViewFormat::dateFromDB($lote['lot_fecha']) }}</td>
							<td class="column-lot_montant_euro">{{ ViewFormat::NFL($lote['lot_montant_euro']) }}</td>
							<td class="column-lot_montant_uf">{{ ViewFormat::NFL($lote['lot_montant_uf'],2) }}</td>
							<td class="columnd-lot_status">
								@if ($lote['fsi_id']>0)
									{{ Label::success('Emitida') }}
									{{ Label::normal(ViewFormat::NFL($lote['valor_uf'],2))}}
								@else
									@if ($lote['solicitud']<>0)
										{{ Button::mini_info_link('/solicitud/'.$lote['solicitud'],'En curso #'.$lote['solicitud'])}}
									@else
										{{ Label::warning('Pendiente')}}
										{{ Button::mini_warning_link('#lote_modal'.$lote['lot_id'],'',array('data-toggle'=>'modal'))->with_icon('pencil') }}

										@if (Sentry::user()->has_access('mod_realizado'))
											{{ Button::mini_danger_link('/lote/'.$lote['lot_id'],'',array('data-method'=>'delete'))->with_icon('trash-o') }}
										@endif
										{{ Button::mini_link('/lote/mail/'.$lote['lot_id'],'Solicitar')->with_icon('certificate') }}
									@endif
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5" class="rightCell">TOTAL</td>
							<td class="rightCell"><strong>{{ ViewFormat::NFL($total['total_clp'],0) }}</strong></td>
							<td class="rightCell"><strong>{{ ViewFormat::NFL($total['total_uf'],2) }}</strong></td>
							<td class="rightCell"> </td>
						</tr>
					</tfoot>
				</table>
                @if (isset($spj_id))
                    @if (Sentry::user()->has_access('mod_realizado'))
                    {{ Button::small_danger_link('/lote/new/'.$spj_id ,'Añadir Lote')->with_icon('plus'); }}
                    {{ Button::small_danger_link('#lote_ajuste_modal','Añadir Lotes de Ajuste',array('data-toggle'=>'modal'))->with_icon('plus'); }}
                    @endif
                @endif
                <hr/>
			</div>
		</div>
		@foreach ($lotes as $lote)
		@include('plugins/lote_modal')
		@endforeach
		@else
			<p class="text-warning">No se han encontrado lotes de facturación en este periodo</p>
		@endif
