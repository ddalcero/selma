@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
	@include('main/left')
	<div class="span9">

		<br/>
		<h3>Personal</h3>

		<div class="row-fluid">
			<div class="span12">

				{{ Table::striped_bordered_hover_condensed_open(array('id'=>'consultores')) }}
				{{ Table::headers('#','Consultor','Ficha','Tipo','Activo') }}
				{{ Table::body($personal)->ignore('matricula')->order('per_id','consultor','ficha','per_activite','activo') }}
				{{ Table::footers('','Consultor','Ficha','Tipo','Activo') }}
				{{ Table::close() }}

			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
@parent
$(document).ready(function() {
	$('#consultores').dataTable({
		"oLanguage": { "sUrl": "/js/datatables_es.txt" }
	}).columnFilter({
		aoColumns: [
			null,
			{ type: "text" },
			{ type: "number" },
			{ type: "select" },
			{ type: "select" }
		]
	});

	$('#consultores tr').click(function(event) {
		var id=$('.column-per_id', this).html();
		if (!isNaN(parseFloat(id)) && isFinite(id)) {
			window.location='/personal/'+id;
		}
	});

});
@endsection

@section('css')
@parent
.table td.column-ficha {text-align: right;}
.table td.column-per_activite {text-align: center;}
.table td {cursor:hand;cursor:pointer;}
input.number_filter {width: 80px;}
select.select_filter {width: 80px;}
@endsection
