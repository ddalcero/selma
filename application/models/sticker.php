<?php

class Sticker extends Eloquent {

	public static function spjid($spj_id) {
		return static::where('spj_id','=',$spj_id);
	}

	public function user() {
		return $this->has_one('User');
	}

}
