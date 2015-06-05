<div class="row-fluid">
    <div class="span12">

        {{ Table::striped_bordered_hover_condensed_open(array('id'=>'dtes')) }}
        {{ Table::headers('Cliente','Importe','Importe Olga','Delta') }}
        <?php
            echo Table::body($dtes)
                    ->ignore('codaux')
                    ->neto(function($dte){return ViewFormat::NFL($dte['neto']);})
                    ->olga(function($dte) use ($auxiliar){
                        if ($dte['olga']==0 && $dte['delta']==0) {
                            // selector auxiliar
                            return  Form::span5_select($dte['codaux'],$auxiliar);
                        } else return ViewFormat::NFL($dte['olga']);
                    })
                    ->delta(function($dte){return ViewFormat::NFL($dte['delta']);})
                    ->order('nomaux','neto','olga','delta');
        ?>
        {{ Table::close() }}

    </div>
</div>
<script>
    $('select').on('change', function() {
        aux=parseInt($(this).attr("name"));
        cli=parseInt(this.value ); // or $(this).val()
        //alert ('aux='+aux+' cli='+cli);
        $.post("/api/auxcli",{aux:aux,clt_id:cli});
        //$("#dtes").load("/solicitud/dtes/"+e.added.id);
        location.reload(true);
    });
</script>