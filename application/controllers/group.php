<?php

class Group_Controller extends Base_Controller {

	public $restful=true;

	// lista de grupos
	public static function get_index() {
		return View::make('group.index',array(
			'title'=> 'Gestión de grupos',
			'groups'=> Sentry::group()->all(),
		));
	}

	public static function get_detail($id) {
		$group=Sentry::group($id);
		if ($group!==null) {
		
			// Get all users and users in the groups
			$users_all = Sentry::user()->all(array('id','username'));
			$users_in_group = $group->users(array('id','username'));

			// take the differences
			$users_not = array_diff_assoc($users_all[0],$users_in_group[0]);

			// permissions
			$all_rules=\Sentry\Sentry_Rules::fetch_rules();
			$group_permissions=json_decode($group->permissions(),true);

			$rules=null;
			if (is_array($all_rules)) {
				foreach ($all_rules as $rule) {
					$rules[$rule]=0;
					if (is_array($group_permissions) && count($group_permissions)>0) {
						foreach ($group_permissions as $permission=>$checked) {
							if ($permission==$rule) $rules[$rule]=$checked;
						}
					}
				}
			}

			// make the view
			return View::make('group.detail',array(
				'title'=>'Ficha de grupo',
				'group'=> $group,
				'users_in_group'=> $users_in_group,
				'users_not'=> $users_not,
				'rules' => $rules,
				'action_text'=>'Actualizar',
				'method'=>'PUT',
			));
		}
		else {
			return Redirect::to_route('group_list');
		}
	}

	// update grupo
	public static function put_update($id) {

		$input=Input::get();
		$rules=$input['rules'];
		foreach ($rules as $key=>$val) $permissions[$key]=($val=='1')?1:0;

		// return Response::json(array('rules'=>$rules,'permissions'=>$permissions));

		try {
			// Update current group permissions

			$group=Sentry::group((int)$id);

			$update = $group->update(array(
				'name'        => $input['name'],
				'permissions' => $permissions,
			));

			if ($update) {
				// Group was updated
				Session::flash('success','Grupo actualizado.');
			}
			else {
				// Group was not updated
				Session::flash('warning','El grupo no se ha actualizado.');
			}
		}
		catch (Sentry\SentryException $e) {
			$errors = $e->getMessage();
			Session::flash('error','Error: '.$errors);
		}

		return Redirect::to_route('group_list');
	}

	// nuevo grupo (post)
	public static function post_new() {
		$input=Input::get();
		$rules=$input['rules'];
		foreach ($rules as $key=>$val) $permissions[$key]=($val=='1')?1:0;

		if (!isset($input['name'])) {
			Session::flash('Warning','El nombre del grupo es obligatorio.');
			return Redirect::to_route('group_new');
		}
		try {
			$group_id = Sentry::group()->create(array(
				'name'        => $input['name']));

			$group=Sentry::group((int)$group_id);
			$update = $group->update(array(
				'name'        => $input['name'],
				'permissions' => $permissions,
			));
		}
		catch(Sentry\SentryException $e) { 
			$errors = $e->getMessage();
			Return Redirect::to_route('group_list')->with_errors($errors);
		}
		return Redirect::to_route('group_list');
	}

	// formulario nuevo grupo
	public static function get_new() {

		$all_rules=\Sentry\Sentry_Rules::fetch_rules();
		$rules=null;
		if (is_array($all_rules)) {
			foreach ($all_rules as $rule) {
				$rules[$rule]=0;
			}
		}

		return View::make('group.detail',array(
			'title'=>'Nuevo grupo',
			'rules'=>$rules,
			'action_text'=>'Grabar',
		));
	}


}
