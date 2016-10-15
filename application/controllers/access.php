<?php

class Access_Controller extends Base_Controller {

	public $restful=true;

	public function get_login() {
		return View::make('index',array(
			'title'=>'Log-in'));
	}

	public function get_register() {
		return View::make('main.register',array(
			'title'=>'Registro de un nuevo usuario'));
	}

/* No more password change/update
	public function get_password() {
		return View::make('user.password',array(
			'title'=>'Actualizar contraseña'));
	}

	public function put_password() {
		// password update
		try {
			$user=Sentry::user();

			$current=Input::get('oldpassword');
			$new=Input::get('newpassword');
			$confirm=Input::get('confirm');

			if ($new!=$confirm) {
				Session::flash('warning','La nueva contraseña no coincide.');
				return Redirect::to('password');
			}

			if ($user->change_password($new,$current)) {
				// User password was successfully updated
				Session::flash('success','Contraseña actualizada.');
			}
			else {
				// There was a problem updating the user password
				Session::flash('error','Hubo un error, la contraseña no ha sido actualizada.');
		    }
		}
		catch (Sentry\SentryException $e) {
			// There was a problem updating the user password
			Session::flash('error','Hubo un error, la contraseña no ha sido actualizada.');
		}
		return Redirect::to('main');
	}
*/

	public function post_login() {
		try {
			$valid_login = Sentry::login(Input::get('username'), Input::get('password'), true);
			if ($valid_login) {
				Return Redirect::to('main');
			}
			Session::flash('error','Usuario o contraseña incorrectos.');
			Return Redirect::to('/'); //->with_errors($errors);
		}
		catch (Exception $e) {
			Session::flash('error','Usuario o contraseña incorrectos.');
			Return Redirect::to('login'); //->with_errors($errors);
		}
	}

	public function get_logout() {
		Sentry::logout();
		return Redirect::to('/');
	}

	public function post_register() {
		$input = Input::get(); //receive input
		
		try	{
			//create some validation rules
			$rules = array(
			    'username'  => 'required|unique:users',
			    'password' => 'same:confirm'
				);
			//validate the inputs
			$validation = Validator::make($input, $rules);
			if ($validation->fails()) {
				return Redirect::to('register')->with_errors($validation);
			}
			else {
				unset($input['confirm']);			
		  		$user = Sentry::user()->create($input);
		  		if($user)
		  		{
		  			Return Redirect::to('login');
		  		}
			}
		}
		catch (Sentry\SentryException $e) {
			//create a real Laravel\Messages object from a sentry error.
			$errors = new Laravel\Messages();
			$errors->add('sentry', $e->getMessage());
			Return Redirect::to('register')->with_errors($errors);
		}

	}

}
