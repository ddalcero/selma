<div id="lote_modal{{ $lote['lot_id'] }}" class="modal hide" data-backdrop="true" data-keyboard="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal_label">Modificar lote {{ $lote['lot_id'] }}</h3>
    </div>
    <div class="modal-body">
        <form id="lote_modal_form{{ $lote['lot_id'] }}" method="POST" action="/lote/{{ $lote['lot_id'] }}" class="form-inline">
            <input type="hidden" name="backUrl" id="backUrl" value="{{ URI::current() }}">
            <input type="hidden" name="uf" id="uf" value="">
            <fieldset>
            <!-- Nombre lote -->
            <div class="control-group">
              <label class="control-label" for="libelle">Nombre lote</label>
              <div class="controls">
                <input id="libelle" name="libelle" type="text" class="span9" required="true" value="{{ $lote['lot_libelle'] }}" required="">
              </div>
            </div>
            <!-- Importe -->
            <div class="control-group">
              <label class="control-label" for="importe">Importe</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on">CLP $</span>
                  <input id="importe_clp" name="importe_clp" class="input-medium" type="text" value="{{ ViewFormat::NFL($lote['lot_montant_euro']) }}" required="">
                </div>
                <div class="input-prepend">
                  <span class="add-on">UF</span>
                  <input id="importe_uf" name="importe_uf" class="input-small" type="text" value="" required="">
                </div>
              </div>
            </div>
            <!-- Importe -->
            <div class="control-group">
              <label class="control-label" for="fechaLote{{ $lote['lot_id'] }}">Fecha</label>
              <div class="controls">
                <input id="fechaLote{{ $lote['lot_id'] }}" name="fechaLote{{ $lote['lot_id'] }}" type="text" class="input-small" required="true" value="{{ ViewFormat::DateFromDB($lote['lot_date_previ_fac']) }}" required="">
              </div>
            </div>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-primary" data-loading-text="Guardando..." onclick="$('#lote_modal_form{{ $lote['lot_id'] }}').submit()">Guardar</button>
    </div>
</div>
@section('js')
@parent

function getUF(fecha) { 
  var parts = fecha.split('-');
  url='/ufdia/'+parts[2]+'/'+parts[1]+'/'+parts[0];
  ajaxGet(url,function(msg){
    $("#lote_modal_form{{ $lote['lot_id'] }} input[name=uf]").val(msg);
  });
}

$(document).ready(function() {
    $("#fechaLote{{ $lote['lot_id'] }}").datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(dateText) {
          getUF(dateText);
        }
    });
    getUF($("#fechaLote{{ $lote['lot_id'] }}").val());

    $("#lote_modal_form{{ $lote['lot_id'] }} input[name=importe_uf]").change(function(){
      var uf=$("#lote_modal_form{{ $lote['lot_id'] }} input[name=uf]").val();
      var importe_uf=$("#lote_modal_form{{ $lote['lot_id'] }} input[name=importe_uf]").val().replace(/\./g,'').replace(',','.');
      $("#lote_modal_form{{ $lote['lot_id'] }} input[name=importe_clp]").val(_NFL(uf*importe_uf,0));
    });

    $("#lote_modal_form{{ $lote['lot_id'] }} input[name=importe_clp]").change(function(){
      var uf=$("#lote_modal_form{{ $lote['lot_id'] }} input[name=uf]").val();
      var importe_clp=$("#lote_modal_form{{ $lote['lot_id'] }} input[name=importe_clp]").val().replace(/\./g,'').replace(',','.');
      $("#lote_modal_form{{ $lote['lot_id'] }} input[name=importe_uf]").val(_NFL(importe_clp/uf,2));
    });

    $("#lote_modal_form{{ $lote['lot_id'] }} input[name=importe_clp]").change();


});
@endsection