<?php

class Auxcli extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';
	public static $table='auxcli';

	// columnas
	// ctl_id = ID OLGA, int
	// aux = RUT Softland, varchar(10)

	public static function byClt($clt_id) {
		return static::where('clt_id','=',$clt_id)
			->first();
	}

	public static function byAux($aux) {
		return static::where('aux','=',$aux)
			->first();
	}

}
