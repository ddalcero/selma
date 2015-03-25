<?php

class TipoProyecto extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';
	public static $table='tipoproyecto';

    public static $Description = [
        'AT - En UF mensualizado',
        'AT - Tarifa en UF por día',
        'AT - Tarifa en CLP por día',
        'FP - Llave en mano en UF',
        'FP - Llave en mano en CLP',
        'LM - Mantenimiento/soporte en UF por mes'
    ];

}
