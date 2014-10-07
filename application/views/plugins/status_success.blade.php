<div class="alert alert-success">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<h4 class="alert-heading">Éxito</h4>
	@if (is_array(Session::get('success')))
		<ul>
		@foreach (Session::get('success') as $success)
			<li>{{ $success }}</li>
		@endforeach
		</ul>
	@else
		{{ Session::get('success') }}
	@endif
</div>
