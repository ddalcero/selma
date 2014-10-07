<?php

class Vacaciones extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'bob';
	public static $table = 'softland.sw_vacsolic';

	private static $columnas = array('Ficha','FsDesde','FsHasta','Estado','NDias','FaDesde','FaHasta','NDiasAp','Observ');

	public static function solicitadas($rut) {
		return static::where('Ficha','=',$rut)
			->where('Estado','=','S')
			->get(self::$columnas);
	}

	public static function aprobadas($rut) {
		return static::where('Ficha','=',$rut)
			->where('Estado','=','A')
			->get(self::$columnas);
	}

}
