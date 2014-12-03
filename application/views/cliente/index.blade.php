          <div class="row-fluid">
            <div class="span12" id="clientes">
              @if ($clientes==null)
              <h4>Todavía no hay actividad reportada en proyectos en este periodo.</h4>
              @else
            	{{ Form::span4_select('lClientes', $clientes, Session::get('sCliente'), array('id'=>'lClientes')) }}
              @endif
            </div>
          </div>
          <div class="row-fluid">
            <div class="span12" id="proyectos">

            </div>
          </div>
<script>
$(document).ready(function(){
 $("#lClientes").change(function () {
   var clt_id=$("#lClientes").val();
   if (clt_id!='0') {
     var pUrl='/proyecto/'+$("#lPeriodos option:selected").text()+'/'+clt_id;
     $("#proyectos").html('<br/><i class="fa fa-spinner fa-spin fa-2x"></i> Cargando...');
     $("#proyectos").load(pUrl);
   } else $("#proyectos").empty();
 });
 $("#lClientes").change();
});
</script>