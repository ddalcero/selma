<?php

require 'vendor/autoload.php';

class Mavenlink {

	const M_WORKSPACES = "https://api.mavenlink.com/api/v1/workspaces.json";
	const M_TIMEENTRIES = "https://api.mavenlink.com/api/v1/time_entries.json";
	const M_USERS = "https://api.mavenlink.com/api/v1/users.json";

	public static function Test() {

		$curl=new Curl;
		$curl->option(
			CURLOPT_HTTPHEADER,array(Config::get('mavenlink.oauth'))
		);

		$users=json_decode($curl->simple_get(self::M_USERS));

		$cadena="Usuarios: </br>";

		// Ciclo usuarios
		foreach ($users->users as $id=>$user) {
			$cadena.=$id." -> ".$user->full_name." </br>";
		}
		return $cadena;

	}

}