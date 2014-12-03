@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
	@include('main/left')
	<div class="span9">
		<div class="row-fluid">
			<div class="span7">
				<br/>
				<h4>Cambiar contraseña</h4>
				{{ Form::horizontal_open(null,'PUT') }}
				{{ Form::control_group(Form::label('first_name', 'Nombre'),Form::text('first_name', Sentry::user()->get('metadata.first_name'), array('class' => 'span5', 'placeholder' => 'Nombre')),'') }}
				{{ Form::control_group(Form::label('last_name', 'Apellidos'),Form::text('last_name', Sentry::user()->get('metadata.last_name'), array('class' => 'span5', 'placeholder' => 'Apellidos')),'') }}
				{{ Form::control_group(Form::label('oldpassword', 'Contraseña actual'),Form::password('oldpassword', array('class' => 'span4', 'placeholder' => 'old password')),'') }}
				{{ Form::control_group(Form::label('newpassword', 'Nueva contraseña'),Form::password('newpassword', array('class' => 'span4', 'placeholder' => 'password')),'') }}
				{{ Form::control_group(Form::label('confirm', 'Confirmar contraseña'),Form::password('confirm', array('class' => 'span4', 'placeholder' => 'password')),'') }}
				{{ Form::submit('<i class="fa fa-ok"></i> Actualizar',array('class'=>'btn btn-success')) }}
				<a href="{{ URL::to('main') }}" class="btn"><i class="fa fa-remove"></i> Cancelar</a>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@endsection
