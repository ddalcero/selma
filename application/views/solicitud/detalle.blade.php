@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
		@include('main/left')
		<div class="span9">
				<br/>

				<div class="row-fluid">
						<div class="span12">
									<h3>Gestión de solicitudes de facturación</h3>
<h4>Solicitud de factura {{ $tipo_factura }}</h4>
<table class="table-striped table-condensed table">
	<tr>
		<td>Solicitud</td>
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
		<td>Lote</td>
		<td>{{ HTML::link_to_route('facturar_lote','#'.$solicitud->lot_id,array($solicitud->spj_id)) }} {{ $solicitud->glosa }}</td>
	</tr>
			<tr>
						<td>Glosa solicitud</td>
						<td>{{ nl2br($solicitud->detalle) }}</td>
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

{{ Form::horizontal_open('solicitud/'.$solicitud->id,'PUT',array('name'=>'form-factura-lote')) }}
{{ Form::control_group(Form::label('folio', 'Nr. de Folio'),Form::text('folio',null,array('class' => 'input-small', 'placeholder' => '# folio')), '') }}
{{ Form::control_group(Form::label('auxiliar', 'Auxiliar'),Form::span5_select('auxiliar',$auxiliares,$auxcli), '') }}

<input type="hidden" name="importe" id="importe" value="{{ ViewFormat::NFL($solicitud->importe_clp) }}">

{{ Form::actions(array(Button::primary_submit('Grabar y enviar notificación',array('class'=>'btn-info'))->with_icon('check'),Button::link('#','Cancelar',array('id'=>'bCancel'))->with_icon('times'))) }}

{{ Form::close() }}

							<div id="dtes">
@include('solicitud/dtes')                
							</div>

						</div>
				</div>
		</div>
</div>
@endsection

@section('js')
@parent
$(document).ready(function () {

	// Select Auxiliar
	$("#auxiliar").select2({
				placeholder: "Auxiliar"
	});

	// Actualiza el auxiliar con el cliente OLGA
	$("#auxiliar")
		.on("change", function(e) {
			$.post("/api/auxcli",{aux:e.added.id,clt_id:{{ $solicitud->clt_id }}});
			//$("#dtes").load("/solicitud/dtes/"+e.added.id);
			location.reload(true);
		});

	// Selecciona línea y pon nr. de folio en campo texto
	$('#tabla_dtes tr').click(function(event) {
		$("#folio").val(($('.column-e_numfact',this).html()));

		var imp_dte=($('.column-e_importe',this).html() + '').replace(/[^0-9+-Ee.]/g, '');
		var imp_sol=($("#importe").val() + '').replace(/[^0-9+-Ee.]/g, '');

		if (imp_dte != imp_sol) {
			alert("Los importes no coinciden. dte: "+imp_dte+" sol: "+imp_sol);
		}

	});

	// Botón cancel
	$("#bCancel").click(function(){
		window.location='/main/gestionfacturas';
	});

	// Grabar Folio y enviar resolución solicitud
/*	$("#bSubmit").click(function(){
		dataString=$("form[name='form-factura-lote']").serialize();
		$.ajax({
			type: "post",
			url: "/",
			data: dataString,
			dataType: "html",
			success: function(data) {
				alert('success: '+data)
			},
			error: function(data) {
				alert('error: '+data)
			}
		});
	});
*/
});
@endsection

@section('css')
@parent
{{-- <style> --}}
.table td.column-fecha_sol,
.table td.column-importe_clp,
.table td.column-total_uf {
	text-align: right;
}
{{-- </style> --}}
@endsection
