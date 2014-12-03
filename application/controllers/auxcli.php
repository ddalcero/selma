<?php

class Auxcli_Controller extends Base_Controller {

	public $restful=true;

	public function post_auxcli() {
		// to-do update de persub
		$aux=Input::get('aux');
		$clt_id=Input::get('clt_id');
		$nuevo = Auxcli::byClt($clt_id);
		if ($nuevo==null) {
			$nuevo = new Auxcli();
		}
		$nuevo->aux=$aux;
		$nuevo->clt_id=$clt_id;
		$res=$nuevo->save();

		// TO-DO: gestión de errores (no informa si hay un error en el update)

		return Response::json($res);
	}

}
