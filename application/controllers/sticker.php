<?php

class Sticker_Controller extends Base_Controller {

	public $restful=true;

	// detalle stickers por subproyecto
	public function get_list($spj_id=0) {
		if ($spj_id==0) {
			$stickers=Sticker::all();
		}
		else {
			$stickers=Sticker::spjid($spj_id)->all();
		}
		return Response::json($stickers);
	}

	// detalle sticker
	public function get_view($spj_id=0) {

		$method='PUT';
		if ($spj_id==0) {
			$sticker = new Sticker;
			$method='POST';
		}
		else {
			$sticker=Sticker::spjid($spj_id)->first();
			if ($sticker==null) {
				$sticker=new Sticker;
				$sticker->spj_id=$spj_id;
				$method='POST';
				$show=0;
			}
			else $show=1;
		}
		return View::make('sticker.index',array(
			'sticker'=>$sticker,
			'method'=>$method,
			'show'=>$show,
		));
	}

	// nuevo
	public function get_new() {
		$sticker = new Sticker;
		return View::make('sticker.index',array(
			'sticker'=>$sticker
		));
	}

	// graba sticker
	public function post_new($id=0) {

		$input=Input::get();
		$usrid=Sentry::user()->get('id');

		$sticker=new Sticker;

		$sticker->spj_id = $input['spj_id'];
		$sticker->user_id = $usrid;
		$sticker->text = $input['text'];

		$sticker->save();

		return View::make('sticker.index',array(
			'sticker'=>$sticker,
			'method'=>'PUT',
			'show'=>1,
		));
	}

	// actualiza sticker
	public function put_update($spj_id) {
		$input=Input::get();
		$usrid=Sentry::user()->get('id');

		$sticker=Sticker::spjid($spj_id)->first();
		if ($sticker==null) {
			return "Sticker no encontrado.";
		}

		$sticker->spj_id = $input['spj_id'];
		$sticker->user_id = $usrid;
		$sticker->text = $input['text'];

		$sticker->save();

		return View::make('sticker.index',array(
			'sticker'=>$sticker,
			'method'=>'PUT',
			'show'=>1,
		));

	}

	// elimina sticker
	public function delete_delete($spj_id) {

		$sticker=Sticker::spjid($spj_id)->first();
		if ($sticker==null) {
			return "Sticker no encontrado.";
		}
		$sticker->delete();

		$new_sticker=new Sticker;
		$new_sticker->spj_id=$spj_id;

		return View::make('sticker.index',array(
			'sticker'=>$new_sticker,
			'method'=>'POST',
		));

	}
}
