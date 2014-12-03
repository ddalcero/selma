<?php

class PerSub_Controller extends Base_Controller {

	public $restful=true;

	public function post_tarifa($persub) {
		// to-do update de persub
		$nuevo = PerSub::encuentra($persub)->first();
		if ($nuevo==null) {
			$nuevo = new PerSub();
			$nuevo->persub=$persub;
		}
		$nuevo->uf=Input::get('uf');
		$nuevo->save();

		// TO-DO: gestión de errores (no informa si hay un error en el update)

		return Response::json(array('true'));
	}

}
