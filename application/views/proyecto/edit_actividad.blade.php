@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
	<div class="span9">
		<br/>

		<h3>Actividad</h3>

		<p class="lead">Resumen de la actividad mensual</p>

		@if (count($actividad)>0)

		<dl class="dl-horizontal">
			<dt>Periodo</dt>
			<dd>{{ Session::get('sPeriodo') }}</dd>
			<dt>Valor UF</dt>
			<dd>{{ ViewFormat::NFL($valores->uf,2) }}</dt>
			<dt>Días periodo<br/></dt>
			<dd>{{ $valores->pdays }}<br/></dt>
			<dt>Proyecto</dt>
			<dd>{{ $actividad[0]['spj_libelle'] }}</dt>
		</dl>

		<hr/>

		<div class="row-fluid">
			<div class="span9">
				<table class="table table-condensed table-hover" id="tablaActividad">
					<thead>
						<th>#</th>
						<th>Consultor</th>
						<th>Tarifa&nbsp;mes</th>
						<th>Imputado</th>
						<th>Facturable</th>
						<th>Tarifa</th>
						<th>Realizado&nbsp;$</th>
					</thead>
					<tbody>
						@foreach ($actividad as $linea)
						<tr class="datos">
							<td class="column-act_id">{{ $linea['act_id'] }}</td>
							<td class="column-consultor">{{ $linea['consultor'] }}</td>
							<td class="column-tarifa_uf">{{ Form::mini_text($linea['per-spj'],ViewFormat::NFL($linea['tarifa_uf'],2))  }}</td>
							<td class="column-imputado">{{ ViewFormat::NFL($linea['imputado']) }}</td>
							<td class="column-facturable">{{ ViewFormat::NFL($linea['facturable'],2) }}</td>
							<td class="column-tarifa">{{ ViewFormat::NFL($linea['tarifa'],2) }}</td>
							<td class="column-realizado">{{ ViewFormat::NFL($linea['realizado']) }}</td>
						</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" class="rightCell">TOTAL</td>
							<td class="column-total">{{ ViewFormat::NFL($total['realizado']) }}</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<div class="span3" id="sticker">
			</div>
		</div>

		@if (count($lotes)>0)
		<div class="row-fluid">
			<div class="span9">
				<table class="table-striped table-hover table-condensed table">
					<thead>
						<th>#</th>
						<th>Nombre lote</th>
						<th>Fecha fact.</th>
						<th>Importe $</th>
						<th>Status</th>
					</thead>
					<tbody>
						@foreach ($lotes as $lote)
						<tr class="lotes">
							<td class="column-lot_id">{{ $lote['lot_id'] }}</td>
							<td class="column-lot_libelle">{{ $lote['lot_libelle'] }}</td>
							<td class="column-lot_date_previ_fac">{{ ViewFormat::dateFromDB($lote['lot_date_previ_fac']) }}</td>
							<td class="column-lot_montant_euro">{{ ViewFormat::NFL($lote['lot_montant_euro']) }}</td>
							<td class="columnd-lot_status">
								@if ($lote['fsi_id']>0)
								<span class="label label-success">Facturado</span>
								@else
								<span class="label label-warning">Pendiente</span>
								<a href="#lote_modal{{ $lote['lot_id'] }}" data-toggle="modal">Modificar</a>
								@endif
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		@foreach ($lotes as $lote)
		@include('plugins/lote_modal')
		@endforeach

		@if (Sentry::user()->has_access('validar_proyecto'))
		<div class="row-fluid">
			<label class="checkbox">Actividad validada <input type="checkbox" id="validado" {{($checked)?'checked="checked"':''}}> </label>
		</div>
		@endif
		<div class="row-fluid">
			<div class="span4">
				@if ($facturado>0)
					<p class="text-warning">Lote facturado. Modificación no permitida.</p>
				@else
				<button id="bRecalcular" class="btn btn-info" type="button"><i class="icon-list-alt icon-white"></i> Recalcular</button>
				<button id="bGrabarOlga" class="btn botonOlga btn-danger" type="button"><i class="icon-thumbs-up icon-white"></i> Actualizar Olga </button>
				@endif
			</div>
			<div id="resultadoOlga" class="span4">
			</div>
		</div>
		@else
		<div class="row-fluid">
			<div class="span9">
				<p>
					<button id="bAddLote" class="btn btn-success" type="button"><i class="icon-plus-sign icon-white"></i> Añadir lote</button>
				</p>
			</div>
		</div>
		@endif

		@else
			<p class="text-warning">No se ha encontrado actividad para este proyecto en este periodo</p>
		@endif

		<br/>

		<p>
			{{ HTML::link($view_link,'Visualizar',array('class'=>'btn')) }}  
			{{ HTML::link('main/realizado','Volver a proyectos',array('class'=>'btn btn-primary')) }}
		</p>
		
	</div>
