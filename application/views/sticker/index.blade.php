     <a href="#" id="show_hide">Sticker</a>
<div class="well stick well-small" id="#mystickers">
		{{ Form::vertical_open(null,null,array('name' => 'f_Sticker')) }}
		{{ Form::hidden('spj_id',$sticker->spj_id) }}
		{{ Form::textarea('text',$sticker->text,array('rows' => '4','class'=>'input-block-level')) }}
		<br/>
		@if ($method!='POST')
		{{ Form::submit('Actualizar',array('class'=>'btn-mini btn-info')) }}
		{{ Button::mini_danger_link('#','Eliminar',array('id'=>'bEliminar')) }}
		@else
		{{ Form::submit('Grabar',array('class'=>'btn-mini btn-info')) }}
		@endif
		{{ Form::close() }}
	</div>
</div>
<style>
.well.stick.well-small {
	background-color: #FFFFE0;
	display:none;
}
</style>
<script>
$(document).ready(function(){
	$('#bEliminar').click(function(){
		$.ajax({
			type: "delete",
			url: "/sticker/{{$sticker->spj_id}}",
			dataType: "html",
			success: function(data) {
				$("#sticker").html(data)
			},
			error: function(data) {
				$("#sticker").html(data)
			}
		});
	});

	@if (isset($show) && $show)
	$('.well.stick').show();
	@else
	$('.well.stick').hide();
	@endif

	$('#show_hide').click(function(){
		$('.well.stick').slideToggle();
	});

    $("form[name='f_Sticker']").submit(function(e){
		e.preventDefault();
		dataString = $("form[name='f_Sticker']").serialize();
		$.ajax({
			type: "{{$method}}",
			url: "/sticker/{{$sticker->spj_id}}",
			data: dataString,
			dataType: "html",
			success: function(data) {
				$("#sticker").html(data)
			},
			error: function(data) {
				alert('failure: '+data);
			}
		});
    });


});
</script>