@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>

        <div id="selClientes">
			@include('cliente/facturacion')
        </div>

    </div>
</div>
@endsection
