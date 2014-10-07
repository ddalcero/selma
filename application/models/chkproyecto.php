<?php

class ChkProyecto extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';
	public static $table='chkproyecto';

	public static function proyecto($spj_id,$year,$month) {
		return static::where('pyear','=',$year)
				->where('pmonth','=',$month)
				->where('spj_id','=',$spj_id);
	}

	public function is_checked() {
		return $this->chk;
	}

}
