<div id="lote_ajuste_modal" class="modal hide" data-backdrop="true" data-keyboard="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal_label">Añadir Lotes de Ajuste</h3>
    </div>
    <div class="modal-body">
        <form id="lote_ajuste_modal_form" method="POST" action="/lote/ajuste" class="form-inline">
            <input type="hidden" name="spj_id" id="spj_id" value="{{ $spj_id }}">
            <fieldset>
            <!-- Importe -->
            <div class="control-group">
              <label class="control-label" for="importe">Importe</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on">CLP $</span>
                  <input id="importe_clp" name="importe_clp" class="input-medium" type="text" required="">
                </div>
            </div>
            <!-- Importe -->
            <div class="control-group">
              <label class="control-label" for="fechaLoteAjuste">Fecha</label>
              <div class="controls">
                <input id="fechaLoteAjuste" name="fechaLoteAjuste" type="text" class="input-small" required="">
              </div>
            </div>
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-primary" data-loading-text="Guardando..." onclick="$('#lote_ajuste_modal_form').submit()">Guardar</button>
    </div>
</div>
@section('js')
@parent
$(document).ready(function() {
    $("#fechaLoteAjuste").datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        numberOfMonths: 1,
    });
});
@endsection