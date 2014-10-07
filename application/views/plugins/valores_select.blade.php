
<div class="row-fluid">
  <div class="span4" id="cValores">
  	{{ Form::span4_select('lPeriodos', $periodos, Session::get('sPeriodo'), array('id'=>'lPeriodos')) }}
  </div>
  <div class="span6 offset2" id="valores">

  </div>
</div>

@section('js')
@parent
{{-- <script> --}}
$(document).ready(function() {
 $("#lPeriodos").change(function () {

  var periodo=$("#lPeriodos").val();
  var cUrl='/valores/'+periodo+'/';

  $("#valores").load(cUrl);
  $("#selClientes").load('/main/clientes/'+periodo);
  //$("#lClientes").change();
 })
 .change();
})
{{-- </script> --}}
@endsection
