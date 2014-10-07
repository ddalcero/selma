<?php

class Metadata extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';
	public static $table = 'users_metadata';

	public function user() {
		return $this->belongs_to('User');
	}

}
