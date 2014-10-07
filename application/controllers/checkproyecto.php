<?php

class CheckProyecto_Controller extends Base_Controller {

	public $restful=true;

	public static function get_toggle($year,$month,$spj_id,$checked=1) {
		$prid=ChkProyecto::proyecto($spj_id,$year,$month)->first();
		if ($prid==null) {
			$prid=new ChkProyecto();
			$prid->spj_id=$spj_id;
			$prid->pyear=$year;
			$prid->pmonth=$month;
		}
		// update
		$prid->chk=$checked;
		$prid->save();
		return Response::json($prid);
	}

}

