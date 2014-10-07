@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
	@include('main/left')
	<div class="span9">
		<br/>
		<div class="row-fluid">
			<div class="span12">
				<h3>{{ $title }}</h3>

				<div class="tabbable">
					<ul class="nav nav-tabs" id="myTabs">
						<li class="active"><a href="#tab1" data-toggle="tab">General</a></li>
						<li><a href="#tab2" data-toggle="tab">Actividad</a></li>
						<li><a href="#tab3" data-toggle="tab">Evaluaciones</a></li>
						<li><a href="#tab4" data-toggle="tab">Objetivos</a></li>
						<li><a href="#tab5" data-toggle="tab">Reuniones</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab1">
							<h4>General</h4>

	{{ Form::horizontal_open(null,null,array('name'=>'f_Organization')) }}
	{{ Form::control_group(Form::label('parent', 'Depende de:'),Form::span5_select('parent',$personal,$boss), '') }}
	{{ Form::control_group(Form::label('bossof[]', 'Supervisa:'),Form::span10_multiselect('bossof[]',$personal,$descendants,array('id'=>'bossof')), '') }}
	{{ Form::actions(array(Button::link('#','Grabar cambios',array('class'=>'btn-info','id'=>'bSubmit'))->with_icon('ok'))) }}

	{{ Form::close() }}

<pre>
PER_ID {{ $persona['per_id'] }} 

Fecha de nacimiento
{{ $persona['per_date_naissance'] }}

RUT
{{ $persona['per_no_secu'] }}

Dirección
{{ $persona['per_adrs1'] }}
{{ $persona['per_adrs2'] }}
{{ $persona['per_adrs3'] }}
{{ $persona['per_cp'] }}
{{ $persona['per_ville'] }}

e-mail
{{ $persona['per_email'] }}

Fecha de inicio
{{ $persona['per_date_arrive'] }}

Fecha de fin
{{ $persona['per_date_depart'] }}

Actividad
{{ $persona['per_activite'] }}

% Directo
{{ $persona['per_prct_direct'] }}
</pre>

						</div>
						<div class="tab-pane" id="tab2">
							<h4>Actividad</h4>
							<div class="row-fluid">
								<div class="span6" id="chartActividad" style="height: 250px; margin: 0 auto"></div>
								<div class="span6" id="chartActividadMes" style="height: 250px; margin: 0 auto"></div>
							</div>
						</div>
						<div class="tab-pane" id="tab3">
							<h4>Evaluaciones</h4>
						</div>
						<div class="tab-pane" id="tab4">
							<h4>Objetivos</h4>
						</div>
						<div class="tab-pane" id="tab5">
							<h4>Reuniones</h4>
							<div id="reuniones">
								<br/><i class="icon-spinner icon-spin icon-large"></i> Cargando...
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="span12">
				<a href="{{ URL::to_route('personal_index') }}" class="btn"><i class="icon-user"></i> Personal</a>
			</div>
		</div>
	</div>
</div>
@endsection

@section('css')
@parent
.selAuto { width: 340px; }
@endsection

@section('js')
@parent
$(document).ready(function () {

	var reuniones_ok=false;

	$("#bossof").select2({
		placeholder: "Personal a cargo"
	});

	$("#parent").select2({
		placeholder: "Jefe"
	});

	//$("form[name='f_Organization']").submit(function(e){
	$("#bSubmit").click(function(){
		dataString=$("form[name='f_Organization']").serialize();
		$.ajax({
			type: "post",
			url: "/personal/{{$persona['per_id']}}/organization",
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

	$('#myTabs').bind('show', function(e) {
		var pattern=/#.+/gi //use regex to get anchor(==selector)
		var contentID = e.target.toString().match(pattern)[0]; //get anchor

		{{-- // tab2 activo, mostrar el gráfico --}}
		if (contentID=='#tab2') {
			$('#chartActividad').highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false
				},
				title: { text: 'Productividad' },
				tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							formatter: function() {
								return '<b>'+ this.point.name +'</b>'+ (Math.round(this.percentage*10))/10 +' %';
							}
						}
					}
				},
				series: [{
					type: 'pie',
					name: 'Actividad',
					data: {{ $resumen }}
				}]
			});
			$('#chartActividadMes').highcharts({
				chart: {
					type: 'area',
				},
				title: {
					text: 'Productividad mensual',
					x: -20 //center
				},
				xAxis: {
					categories: {{ $periodo }}
				},
				yAxis: {
					title: {
						text: 'Días',
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.percentage:.1f}%</b> ({point.y:,.0f})<br/>',
					shared: true
				},
				plotOptions: {
					area: {
						stacking: 'normal',
						lineColor: '#ffffff',
						lineWidth: 1,
						fillOpacity: 0.5,
						marker: {
							lineWidth: 1,
							lineColor: '#ffffff'
						}
					}
				},
				legend: {
					layout: 'vertical',
					align: 'right',
					verticalAlign: 'top',
					x: -10,
					y: 100,
					borderWidth: 0
				},
				series: {{ $series }}
			});
		}
		else if (contentID=='#tab5') {
			if (!reuniones_ok) {
				$('#reuniones').load('/reuniones/{{ $persona['per_id'] }}');
				reuniones_ok=true;
			}
		}
	});

});
@endsection