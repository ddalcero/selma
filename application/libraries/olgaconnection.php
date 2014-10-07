<?php

Class OlgaConnection {

	private $mssql;
	private $connected;
	private $resultado;

	public function __construct() {
		if (!$this->connected) 
			$this->connect();
	}

	public function connect() {
		if (!$this->connected) {
			$this->mssql = @mssql_connect (
				'so-santiago.santiago.cvteam.cl',
				Config::get('database.connections.so-santiago.username'),
				Config::get('database.connections.so-santiago.password')
			);

			if (!$this->mssql) {
				throw new Exception("Error conectando con OLGA");
			}
			@mssql_select_db(Config::get('database.connections.so-santiago.database'),$this->mssql);

			$this->connected=true;
			$this->resultado=null;
		}
	}

	public function query($query) {
		$this->resultado = @mssql_query($query,$this->mssql);
	}

	public function all() {
		if (!$this->resultado) return null;
		while ($row = @mssql_fetch_assoc($this->resultado)) $actividad[]=$row;
		return (isset($actividad))?$actividad:null;
	}

	public function update($query) {
		return @mssql_query($query,$this->mssql);
	}

	public function fetch() {
		return @mssql_fetch_assoc($this->resultado);
	}

	public function close() {
		$this->connected=false;
		$this->resultado=null;
		@mssql_close($mssql,$this->mssql);
	}

}