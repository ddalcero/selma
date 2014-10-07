<?php

Class ViewFormat {

	public static function NFFS($string) {
		return floatval(str_replace(",",".",str_replace(".","",$string)));
	}

	public static function NFL($number,$decimals=0) {
	    return number_format($number,$decimals,',','.');
	}

	public static function dateFromDB($fechaDB) {
	    return (isset($fechaDB))?date('d-m-Y',strtotime($fechaDB)):"";
	}

	public static function dateTimeFromDB($fechaDB) {
	    return (isset($fechaDB))?date('d-m-Y H:i',strtotime($fechaDB)):"";
	}

	public static function dateToDB($fechaNorm) {
	    return (isset($fechaNorm))?date('Y-m-d',strtotime(str_replace("/","-",$fechaNorm))):"";
	}

}