</div>
@endsection

@section('js')
@parent
{{-- <script> --}}
$(document).ready(function() {
	$('#sticker').load('/sticker/{{$spj_id}}');
	// actualizar tarifas
	$('input.input-mini[type=text]').change(function() {
		var persub,arr,uf,url;
		persub=$(this).attr('name');
		uf=$(this).val().replace(/\./g,'').replace(',','.');
		arr={'uf':uf};
		url="/persub/"+persub;
		$.ajax({
			type: 'POST',
			url: url,
			data: arr,
			dataType: "html"
		});		
		$(this).val(_NFL(uf,2));
	});
	// Añadir lote
	$('#bAddLote').click(function() {
		window.location.href = "/actividad/{{ Session::get('sPeriodo') }}/{{ $spj_id }}/lote";
	});

	// recalcular
	$('#bRecalcular').click(function() {
		var imp,fac,tmes,actid,costomes,realizado,diasfact,tarifadia;
		var total=0;
		var uf={{ $valores->uf }};
		var dias={{ $valores->pdays }};
		$('table tr.datos').each(function(){
			tmes=$(':text',this).val().replace(/\./g,'').replace(',','.');
			imp =$('td.column-imputado',this).text().replace(/\./g,'').replace(',','.');
			// calculos
			costomes=Math.round((imp/dias*tmes)*100)/100;
			realizado=Math.round(costomes*uf);
			diasfact=Math.round((imp/dias*18.5)*100)/100;
			tarifadia=Math.round((realizado/diasfact)*100)/100;
			total+=realizado;
			$('td.column-facturable',this).html(_NFL(diasfact,2));
			$('td.column-realizado',this).html(_NFL(realizado,0));
			$('td.column-tarifa',this).html(_NFL(tarifadia,2));
		});
		$('table tfoot tr td.column-total').html(_NFL(total));
		$('table tr.lotes').each(function(){
			$('td.column-lot_montant_euro',this).html(_NFL(total));
		});
		$(".botonOlga").show();
	});
	// grabar a OLGA
	$("#bGrabarOlga").click(function() {
		var actid,diasfact,tarifadia,i=0,lot_id,total_lote,resultado="";
		var arr = {},lote = {};
		var uf={{ $valores->uf }};
		var dias={{ $valores->pdays }};

		$('table tr.datos').each(function(){
			actid=$('td.column-act_id',this).text();
			diasfact=$('td.column-facturable',this).text().replace(/\./g,'').replace(',','.');
			tarifadia=$('td.column-tarifa',this).text().replace(/\./g,'').replace(',','.');
			arr[i]={
				'index' : i,
				'act_id' : actid,
				'dias_fact' : diasfact,
				'tarifa_dia' : tarifadia
			};
			i++;
		});
		$.ajax({
			type: 'POST',
			url: "/actividad/update",
			data: arr,
			dataType: "html",
			success: function(data) {
				// alert(data); 
				$('#resultadoOlga').append(data); 
			}
		});
		i=0;
		$('table tr.lotes').each(function(){
			lot_id=$('td.column-lot_id',this).text().replace(/\./g,'').replace(',','.');
			total=$('td.column-lot_montant_euro',this).text().replace(/\./g,'').replace(',','.');
			lote[i]={
				'index' : i,
				'lot_id' : lot_id,
				'total' : total
			};
			// alert('tengo un lote: '+JSON.stringify(lote[i]))
			i++;
		});
		if (i>0) {
			$.ajax({
				type: 'POST',
				url: "/actividad/lote/update",
				data: lote,
				dataType: "html",
				success: function(data) {
					$('#resultadoOlga').append(data); 
				}
			});
		}
	});
    @if (Sentry::user()->has_access('validar_proyecto'))
	$('#validado').change(function() {
		// dataString = $("form[id='toggleCheck']").serialize();
		var chkd=($('#validado').prop('checked'))?1:0;
		var url="{{ $check_link }}/"+chkd;
		$.ajax({
			type: "GET",
			url: url,
			dataType: "html",
		});
	});
	@endif
});

function _NFL (number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
	s = '',
	toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec);
		return '' + Math.round(n * k) / k;
	};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}

{{-- </script> --}}
@endsection

@section('css')
@parent
{{-- <style> --}}
.table th.rightCell,
.table td.rightCell,
.table td.column-tarifa_uf,
.table td.column-imputado,
.table td.column-facturable,
.table td.column-tarifa,
.table td.column-realizado {
  text-align: right;
}
.table td.column-total,
.table td.column-lot_montant_euro {
  text-align: right;
  font-weight: bold;
}
.botonOlga {
	display: none;
}

{{-- </style> --}}
@endsection
