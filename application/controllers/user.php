<?php

class User_Controller extends Base_Controller {

	public $restful=true;

	// detalle de los valores por el periodo indicado
	public static function get_index() {

		Asset::add('datatables','js/jquery.dataTables.min.js','jquery');
		Asset::add('css_datatables','css/DT_bootstrap.css','datatables');

		Asset::add('datatablesCF','js/jquery.dataTables.columnFilter.js','datatables');

		return View::make('user.index',array(
			'title'=> 'Gestión de usuarios',
			'users'=> Sentry::user()->all(),
		));
	}

	public static function get_detail($id) {
		$user=User::find($id);
		if ($user!==null) {
			$groups=null;
			$metadata=$user->metadata()->first();
			$user_groups=$user->groups()->get();
			$grupos=Group::all();

			// el usuario tiene grupos
			foreach ($grupos as $grupo) {
				$groups[$grupo->id]['name']=$grupo->name;
				$groups[$grupo->id]['checked']=false;
				if (count($user_groups)>0) {
					foreach ($user_groups as $usrgr) {
						if ($usrgr->id==$grupo->id) $groups[$grupo->id]['checked']=true;
					}
				}
			}

			$lista_personal=Persona::get(array('per_id','per_prenom+\' \'+per_nom as per_libelle'));
			$select_personal[0]="-";
			foreach ($lista_personal as $personal) $select_personal[$personal['per_id']]=$personal['per_libelle'];

			Asset::add('select2','js/select2.min.js','jquery');
			Asset::add('select2es','js/select2_locale_es.js','jquery');
			Asset::add('select2css','css/select2.css','jquery');

			return View::make('user.detail',array(
				'title'=>'Ficha de usuario',
				'user'=> $user,
				'metadata' => $metadata,
				'personal' => $select_personal,
				'groups' => $groups,
				'method'=>'PUT',
				'action_text'=>'Actualizar',
			));
		}
		else {
			Return Redirect::to_route('user_list');
		}
	}

	public static function get_new() {

		$grupos=Group::all();

		foreach ($grupos as $grupo) {
			$groups[$grupo->id]['name']=$grupo->name;
			$groups[$grupo->id]['checked']=false;
		}

		$lista_personal=Persona::get(array('per_id','per_prenom+\' \'+per_nom as per_libelle'));
		$select_personal[0]="-";
		foreach ($lista_personal as $personal) $select_personal[$personal['per_id']]=$personal['per_libelle'];

		Asset::add('select2','js/select2.min.js','jquery');
		Asset::add('select2es','js/select2_locale_es.js','jquery');
		Asset::add('select2css','css/select2.css','jquery');

		return View::make('user.detail',array(
			'title'=>'Nuevo usuario',
			'user'=> new User,
			'metadata' => new Metadata,
			'personal' => $select_personal,
			'groups' => $groups,
			'action_text'=>'Grabar',
		));
	}

	public static function put_update_password($id) {

		$input=Input::get();

		try {

			$rules = array(
				'password' => 'same:confirm'
			);

			$validation = Validator::make($input, $rules);
			if ($validation->fails()) {
				Session::flash('warning',$validation->errors);
				return Redirect::to('/');
			}

			$user = Sentry::user((int)$id);

			// Change the user password
			if ($user->change_password($input['password'], $input['oldpassword'])) {
				// User password was successfully updated
				Session::flash('success','Contraseña actualizada.');
				Return Redirect::to('/');
			}
			else {
				// There was a problem updating the user password
				Session::flash('error','No se ha podido actualizar la contraseña.');
				Return Redirect::to('/');
			}

		}
		catch (Sentry\SentryException $e) {
			$errors = $e->getMessage();
			Session::flash('error','Error: '.$errors.'; ID: '.$id);
			Return Redirect::to('/');
		}
	}

