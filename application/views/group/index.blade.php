@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>
        <h4>Gestión de grupos</h4>

        <p><a href="{{ URL::to_route('group_new') }}" class="btn btn-primary"><i class=""></i> Añadir grupo</a></p>

@if (count($groups)>0)

        <h5>Grupos activos</h5>

        <div class="span6">
            {{ Table::striped_bordered_hover_condensed_open() }}
            {{ Table::headers('','Nombre') }}
            <?php echo Table::body($groups)
                    ->ignore('permissions')
                    ->edit(function($group) {
                        return HTML::link_to_route('group_detail','Modificar',array($group['id']));
                    });
            ?>
            {{ Table::close() }}
        </div>
@else

        <h5>No se han encontrado grupos</h5>
@endif

    </div>
</div>
@endsection