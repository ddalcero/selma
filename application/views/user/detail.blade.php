@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <div class="row-fluid">
            <div class="span7">
                <br/>
                <h4>Usuario</h4>

                @if (isset($user->id))
                {{ Form::horizontal_open(null,'PUT') }}
                @else
                {{ Form::horizontal_open() }}
                @endif

                {{ Form::control_group(Form::label('first_name', 'Nombre'),Form::text('first_name', $metadata->first_name, array('class' => 'span5', 'placeholder' => 'Nombre')),'') }}
                {{ Form::control_group(Form::label('last_name', 'Apellidos'),Form::text('last_name', $metadata->last_name, array('class' => 'span5', 'placeholder' => 'Apellidos')),'') }}

                {{ Form::control_group(Form::label('password', 'Contraseña'),Form::password('password', array('class' => 'span4', 'placeholder' => 'password')),'') }}
                {{ Form::control_group(Form::label('confirm', 'Confirmar contraseña'),Form::password('confirm', array('class' => 'span4', 'placeholder' => 'password')),'') }}

                {{ Form::control_group(Form::label('email', 'e-mail'),Form::text('email', $user->email, array('class' => 'span7', 'placeholder' => 'e-mail')),'') }}
                {{ Form::control_group(Form::label('rut', 'RUT'),Form::text('rut', $metadata->rut, array('class' => 'span3', 'placeholder' => 'RUT')),'') }}

                {{ Form::control_group(Form::label('per_id', 'Usuario OLGA:'),Form::span9_select('per_id',$personal,$metadata->per_id), '') }}

                {{ Form::submit('<i class="icon-ok"></i> '.(isset($action_text)?$action_text:'Grabar'),array('class'=>'btn btn-success')) }}
                <a href="{{ URL::to_route('user_list') }}" class="btn"><i class="icon-remove"></i> Cancelar</a>
            </div>
            <div span="class5">
                <br/><br/>
                <h4>Grupos</h4>
                <h5>Asociar grupos</h5>

                @if (isset($groups)&&(count($groups)>0))
                    @foreach ($groups as $id_grupo=>$grupo)
                    {{ Form::labelled_checkbox('group['.$id_grupo.']', $grupo['name'], $id_grupo, $grupo['checked']) }}
                    @endforeach
                @else
                    No hay grupos.
                @endif

                {{ Form::close() }}

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@parent
$(document).ready(function () {
    $("#per_id").select2({
        placeholder: "Jefe"
    });
});
@endsection
