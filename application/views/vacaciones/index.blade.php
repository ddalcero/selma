@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
	@include('main/left')
	<div class="span9">
		<br/>
		<div class="row-fluid">
			<div class="span12">
				<h3>Mis vacaciones</h3>

				<div class="tabbable"> <!-- Only required for left/right tabs -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab1" data-toggle="tab">Solicitadas y aprobadas</a></li>
						<li><a href="#tab2" data-toggle="tab">Nueva solicitud</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab1">
							<h4>Solicitadas</h4>

							@if (isset($solicitadas) && count($solicitadas)>0)
							<div class="row-fluid">
								<div class="span9">
									{{ Table::striped_bordered_hover_condensed_open() }}
									{{ Table::headers('Desde','Hasta','Días','Observaciones') }}
									{{ Table::body($solicitadas)->ignore('ficha','estado','fadesde','fahasta','ndiasap')->fsdesde(function($dia){return ViewFormat::DateFromDB($dia->fsdesde);})->fshasta(function($dia){return ViewFormat::DateFromDB($dia->fshasta);}) }}
									{{ Table::close() }}
								</div>
							</div>
							@else
							<h5>No hay vacaciones solicitadas pendientes</h5>
							@endif
							<hr/>
							@if (isset($aprobadas) && count($aprobadas)>0)
							<div class="row-fluid">
								<div class="span9">
									<h4>Aprobadas</h4>
									{{ Table::striped_bordered_hover_condensed_open() }}
									{{ Table::headers('Desde','Hasta','Días','Observaciones') }}
									{{ Table::body($aprobadas)->ignore('ficha','estado','fsdesde','fshasta','ndias')->fadesde(function($dia){return ViewFormat::DateFromDB($dia->fadesde);})->fahasta(function($dia){return ViewFormat::DateFromDB($dia->fahasta);}) }}
									{{ Table::close() }}
								</div>
							</div>
							@endif
						</div>
						<div class="tab-pane" id="tab2">
							<h4>Nueva solicitud de vacaciones</h4>
							{{ Form::horizontal_open() }}
							{{ Form::control_group(Form::label('FsDesde','Desde'),Form::small_text('FsDesde'),'Fecha de inicio',Form::block_help('Fecha de inicio de las vacaciones (1er día de vacaciones).')) }}
							{{ Form::control_group(Form::label('FsHasta','Hasta'),Form::small_text('FsHasta'),'Fecha final',Form::block_help('Fecha final de las vacaciones (último día de vacaciones). Si solicita un solo día, el mismo día de inicio.')) }}
							{{ Form::control_group(Form::label('NDias','Días'),Form::mini_text('NDias','',array('class'=>'disabled', 'disabled' => 'disabled'))) }}
							{{ Form::control_group(Form::label('Observ','Observaciones'),Form::xlarge_textarea('Observaciones','', array('rows' => '3')),'') }}
							{{ Form::actions(array(Button::primary_submit('Enviar solicitud'))) }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
@parent
$(document).ready(function() {
	$("#FsDesde").datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function(selectedDate) {
			$("#FsHasta").datepicker("option", "minDate", selectedDate);
			var toDate=$("#FsHasta").val();
			if (toDate.length>0 && selectedDate.length>0) {
//				alert('la otra fecha hasta: '+toDate+' mide '+toDate.length);
				var data={
					'start' : selectedDate,
					'end' : toDate
				};
				ajaxCall('/vacaciones/dias',data,function(msg){
					$("#NDias").val(msg);
				});
			}
		}
	});
	$("#FsHasta").datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function(selectedDate) {
			$("#FsDesde").datepicker("option", "maxDate", selectedDate);
			var fromDate=$("#FsDesde").val();
			if (fromDate.length>0 && selectedDate.length>0) {
//				alert('la otra fecha desde: '+fromDate+' mide '+fromDate.length);
				var data={
					'start' : fromDate,
					'end' : selectedDate
				};
				ajaxCall('/vacaciones/dias',data,function(msg){
					$("#NDias").val(msg);
				});
			}
		}
	});
});
@endsection
