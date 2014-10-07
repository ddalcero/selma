<?php

class Llamada extends Eloquent {

	public static $timestamps = true;
	public static $connection = 'mysql';

    public function user()
    {
         return $this->has_one('User');
    }
    
}
