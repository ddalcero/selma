<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          <i class="icon-collapse-alt"></i> Subir Archivo
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">

		{{ Form::horizontal_open_for_files('/factory/upload','POST') }}
		<fieldset>
		<!-- File Button --> 
		<div class="control-group">
		  <label class="control-label" for="excel">Seleccionar archivo</label>
		  <div class="controls">
		    <input id="excel" name="excel" class="input-file" type="file">
		  </div>
		</div>

		<div class="control-group">
		  <label class="control-label" for="periodo">Seleccionar periodo</label>
		  <div class="controls">
		  	{{ Form::span2_select('lPeriodos', $periodos, Session::get('sPeriodo'), array('id'=>'lPeriodos')) }}
		  </div>
		</div>

		<div class="control-group">
		  <div class="controls">
		    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Enviar</button>
		  </div>
		</div>

		</fieldset>

		{{ Form::close() }}
      </div>
    </div>
  </div>
</div>  