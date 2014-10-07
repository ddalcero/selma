<?php

class Persub extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';
	public static $table='persub';

	public static function encuentra($persub) {
		return static::where('persub','=',$persub);
	}

}
