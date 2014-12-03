@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
	<div class="span9">
		<br/>
		<h4>Grupo</h4>

			@if (!isset($method))
			{{ Form::horizontal_open() }}
			@else
			{{ Form::horizontal_open(null,$method) }}
			@endif
			{{ Form::control_group(Form::label('name', 'Nombre grupo'),Form::text('name', (isset($group)?$group->name:null), array('class' => 'span3', 'placeholder' => 'Nombre del grupo')),'',Form::block_help('Introduzca el nombre del grupo.')) }}

			<hr/>

			@foreach ($rules as $rule=>$active)

			{{ Form::control_group(Form::label($rule,$rule),Form::inline_labelled_radio('rules['.$rule.']', 'On', 1, $active).''.Form::inline_labelled_radio('rules['.$rule.']', 'Off', 0, !$active)) }}
			
			@endforeach

			{{ Form::submit('<i class="fa fa-ok"></i> '.(isset($action_text)?$action_text:'Grabar'),array('class'=>'btn btn-success')) }}
			<a href="{{ URL::to_route('group_list') }}" class="btn"><i class="fa fa-remove"></i> Cancelar</a>


			{{ Form::close() }}

<!--
		@if (isset($group))
		<div class="row-fluid">
			<div class="span6">
				<pre>
					<?php var_dump($users_not); ?>
				</pre>
			</div>

			<div class="span6">
				<pre>
					<?php var_dump($users_in_group); ?>
				</pre>
			</div>
		</div>
		@endif
-->

	</div>
</div>
@endsection
