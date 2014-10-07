<?php

class Group extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';

	public function users() {
		return $this->has_many_and_belongs_to('User', 'users_groups');
	}


}
