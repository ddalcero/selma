<div class="accordion" id="accordion2">
@foreach ($reuniones as $reunion)
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#Collapse{{$reunion->id}}">
		{{ ViewFormat::DateTimeFromDB($reunion->created_at) }} - <strong>{{ $reunion->subject }}</strong>
			</a>
		</div>
		<div id="Collapse{{$reunion->id}}" class="accordion-body collapse">
			<div class="accordion-inner">
		{{ $reunion->text }} 
			</div>
		</div>
	</div>
@endforeach
</div>
