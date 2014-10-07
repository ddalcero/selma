<?php

class Vacaciones_Controller extends Base_Controller {

	public $restful=true;

	// listado de vacaciones disponibles
	public function get_index() {

		// rut del usuario actual
		$rut=Sentry::user()->get('metadata.rut');

		// vacaciones solicitadas y aprobadas
		$aprobadas=Vacaciones::aprobadas($rut);
		$solicitadas=Vacaciones::solicitadas($rut);

		Asset::add('jqueryui', 'js/jquery-ui-1.10.3.custom.js','jquery');
		Asset::add('jqueryui-css','css/ui-lightness/jquery-ui-1.10.3.custom.min.css','jqueryui');
		Asset::add('jqueryui-i18n','js/jquery.ui.datepicker-es.js','jqueryui');

		return View::make('vacaciones.index',array(
			'aprobadas'=>$aprobadas,
			'solicitadas'=>$solicitadas,
		));
	}	

	// inserta una petición de vacaciones
	public function post_new() {
		$input=Input::get();
		$feriados=Feriado::get();
		$dias=SelmaUtil::getWorkingDays($input['FsDesde'],$input['FsHasta'],$feriados);

		$rut=Sentry::user()->get('metadata.rut');

		$solicitud=new Vacaciones(array(
			'Ficha'  => $rut,
			'FsDesde'=> ViewFormat::DateToDB($input['FsDesde']),
			'FsHasta'=> ViewFormat::DateToDB($input['FsHasta']),
			'NDias'  => $dias,
			'Observ' => $input['Observaciones'],
			'Estado' => 'S',
			'TraspAusentismo' => 2,
		));

		if ($solicitud->save()) {
			Session::flash('error','No se ha podido registrar su petición.');
			return Redirect::to_route('vacaciones_index');
		}
		else {
			Session::flash('success','Petición recibida.');
			return Redirect::to_route('vacaciones_index');
		}

//		return Response::json(array('input'=>$input,'dias'=>$dias));
	}

	// AJAX: recibe la fecha de inicio y de fin y devuelve el número de días laborables
	public function post_dias() {

		$input=Input::get();
		$feriados=Feriado::get();
		$dias=SelmaUtil::getWorkingDays($input['start'],$input['end'],$feriados);

		return Response::json($dias);

	}

}
