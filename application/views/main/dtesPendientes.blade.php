@layout('layouts/main_fluid')

@section('content')
    <div class="row-fluid">
        @include('main/left')
        <div class="span9">
            <br/>

            <div class="row-fluid">
                <div class="span12">
                    <h3>DTEs pendientes</h3>

                    @if (count($emitidos->results)>0)
                        {{ Table::striped_bordered_hover_condensed_open(array('id'=>'tabla_dtes')) }}
                        {{ Table::headers('Cliente','Fecha Emision','Fecha Vencimiento','DTE','Tipo','Importe') }}
                        <?php echo Table::body($emitidos->results)
                                ->e_fechaemision(function($factura){return ViewFormat::dateFromDB($factura->e_fechaemision);})
                                ->e_fechavencimiento(function($factura){return ViewFormat::dateFromDB($factura->e_fechavencimiento);})
                                ->e_importe(function($factura){return ViewFormat::NFL($factura->e_importe);})
                                ->order('c_cliente','e_fechaemision','e_fechavencimiento','e_numfact','e_tipodte','e_importe');
                        ?>
                        {{ Table::close() }}
                        <div id="pagination">{{ $emitidos->links(); }}</div>
                    @else
                        <h4>No se han encontrado facturas emitidas en el periodo solicitado.</h4>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    @parent
    {{-- <style> --}}
    .table td.column-e_fechaemision,
    .table td.column-e_fechavencimiento,
    .table td.column-e_importe,
    .table td.column-tipo {
    text-align: right;
    }
    {{-- </style> --}}
@endsection
