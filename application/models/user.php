<?php

class User extends Eloquent {

	public static $timestamps = true;
	public static $connection = 'mysql';

	public static $hidden = array('password');

	public function metadata() {
		return $this->has_one('Metadata');
	}

	public function groups() {
		return $this->has_many_and_belongs_to('Group', 'users_groups');
	}

}
