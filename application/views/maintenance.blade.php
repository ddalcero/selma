@layout('layouts/main')

@section('content')
<div class="hero-unit">
	<div class="row">
        <div class="span5">
            <h1>Bienvenidos...</h1>
            <h3>Mantención en curso</h3>
            <h4></h4>
        </div>
        <div class="span5">
            <img src="{{ asset('img/logo_hd.png') }}" alt="{{ Config::get('project.title') }}" />
        </div>
    </div>
</div>
@endsection

@section('js')
@parent
{{-- <script> --}}
$(document).ready(function() {
});
{{-- </script> --}}
@endsection

@section('css')
@parent
{{-- <style> --}}
.hero-unit h1 {
	margin-bottom: 30px;
}
{{-- </style> --}}
@endsection
