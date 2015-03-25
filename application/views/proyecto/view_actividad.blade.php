@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
	<div class="span9">
		<br/>
		<h3>Actividad</h3>

		<p class="lead">Resumen de la actividad mensual</p>

		@if (count($actividad)>0)

            {{-- Cabecera proyecto --}}
            <dl class="dl-horizontal">
                <dt>Cliente</dt>
                <dd>{{ $cliente }}</dd>
                @if ($edit_link!=null)
                <dt>Proyecto</dt>
                <dd>{{ $actividad[0]['spj_libelle'] }}</dt>
                @endif
                <dt>Periodo</dt>
                <dd>{{ Session::get('sPeriodo') }}</dd>
                <dt>Valor UF</dt>
                <dd>{{ ViewFormat::NFL($valores->uf,2) }}</dt>
                <dt>Días periodo<br/></dt>
                <dd>{{ $valores->pdays }}<br/></dt>
                <dt>Tipo proyecto</dt>
                <dd>{{ TipoProyecto::$Description[$tipo_proyecto->tipoproyecto] }} ({{ Proyecto::$Tipo[$wipm] }})</dd>
            </dl>
            <hr/>

            @if ($wipm==5)
                {{-- Tipo Proyecto = 5 --}}
                <div class="row-fluid">
                    <div class="span12">
                        <h4>Detalle actividad FP</h4>
                        <div id="spline-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            @endif

            @if ($tipo_proyecto->tipoproyecto==0)
                {{-- Tipo Proyecto = 0 --}}
                <div class="row-fluid">
                    <div class="span12">
                        <h4>Detalle actividad</h4>
                        <table class="table table-condensed table-hover table-bordered table-striped">
                            <thead>
                                <th>Consultor</th>
                                <th>Tarifa&nbsp;mes</th>
                                <th>Dias</th>
                                <th>Descuento</th>
                                <th>Valor&nbsp;UF</th>
                                <th>Valor&nbsp;$</th>
                            </thead>
                            <tbody>
                                @foreach ($actividad as $linea)
                                <tr>
                                    <td class="column-consultor">{{ $linea['consultor'] }}</td>
                                    <td class="column-tarifa_uf rightCell">{{ ViewFormat::NFL($linea['tarifa_uf'],2) }}</td>
                                    <td class="column-imputado rightCell">{{ ViewFormat::NFL($linea['imputado']) }}</td>
                                    <td class="column-descuento rightCell">{{ ViewFormat::NFL($linea['descuento']) }}</td>
                                    <td class="column-valoruf rightCell">{{ ViewFormat::NFL($linea['valoruf'],2) }}</td>
                                    <td class="column-realizado rightCell">{{ ViewFormat::NFL($linea['realizado']) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="rightCell">TOTAL</td>
                                    <td class="rightCell"><strong>{{ ViewFormat::NFL($total['realizado_uf'],2) }}</strong></td>
                                    <td class="rightCell"><strong>{{ ViewFormat::NFL($total['realizado']) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @if (count($descuentos)>0)
                <div class="row-fluid">
                    <div class="span9">
                        <h4>Detalle descuentos</h4>
                        {{ Table::open_condensed_hover() }}
                        {{ Table::headers('Consultor','Motivo','Días') }}
                        {{ Table::body($descuentos) }}
                        {{ Table::close() }}
                    </div>
                </div>
                @endif
            @endif

        @else
            <p class="text-warning">No se ha encontrado actividad para este proyecto en este periodo</p>
        @endif

		<div class="row-fluid">
			<div class="span9">
			<p>
			@if ($edit_link!=null && Sentry::user()->has_access('mod_realizado'))
			{{ HTML::link($edit_link,'Editar',array('class'=>'btn')) }}  
			@endif
			{{ HTML::link('main/realizado','Volver a proyectos',array('class'=>'btn btn-primary')) }} 
			</p>
			</div>
		</div>

	</div>
</div>
@endsection

@section('css')
@parent
{{-- <style> --}}
.table th.rightCell,
.table td.rightCell,
.table td.column-imputado {
  text-align: right;
}
{{-- </style> --}}
@endsection

@if ($wipm==5)
@section('js')
@parent
$(document).ready(function() {
    $('#spline-container').highcharts({
        chart: {
            type: 'spline'
        },
        title: {
            text: '% Avance proyecto'
        },
        xAxis: {
            categories: [ '{{ implode("','",array_keys($history)) }}' ]
        },
        yAxis: {
            title: {
                text: '% avance'
            },
            labels: {
                formatter: function () {
                    return this.value + '%';
                }
            }
        },
        tooltip: {
            crosshairs: true,
            shared: true
        },
        plotOptions: {
            spline: {
                marker: {
                    radius: 4,
                    lineColor: '#666666',
                    lineWidth: 1
                }
            }
        },
        series: [{
            data: [ {{ implode(",",array_values($history)) }} ]
        }]
    });
});
@endsection
@endif
