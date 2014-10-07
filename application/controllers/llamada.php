<?php

class Llamada_Controller extends Base_Controller {

	public function action_index()
	{
		// code here..
		return View::make('llamada.index', array(
			'llamadas' => Llamada::get(array('id','fono','descripcion','anexo'))
		));
		// return Response::eloquent(Cliente::get(array('clt_id','clt_nom')));
	}

}
