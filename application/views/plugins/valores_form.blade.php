{{ Form::inline_open(null,null,array('name' => 'f_Valor')) }}
{{ Form::prepend(Form::text('uf', $valor->uf , array('class' => 'input-small', 'placeholder' => 'UF')),'UF') }} 
{{ Form::prepend(Form::text('pdays', $valor->pdays ,array('class' => 'input-small', 'placeholder' => 'dias')),'Días') }} 
@if (Sentry::user()->has_access('mod_realizado'))
{{ Form::submit('Actualizar') }}
@endif
{{ Form::close() }}

@include('plugins/valores_js')
