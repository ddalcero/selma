<div id="factura-lote" class="modal hide" data-backdrop="true" data-keyboard="true">
</div>
<script id="factura-lote-hb" type="text/x-handlebars-template">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal_label">Gestión factura</h3>
    </div>
    <div class="modal-body">

        <form id="form-factura-lote" method="POST" action="" class="form-inline">
            <input type="hidden" name="sol_id" id="sol_id" value="{{sol_id}}">
            <div id="info-solicitud">
            	<br/><i class="fa fa-spinner fa-spin fa-2x"></i> Cargando...
            </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-primary" data-loading-text="Guardando..." onclick="$('#form-factura-lote').submit()">Guardar</button>
    </div>
<!--
formulario para ingresar nr. factura

hidden id solicitud

texto:

cliente
proyecto
glosa
detalle
importe_clp
importe_uf

Tipo DTE
Nr. Factura

Check facturado

Grabar
-->
</script>
