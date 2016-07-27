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

@section('css')
@parent
{{-- <style> --}}
.table th.rightCell,
.table td.rightCell,
.table td.column-imputado {
  text-align: right;
}
{{-- </style> --}}
@endsection

