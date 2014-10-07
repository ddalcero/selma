@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>

        <div class="row-fluid">
        	<div class="span12">
        		<h3>Stickers activos</h3>

        		{{ Table::striped_bordered_hover_condensed_open() }}
        		{{ Table::headers('Fecha','Sub-proyecto','Texto','') }}
        		{{ Table::body($stickers->results)->created_at(function($stick){return ViewFormat::dateTimeFromDB($stick->created_at);})->ver(function($stick)use($year,$month){return HTML::link('/actividad/'.$year.'/'.$month.'/'.$stick->spj_id.'/edit','Ver');})->ignore('id','updated_at','user_id')->order('created_at','spj_id','text','ver') }}
        		{{ Table::close() }}

				{{ $stickers->links() }}
        	</div>

        </div>

    </div>
</div>
@endsection
