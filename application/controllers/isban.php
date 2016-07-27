<?php

/**
 *
 */
class Isban_Controller extends Base_Controller {

	public $restful=true;

	/**
	 * Activity detail for the current month
	 */
	public static function get_detalle($year,$month) {

		$proyectos=Isban::get($year,$month);
		$total=array('dias'=>0,
					'hh'=>0,
					'clp'=>0,
					'clp_calc'=>0,
					'pend'=>0);

		if (count($proyectos)) {
			array_walk($proyectos, function(&$pro) use(&$total) {
				$pro['tarifa_hh']=$pro['tarifa']/9;
				$pro['hh']=($pro['tarifa_hh']==0)?0:($pro['importe']/$pro['tarifa_hh']);
				$total['dias']+=$pro['imputado'];
				$total['hh']+=$pro['hh'];
				$total['clp']+=$pro['importe'];
				$total['pend']+=($pro['fsi_id']>0)?0:1;
			});
		}
	
		return View::make('isban.detalle',array(
			'proyectos'=>$proyectos,
			'total'=>$total,
		));

	}

	/**
	 * Receives the post data and updates the invoicing batch (lote)
	 */
	public static function post_detalle() {
		$input=Input::get();
		$resultado="success";
		$cadena="";
		foreach ($input['lot_id'] as $key => $lot_id) {
			try {
				Lote::update($lot_id,$input['importe_calc'][$key]);
				$cadena.="Actualizado lote ".$lot_id."<br/>";
			}
			catch (Exception $e) {
				$cadena.="Error actualizando lote ".$lot_id." (".$e->getMessage().")<br/>";
				$resultado="error";
			}
		}
		Session::flash($resultado,$cadena);
		return Redirect::to("main/isban");
	}

}
