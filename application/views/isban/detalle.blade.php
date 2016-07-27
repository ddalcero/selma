
<div class="row-fluid">
    <div class="span12">
        @if (count($proyectos)>0)
        {{ Form::horizontal_open('/isban', 'POST') }}
        <h4>Detalle actividad</h4>
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <th>#</th>
                <th>Consultor</th>
                <th>Imputados</th>
                <th>Tarifa&nbsp;hora</th>
                <th>Horas fact.</th>
                <th>Valor&nbsp;$</th>
                <th> &nbsp;&nbsp; </th>
            </thead>
            <tbody>
                @foreach ($proyectos as $linea)
                <tr class="datos">
                    @if ($linea['fsi_id'] != 0)
                    <td class="column-spj_id"><a href="/facturar/{{ $linea['spj_id'] }}">{{ $linea['spj_id'] }}</a></td>
                    <td class="column-consultor">{{ $linea['consultor'] }}</td>
                    <td class="column-imputado rightCell">{{ ViewFormat::NFL($linea['imputado']) }}</td>
                    <td class="column-tarifa rightCell">{{ ViewFormat::NFL($linea['tarifa']/9,2) }}</td>
                    <td class="column-horas rightCell"><?php echo (($linea['tarifa']==0)?"0":ViewFormat::NFL($linea['importe']/($linea['tarifa'])*9,2));?></td>
                    <td class="column-realizado rightCell">{{ ViewFormat::NFL($linea['importe']) }}</td>
                    <td class="column-acciones">
                	{{ Label::success('Emitida') }}
                 	@else
                    <td class="column-spj_id"><a href="/facturar/{{ $linea['spj_id'] }}">{{ $linea['spj_id'] }}</a></td>
                    <td class="column-consultor">{{ $linea['consultor'] }}</td>
                    <td class="column-imputado rightCell">{{ ViewFormat::NFL($linea['imputado']) }}</td>
                    <td class="column-tarifa rightCell">{{ Form::text('tarifa[]', ViewFormat::NFL($linea['tarifa_hh'],2),array('class' => 'input-small', 'placeholder' => 'tarifa')) }}
                    </td>
                    <td class="column-horas rightCell">
                        {{ Form::text('horas[]', ViewFormat::NFL($linea['hh'],2),array('class' => 'input-small', 'placeholder' => 'hh')) }}
                    </td>
                    <td class="column-realizado rightCell">{{ ViewFormat::NFL($linea['importe']) }}</td>
                    <td class="column-acciones">
                    @if (!$linea['tarifa'])
                    {{ Label::warning('Tarifa') }}
                    @endif
                    {{ Form::hidden('lot_id[]',$linea['lot_id']) }}
                    {{ Form::hidden('importe_calc[]',$linea['importe']) }}
                    </td>
                    @endif
                 </tr>
                @endforeach
            </tbody>
            <tfoot>
                <td>&nbsp;</td>
                <td>TOTAL</td>
                <td class="rightCell">{{ ViewFormat::NFL($total['dias'],2) }}</td>
                <td>&nbsp;</td>
                <td class="column-totalhoras rightCell">{{ ViewFormat::NFL($total['hh'],2) }}</td>
                <td class="column-total rightCell">{{ ViewFormat::NFL($total['clp']) }}</td>
                <td> &nbsp;&nbsp; </td>
            </tfoot>
        </table>
        @if ($total['pend']>0)
        <button id="bRecalcular" class="btn btn-info" type="button"><i class="fa fa-list-alt icon-white"></i> Recalcular</button>
        {{ Form::submit('<i class="fa fa-ok"></i> Actualizar',array('class'=>'btn btn-success')) }}
        @endif
        {{ Form::close() }}
        @else
            <p class="text-warning">No se ha encontrado actividad en este periodo</p>
        @endif
    </div>
</div>

<script>
$('#bRecalcular').click(function() {
    var imp,tarifa,horas,realizado,total=0,totalhoras=0;
    $('table tr.datos').each(function(){
        tarifa=parseFloat($("input[name='tarifa[]']",this).val().replace(/\./g,'').replace(',','.'));
        horas =parseFloat($("input[name='horas[]']",this).val().replace(/\./g,'').replace(',','.'));
        imp   =parseFloat($('td.column-imputado',this).text().replace(/\./g,'').replace(',','.'));
        // calculos
        realizado=Math.round(horas*tarifa);
        total+=realizado;
        totalhoras=totalhoras+horas;
        $("input[name='importe_calc[]']",this).val(realizado);
        $('td.column-realizado',this).html(_NFL(realizado,0));
        //alert("realizado: "+realizado+" = "+parseFloat($("input[name='importe_calc[]']",this).val().replace(/\./g,'').replace(',','.')));
    });
    $('table tfoot tr td.column-total').html(_NFL(total,0));
    $('table tfoot tr td.column-totalhoras').html(_NFL(totalhoras,2));
    // $(".botonOlga").show();
});
</script>
