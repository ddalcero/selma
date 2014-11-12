<div id="lote_modal{{ $lote['lot_id'] }}" class="modal hide" data-backdrop="true" data-keyboard="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal_label">Modificar lote {{ $lote['lot_id'] }}</h3>
    </div>
    <div class="modal-body">
        <form id="lote_modal_form{{ $lote['lot_id'] }}" method="POST" action="/lote/{{ $lote['lot_id'] }}" class="form-inline">
            <input type="hidden" name="uf{{ $lote['lot_id'] }}" id="uf{{ $lote['lot_id'] }}" value="">
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
                  <input id="importe_clp{{ $lote['lot_id'] }}" name="importe_clp{{ $lote['lot_id'] }}" class="input-medium" type="text" value="{{ ViewFormat::NFL($lote['lot_montant_euro']) }}" required="">
                </div>
                <div class="input-prepend">
                  <span class="add-on">UF</span>
                  <input id="importe_uf{{ $lote['lot_id'] }}" name="importe_uf{{ $lote['lot_id'] }}" class="input-small" type="text" value="" required="">
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

function getUF{{ $lote['lot_id'] }}(fecha) { 
  var parts = fecha.split('-');
  url='/ufdia/'+parts[2]+'/'+parts[1]+'/'+parts[0];
  ajaxGet(url,function(msg){
    $("#uf{{ $lote['lot_id'] }}").val(msg);
  });
}

$(document).ready(function() {
    $("#fechaLote{{ $lote['lot_id'] }}").datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        numberOfMonths: 1,
        onSelect: function(dateText) {
          getUF{{ $lote['lot_id'] }}(dateText);
        }
    });
    getUF{{ $lote['lot_id'] }}($("#fechaLote{{ $lote['lot_id'] }}").val());

    $("#importe_uf{{ $lote['lot_id'] }}").change(function(){
      var uf=$("#uf{{ $lote['lot_id'] }}").val();
      var importe_uf=$("#importe_uf{{ $lote['lot_id'] }}").val().replace(/\./g,'').replace(',','.');
      $("#importe_clp{{ $lote['lot_id'] }}").val(_NFL(uf*importe_uf,0));
    });

    $("#importe_clp{{ $lote['lot_id'] }}").change(function(){
      var uf=$("#uf{{ $lote['lot_id'] }}").val();
      var importe_clp=$("#importe_clp{{ $lote['lot_id'] }}").val().replace(/\./g,'').replace(',','.');
      $("#importe_uf{{ $lote['lot_id'] }}").val(_NFL(importe_clp/uf,2));
    });

    $("#importe_clp{{ $lote['lot_id'] }}").change();

});
@endsection