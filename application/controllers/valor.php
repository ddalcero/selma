<?php

class Valor_Controller extends Base_Controller {

	public $restful=true;

	// detalle de los valores por el periodo indicado
	public function get_view($year,$month) {

		$valor=Valor::periodo($year,$month)->first();
		$periodo=$year.'/'.$month;
		Session::put('sPeriodo',$periodo);

		if ($valor!==null) {
			return View::make('plugins.valores_form',array(
				'valor' => $valor,
				'url' => URL::to_route('valor_detail',array($year,$month)),
				'method' => 'PUT',
			));
		} 
		else {
			return View::make('plugins.valores_form',array(
				'valor'=>new Valor,
				'url'=> URL::to_route('valor_detail',array($year,$month)),
				'method' => 'POST',
			));
		}
	}

	public function put_update($year,$month) {
		$valor=Valor::periodo($year,$month)->first();
		if ($valor!==null) {
			Valor::update($valor->id, array(
				'uf'=>Input::get('uf'),
				'pdays'=>Input::get('pdays')
			));
			$valor=Valor::periodo($year,$month)->first();
			return View::make('plugins.valores_form',array(
				'valor' => $valor,
				'url' => URL::to_route('valor_detail',array($year,$month)),
				'method' => 'PUT',
			));
		}
		// to-do: que pasa si no hay valor???
		Session::flash('error','No se ha podido actualizar el valor');
		return View::make('plugins.valores_form',array(
			'valor' => null,
			'url' => URL::to_route('valor_detail',array($year,$month)),
			'method' => 'PUT',
		));
	}

	public function post_insert($year,$month) {
		Valor::create(array(
			'pyear'=>$year,
			'pmonth'=>$month,
			'uf'=>Input::get('uf'),
			'pdays'=>Input::get('pdays')
			));
		$valor=Valor::periodo($year,$month)->first();
		return View::make('plugins.valores_form',array(
			'valor' => $valor,
			'url' => URL::to_route('valor_detail',array($year,$month)),
			'method' => 'PUT',
		));
	}


}
