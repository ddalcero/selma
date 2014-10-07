@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
	@include('main/left')
	<div class="span9">
		<br/>
			<div class="row-fluid">
				<div class="span12">
					<h3>Candidatos</h3>
					<p class="lead">{{ $title }}</p>
						{{ Table::striped_bordered_hover_condensed_open() }}
						{{ Table::headers('#','Candidato','Puesto','Liquido','Fecha','') }}
						<?php
							echo Table::body($candidatos->results)
									->candidato(function($cand){return $cand->nombre.' '.$cand->apellidos;})
									->liquido(function($cand){return ViewFormat::NFL($cand->liquido);})
									->fecha(function($cand){return ViewFormat::DateFromDb($cand->fecha);})
									->modificar(function($cand){return HTML::link_to_route('edit_candidato','Modificar',array($cand->id));})
									->ignore('nombre','apellidos','salario_json','created_at','updated_at','genero','rut')
									->order('id','candidato','puesto','liquido','fecha','modificar');
						?>
						{{ Table::close() }}
						{{ $candidatos->links() }}
						<hr/>
						{{ Button::link(URL::to_route('new_candidato'),'Nuevo candidato') }}
				</div>
			</div>
	</div>
</div>
@endsection

@section('css')
@parent
.table td.column-liquido { text-align:right;}
.table td.column-fecha { text-align:center;}
@endsection