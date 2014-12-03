@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>

        <div class="row-fluid">
        	<div class="span12">
              <h3>Gestión de solicitudes de facturación</h3>
@if (count($pendientes->results)>0)
{{ Table::striped_bordered_hover_condensed_open(array('id'=>'tabla_pendientes')) }}
{{ Table::headers('Solicitante','Fecha solicitud','Cliente','Glosa','Importe Neto $','Importe UF','') }}
<?php echo Table::body($pendientes->results)
        ->ignore('id','estado','user_id','fecha_fac','lot_id','tipo_dte','nr_dte','clt_id','spj_id','detalle','tasa_iva','iva','total','valor_uf')
        ->meta(function($pendiente) {
            $meta=User::find($pendiente->user_id)->metadata()->first(); 
            return $meta->first_name.' '.$meta->last_name;
        })
        ->fecha_sol(function($pendiente){return ViewFormat::dateTimeFromDB($pendiente->fecha_sol);})
        ->importe_clp(function($pendiente){return ViewFormat::NFL($pendiente->importe_clp);})
        ->total_uf(function($pendiente){return ViewFormat::NFL($pendiente->total_uf,2);})
        ->button(function($pendiente){return "<a href='/solicitud/".$pendiente->id."' class='btn btn-primary'><i class='fa fa-pencil'></i> </a>";})
        ->order('meta','fecha_sol','cliente','glosa','importe_clp','total_uf','button');
?>
{{ Table::close() }}
{{ $pendientes->links(); }}
@else
<h4>No se han encontrado solicitudes de facturas pendientes de emitir.</h4>
@endif
        	</div>
        </div>
    </div>
</div>
@endsection

@section('css')
@parent
{{-- <style> --}}
.table td.column-fecha_sol,
.table td.column-importe_clp,
.table td.column-total_uf {
  text-align: right;
}
{{-- </style> --}}
@endsection
