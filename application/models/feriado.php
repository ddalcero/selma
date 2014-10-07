<?php

class Feriado {

	public static function get() {

		$query_feriados="SELECT jfe_date FROM ferie ORDER BY jfe_date";
		$db=new OlgaConnection();

		$db->query($query_feriados);
		while ($row=$db->fetch()) 
			$feriado[]=$row['jfe_date'];
		return (isset($feriado))?$feriado:null;

	}

}
