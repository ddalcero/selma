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
            <div class="span12" id="lotes">

            </div>
          </div>
<script>
$(document).ready(function(){
 $("#lClientes").change(function () {
   var clt_id=$("#lClientes").val();
   if (clt_id!='0') {
     var pUrl='/lotes/'+clt_id;
     $("#lotes").html('<br/><i class="icon-spinner icon-spin icon-large"></i> Cargando...');
     $("#lotes").load(pUrl);
   } else $("#lotes").empty();
 });
 $("#lClientes").change();
});
</script>