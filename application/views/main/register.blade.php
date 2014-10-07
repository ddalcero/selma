@layout('layouts/main')

@section('content')
<div class="hero-unit">
	<div class="row">
        <div class="span6">

			{{ Form::vertical_open() }}
			{{ Form::label('username', 'Nombre') }}
			{{ Form::text('username', null, array('class' => 'span3', 'placeholder' => 'Nombre y Apellido')) }}
			{{ Form::label('email', 'e-mail') }}
			{{ Form::text('email', null, array('class' => 'span3', 'placeholder' => 'e-mail')) }}
			{{ Form::label('password', 'Contraseña') }}
			{{ Form::password('password', array('class' => 'span3', 'placeholder' => 'password')) }}
			{{ Form::label('confirm', 'Confirmar contraseña') }}
			{{ Form::password('confirm', array('class' => 'span3', 'placeholder' => 'password')) }}

			{{ Form::block_help('Introduzca su nombre, apellido, dirección de e-mail y contraseña para registrarse.') }}

			{{ Form::submit('Registrarse') }}
			{{ Form::close() }}

        </div>
<!--        
        <div class="span4">
            <img src="{{ asset('img/logo_hd.png') }}" alt="{{ Config::get('project.title') }}" />
        </div>
-->
    </div>
</div>
@endsection

@section('js')
@parent
{{-- <script> --}}
$(document).ready(function() {
});
{{-- </script> --}}
@endsection

@section('css')
@parent
{{-- <style> --}}
.hero-unit h1 {
	margin-bottom: 30px;
}
{{-- </style> --}}
@endsection

