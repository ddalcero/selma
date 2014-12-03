@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>
        <h4>Gestión de usuarios</h4>
        <h5>Usuarios activos</h5>
        <p><a href="{{ URL::to_route('user_new') }}" class="btn btn-primary"><i class=""></i> Añadir usuario</a></p>

{{ Table::striped_bordered_hover_condensed_open(array('id'=>'usuarios')) }}
{{ Table::headers('','Nombre','e-mail','Último login','','') }}
<?php echo Table::body($users)
        ->ignore('username','password','password_reset_hash','temp_password','remember_me','activation_hash','ip_address','status','activated','permissions','created_at','updated_at')
        ->meta(function($user) {
            $meta=User::find($user['id'])->metadata()->first(); 
            return $meta->first_name.' '.$meta->last_name;
        })
        ->last_login(function($user){
            return ViewFormat::dateTimeFromDB($user['last_login']);
        })
        ->edit(function($user) {
            return HTML::link_to_route('user_detail', 'Modificar',array($user['id']));
        })
        ->delete(function($user) {
            return HTML::link_to_route('user_delete', 'Eliminar', array($user['id']), array('data-method' => 'delete'));
        })
        ->order('id','meta','email','last_login','edit','delete');
?>
{{ Table::footers('','Nombre','e-mail','','','') }}
{{ Table::close() }}

    </div>
</div>
@endsection

@section('js')
@parent
$(document).ready(function() {
    $('#usuarios').dataTable({
        "oLanguage": { "sUrl": "/js/datatables_es.txt" }
    }).columnFilter({
        aoColumns: [
            null,
            { type: "text" },
            { type: "text" },
            null,
            null,
            null
        ]
    });
});
@endsection
