
@if ($proyecto!==null)

	<h4>Proyectos</h4>
	<h5>Cliente</h5>
	<div id="subSel">
		{{ Table::striped_bordered_hover_condensed_open(array('id'=>'tabla_proyectos')) }}
		{{ Table::headers('Proyecto','') }}
		{{ Table::body($proyecto)->ignore('spj_id','clt_id','prj_id')->order('spj_libelle','link_f') }}
		{{ Table::close() }}
	</div>

@else
	<h4>No se han encontrado proyectos activos para este cliente en este periodo.</h4>
@endif

