<?php

class Reuniones_Controller extends Base_Controller {

	public $restful=true;

	public function get_index($id) {
		
		$reuniones=Reunion::where('per_id', '=', $id)->order_by('created_at','desc')->get();

		return View::make('personal.reuniones',array(
			'reuniones'=>$reuniones,
			)
		);
	}

}
