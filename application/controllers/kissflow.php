<?php

class Kissflow_Controller extends Base_Controller {

	public $restful = true;

	public function get_index() {
		return Response::json(['status'=>'OK']);
	}

	public function post_index() {
		$response=file_get_contents('php://input');

		$input=json_decode($response,true);

		if (!$input) {
			Log::error('Bad input: ',substr($response,0,80));
			return Response::json(array('error parsing json'));
		}

		Log::info('Received Payload for process: '.$input["Process Name"]);
		Log::info('Process step: '.$input["Process Step"]);

		$kf=new Kissflow();
		$kf->process=$input["Process Name"];
		$kf->step=$input["Process Step"];
		$kf->payload=$response;
		$kf->save();

		return var_dump($input);
	}

	public function action_masters() {

		$clientes=Cliente::get();
		$csv['csv_data']='"CltId","Cliente"'.'\n';
		$i=0;
		foreach ($clientes as $id=>$cliente) {
			$csv['csv_data'].='"'.$id.'"'.',"'.$cliente.'"'.'\n';
			$i++;
			if ($i==5) break;
		}
		return ($csv['csv_data']);
	}

}
