<?php

class Reunion extends Eloquent {

	public static $connection = 'mysql';
	public static $table = 'reuniones';

	public function user() {
		return $this->has_one('User');
	}


}
