@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
	@include('main/left')
	<div class="span9">
		<br/>

		@include('factory/home')
		@include('factory/formulario')

	</div>
</div>
@endsection
