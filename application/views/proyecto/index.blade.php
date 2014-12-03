
@if ($proyecto!==null)

	<h4>Proyectos</h4>
	<h5>Cliente</h5>
	<div id="subSel">
		{{ Table::striped_bordered_hover_condensed_open(array('id'=>'tabla_proyectos')) }}
		{{ Table::headers('','Proyecto','') }}
		@if (Sentry::user()->has_access('mod_realizado'))
		{{ Table::body($proyecto)->ignore('spj_id','clt_id','prj_id')->order('chk_id','spj_libelle','link_c','link_v') }}
		@else 
		{{ Table::body($proyecto)->ignore('spj_id','clt_id','prj_id','link_c')->order('chk_id','spj_libelle','link_c','link_v') }}
		@endif
		{{ Table::close() }}
	</div>

<button class="btn btn-mini" type="button" id="selAll"><i class="fa fa-asterisk"></i> Marcar todos</button>
<button class="btn btn-mini btn-primary" type="button" id="showSel"><i class="fa fa-eye icon-white"></i> Visualizar seleccionados</button>

<style>.column-spj_libelle { cursor: hand; cursor: pointer; }</style>
<script>
$(document).ready(function() {

	$('#tabla_proyectos tr').click(function(event) {
		if (event.target.type !== 'checkbox' && $(event.target).attr('class')=='column-spj_libelle') {
			$(':checkbox', this).trigger('click');
		}
	});

	$("#selAll").click(function() {
		$('#subSel input').prop('checked', true);
	});

	$("#showSel").click(function() {
		var count = $('#subSel input:checked').length;
		var selected = $('#subSel input:checked').map(function(i,el){return el.name;}).get().join(',');
		if (count) {
			var url='/actividad/{{ Session::get('sPeriodo') }}?proyectos='+selected;
			//alert('URL: '+url);
			window.location=url;
		}
	});
})
</script>

@else
	<h4>No se han encontrado proyectos activos para este cliente en este periodo.</h4>
@endif

