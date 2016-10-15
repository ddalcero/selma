@layout('layouts/main')

@section('content')
<div class="hero-unit">
	<div class="row">
        <div class="span5">
            <h1>Bienvenidos...</h1>
            <form class="well" method="POST" action="{{ url('login') }}">
                <label for="email">Introduce tu nombre de usuario:</label>
                <input type="text" placeholder="nombre.usuario" name="username" id="username" />
                <label for="password">Contrase&ntilde;a:</label>
                <input type="password" placeholder="contrase&ntilde;a" name="password" id="password" />
                <br />
                <button type="submit" class="btn btn-success">Acceder</button>
            </form>
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
