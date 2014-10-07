<div class="alert alert-error">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<h4 class="alert-heading">Error</h4>
	@if (is_array(Session::get('error')))
		<ul>
		@foreach (Session::get('error') as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
	@else
		{{ Session::get('error') }}
	@endif
</div>
