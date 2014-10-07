<?php

Class Periodo {
	
	public static function get() {
		$year = date('Y');
		$month = date('m');
		$actual = date('Y/n');
		$i=0;

		// Toma 11 periodos anteriores
		for ($i=-13;$i<=-2;$i++) {
			$cadena_fecha=$year."-".$month."-01 ".$i." months";
			$fecha=date("Y/n",strtotime($cadena_fecha));
			$periodo[$fecha]=$fecha;
		}
/*
		$previous2 =  date("Y/n",strtotime($year."-".$month."-01 -3 months"));
		$previous1 =  date("Y/n",strtotime($year."-".$month."-01 -2 months"));
		$periodo[$previous2]=$previous2;
		$periodo[$previous1]=$previous1;
*/
		$actual = date("Y/n",strtotime($year."-".$month."-01 -1 months"));
		$next =  date('Y/n');

		$periodo[$actual]=$actual;
		$periodo[$next]=$next;

		return $periodo;

	}

}