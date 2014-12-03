<?php

class Auxiliar extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'bob';
	public static $table = 'softland.cwtauxi';

	private static $columnas = array('CodAux','NomAux','NoFAux','RutAux','ActAux','GirAux','ComAux',
									'CiuAux','PaiAux','ProvAux','DirAux','DirNum','FonAux1','Notas',
									'eMailDTE','esReceptorDTE');

	public static function clientes() {
		return static::where('clacli','=','S')
			->get(self::$columnas);
	}

}
