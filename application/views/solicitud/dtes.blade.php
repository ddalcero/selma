@if (isset($emitidos))
@if (count($emitidos->results)>0)
{{ Table::striped_bordered_hover_condensed_open(array('id'=>'tabla_dtes')) }}
{{ Table::headers('Fecha Emision','Cliente','DTE','Tipo','Cod.','Importe') }}
<?php echo Table::body($emitidos->results)
	->e_fechaemision(function($factura){return ViewFormat::dateFromDB($factura->e_fechaemision);})
	->tdteval(function($factura){return $factura->e_tipodte=='34'?'Exenta':'Afecta';})
	->e_importe(function($factura){return ViewFormat::NFL($factura->e_importe);})
	->order('e_fechaemision','c_cliente','e_numfact','tdteval','e_tipodte','e_importe');
?>
{{ Table::close() }}
<div id="pagination">{{ $emitidos->links(); }}</div>
@else
<h4>No se han encontrado facturas emitidas en el periodo solicitado.</h4>
@endif
@endif