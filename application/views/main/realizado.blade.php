@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>

        @include('plugins/valores_select')

        <div id="selClientes">

    	</div>

    </div>
</div>
@endsection
