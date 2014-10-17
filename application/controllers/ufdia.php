<?php

class UfDia_Controller extends Base_Controller {

	public $restful=true;

	public function get_uf($year=0,$month=0,$day=0) {
		try {
			$pdaymax=UfDia::max('pday');
			$lastuf=UfDia::where('pday','=',$pdaymax)->first();

			if ($year==0 || $month==0 || $day==0) {
				return Response::json($lastuf->uf);
			}
			$fecha=$year.'-'.$month.'-'.$day.'-';
			$uf=UfDia::where('pday','=',$fecha)->first();
			if (!$uf) $uf=$lastuf;

			return Response::json($uf->uf);
		}
		catch (Exception $e) {
			return Response::json('-1');
		}
	}
}
