<?php

class Kissflow_Controller extends Base_Controller {

	public $restful = true;

	public function get_index() {
		return Response::json(['status'=>'OK']);
	}

	public function post_index() {
		$response=file_get_contents('php://input');
		$input=json_decode($response,true);
		Log::info('Payload: '.$response);

		if (!$input) {
			Log::error('Bad input: ',substr($response,0,80));
			return Response::json(array('error parsing json'));
		}

		switch($input['Process Name']) {
			case 'Travel Claim':
				return Response::json(array('Travel Claim'));
				break;
			default:
				return Response::json(array('Nothing to do'));
		}
//		Log::info(print_r($input,true));
		return Response::json($input);
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
