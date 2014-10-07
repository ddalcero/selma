@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>
			<div class="row-fluid">
				<div class="span12">
					<h3>Candidatos</h3>
					<p class="lead">{{ $title }}</p>
					{{ Form::horizontal_open(null,null,array('name'=>'fCalculo')) }}
					<div class="row-fluid">
						<div class="span6">
						{{ Form::control_group(Form::label('genero', 'Sr./Sra.'),Form::small_select('genero',array('Sr.','Sra.'),$candidato->genero), '') }}
						{{ Form::control_group(Form::label('nombre', 'Nombre'),Form::text('nombre',$candidato->nombre), '') }}
						{{ Form::control_group(Form::label('apellidos', 'Apellidos'),Form::text('apellidos',$candidato->apellidos), '') }}
						{{ Form::control_group(Form::label('rut', 'RUT'),Form::small_text('rut',$candidato->rut,array('id'=>'rut')), '') }}
						{{ Form::control_group(Form::label('puesto', 'Puesto'),Form::text('puesto',$candidato->puesto), '') }}
						{{ Form::control_group(Form::label('fecha', 'Fecha ingreso prevista'),Form::small_text('fecha',ViewFormat::DateFromDB($candidato->fecha)), '') }}
						{{ Form::control_group(Form::label('liquido', 'Salario líquido'),Form::text('liquido',$candidato->liquido), '') }}
						</div>
						<div class="span6">
						{{ Form::control_group(Form::label('afp', 'AFP'),Form::select('afp',$afps,$salario->afp), '') }}
						{{ Form::control_group(Form::label('plazo', 'Plazo'),Form::select('plazo',$plazos,$salario->plazo), '') }}
						{{ Form::control_group(Form::label('ticketsino', 'Cheque restaurant'),Form::inline_labelled_checkbox('ticketsino','Si',1,$check,array('id'=>'cheques')), '') }}
						{{ Form::control_group(Form::label('valor', ($check)?'Valor cheque por día':'Asignación colación',array('id'=>'label_valor')),Form::small_text('valor',$valor), '') }}
						</div>
					</div>
					{{ Form::actions(array(Button::link(URL::to_route('candidatos'),'Lista de candidatos'),Button::link('#','Calcular',array('class'=>'btn-info','id'=>'bCalcular')),Form::submit('Grabar',array('class'=>'btn-success','id'=>'bSave')))) }}
					{{ Form::close() }}
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12" id="datosSalario">
				@if ($salario->liquido > 0)
				@include('tarifas.detalle')
				@endif
				</div>
			</div>
    </div>
</div>
@endsection

@section('js')
@parent
$(document).ready(function () {
	$("#rut").Rut({
		on_error: function(){
			alert('El rut ingresado es incorrecto');
		}
	});
	$("#bCalcular").click(function(){
		dataString=$("form[name='fCalculo']").serialize();
		$.ajax({
			type: "post",
			url: "/candidato/calcular",
			data: dataString,
			dataType: "html",
			success: function(data) {
				$('#datosSalario').html(data);
			},
			error: function(data) {
				alert('error: '+data)
			}
		});
	});
	$('#cheques').mousedown(function() {
		if ($(this).is(':checked')) {
			$(this).trigger("change");
			$('#label_valor').html('Asignación colación');
		}
		else {
			$(this).trigger("change");
			$('#label_valor').html('Valor cheque por día');
		}
	});
	$("#fecha").datepicker({
		changeMonth: true,
		numberOfMonths: 1,
	});
});
@endsection