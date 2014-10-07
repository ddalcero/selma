@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>

        <div class="row-fluid">
        	<div class="span12">
        		<h3>Proyectos pendientes</h3> 
        		<h4>Periodo {{$month}}/{{$year}}</h4>
        	</div>

        </div>

    </div>
</div>
@endsection
