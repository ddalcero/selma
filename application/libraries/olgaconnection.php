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
				Config::get('database.connections.so-santiago.host'),
				Config::get('database.connections.so-santiago.username'),
				Config::get('database.connections.so-santiago.password')
			);

			if (!$this->mssql) {
				throw new Exception("Ha habido un error conectando con OLGA. Por favor avisen a sistemas si el problema persiste.");
			}
			@mssql_select_db(Config::get('database.connections.so-santiago.database'),$this->mssql);

			$this->connected=true;
			$this->resultado=null;
		}
	}

	public function query($query) {
		$this->resultado = @mssql_query($query,$this->mssql);
	}

	public function result() {
		return $this->resultado;
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

	public function addlote($fecha,$monto,$spj_id) {

		$stmt=mssql_init('Crear_Lote');

		// Bind values
		mssql_bind($stmt, '@monto',  $monto,  SQLFLT8,    false);
		mssql_bind($stmt, '@fecha',  $fecha,  SQLVARCHAR, false);
		mssql_bind($stmt, '@spj_id', $spj_id, SQLFLT8,    false);

		// Execute the statement
		$exito=mssql_execute($stmt);

		// And we can free it like so:
		mssql_free_statement($stmt);

		return $exito;
	}

}