	public static function delete_delete($id) {

		try {
			// Find the user using the user id
			$user = Sentry::user((int)$id);

			// Delete the user
			if ($user->delete()) {
				// User was successfully deleted
				Session::flash('success','Usuario eliminado.');
				Return Redirect::to_route('user_list');
			}
			else {
				// There was a problem deleting the user
				Session::flash('error','El usuario no se pudo eliminar.');
				Return Redirect::to_route('user_list');
			}
		}
		catch (Sentry\SentryException $e) {
			$errors = $e->getMessage();
			Session::flash('error','Error: '.$errors.'; ID: '.$id);
			Return Redirect::to_route('user_list');
		}
	}

	public static function put_update($id) {

		$input=Input::get();
		unset($input['_method']);

		// return Response::json($input);
		try {
			// Find the user using the user id
			$user = Sentry::user((int)$id);

			if ($input['password']==='' && $input['confirm']==='') {
				$user_data = array(
					'metadata' => array(
						'first_name' => $input['first_name'],
						'last_name'  => $input['last_name'],
						'rut' => $input['rut'],
						'per_id'=> $input['per_id'],
					),
				);
			}
			else {
				$user_data = array(
					'password' => $input['password'],
					'metadata' => array(
						'first_name' => $input['first_name'],
						'last_name'  => $input['last_name'],
						'rut' => $input['rut'],
						'per_id'=> $input['per_id'],
					),
				);
			}

			// Prepare the user data to be updated
			if ($user->update($user_data)) {
				Session::flash('success','Datos actualizados.');
				// groups
				// first: remove the association from the pivot table where the user id match
				// then: add the groups
/*
				$group_list=Sentry::group()->all();
				foreach ($group_list as $group_det) {
					if ($user->in_group($group_det['id'])) $user->remove_from_group($group_det['id']);
				}
				if (isset($input['group'])) {
					foreach($input['group'] as $grupo) {
						if (!$user->in_group($grupo)) $user->add_to_group($grupo);
					}
				}
*/
				$groups=$input['group'];
				static::update_groups($user,$groups);

				Return Redirect::to_route('user_list');
			}
			else {
				Session::flash('error','Datos NO actualizados.');
				Return Redirect::to_route('user_list');
			}
		}
		catch (Sentry\SentryException $e) {
			$errors = $e->getMessage();
			Session::flash('error','Error: '.$errors.'; ID: '.$id);
			Return Redirect::to_route('user_list');
		}

	}

	public static function post_new() {

		$input=Input::get();

		try {
			$rules = array(
				'email'  => 'required|email|unique:users',
				'password' => 'same:confirm',
			);

			// Find the user using the user id
			$validation = Validator::make($input, $rules);
			if ($validation->fails()) {
				Session::flash('error',$validation->messages());
				return Redirect::to_route('user_new');
			}
			else {
				$user_data = array(
					'password' => $input['password'],
					'email' => $input['email'],
					'metadata' => array(
					'first_name' => $input['first_name'],
					'last_name'  => $input['last_name'],
					'rut' => $input['rut'],
					'per_id'=> $input['per_id'],
					),
				);

				if ($user_id = Sentry::user()->create($user_data)) {

					$user=Sentry::user((int)$user_id);
					if (isset($input['group'])) {
						$groups=$input['group'];
						static::update_groups($user,$groups);
					}

					Session::flash('success','Usuario creado.');
					Return Redirect::to_route('user_list');
				}
				else {
					Session::flash('error','Usuario no creado.');
					Return Redirect::to_route('user_list');
				}
			}
		}
		catch (Sentry\SentryException $e) {
			$errors = $e->getMessage();
			Return Redirect::to_route('user_list')->with_errors($errors);
		}
	}

	private static function update_groups($user,$groups) {
		// groups
		// first: remove the association from the pivot table where the user id match
		// then: add the groups
		$group_list=Sentry::group()->all();
		foreach ($group_list as $group_det) {
			if ($user->in_group($group_det['id'])) $user->remove_from_group($group_det['id']);
		}
		if (isset($groups)) {
			foreach($groups as $grupo) {
				if (!$user->in_group($grupo)) $user->add_to_group($grupo);
			}
		}
	}
}
