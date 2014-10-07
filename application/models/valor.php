<?php

class Valor extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';

	public static function periodo($year,$month) {
		return static::where('pyear','=',$year)
				->where('pmonth','=',$month);
	}

}
