<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/

// testing only - only for admins
Route::group(array('before' => 'is_admin'), function() {
	Route::get('dumpval',function(){
		$salario2=new Salario(array(
				'base'=>1155221,
			));
		$salario=new Salario(array(
				'liquido'=>Input::get('liquido'),
			));
		return View::make('tarifas.detalle',array(
				'salario'=>$salario,
				));
	});
	Route::get('feriados',function(){
		return Response::json(Feriado::get());
	});
	// Dumper Debugger
	Route::any('debugger',function(){
		$input=Input::get();
		return Response::json($input);
	});
	Route::any('curl',function(){
		return Mavenlink::Test();
	});
	Route::any('auxcli',function(){
		return Response::json(Auxcli::byClt(Input::get('clt')));
	});
});
// end testing only

// Home Page
Route::any('/', function() {
	return View::make('index');
});
Route::any('getCumple',function(){
	return View::make('plugins.cumples',array('personas'=>Persona::get_cumple()));
});

// Login
Route::get('login', array('as'=>'login','uses'=>'access@login'));
Route::get('logout', array('as'=>'logout','uses'=>'access@logout'));
Route::post('login', array('uses'=>'access@login'));

// REGISTER IS NOT IN USER ANYMORE
//Route::get('register', array('as'=>'register','uses'=>'access@register'));
//Route::post('register', array('uses'=>'access@register'));

Route::group(array('before' => 'sentry'), function() {
	Route::get('password', array('uses'=>'access@password'));
	Route::put('password', array('uses'=>'access@password'));
	// UfDia
	Route::get('ufdia/(:num?)/(:num?)/(:num?)',array('uses'=>'ufdia@uf')); 
});

// ACCESO A IS_ADMIN
Route::group(array('before' => 'is_admin'), function() {
	// USUARIOS
	Route::get('user', array('as'=>'user_list','uses'=>'user@index'));
	Route::get('user/(:num)', array('as'=>'user_detail','uses'=>'user@detail'));
	Route::get('user/new', array('as'=>'user_new','uses'=>'user@new'));
	Route::post('user/new', array('uses'=>'user@new'));
	Route::put('user/(:num)', array('as'=>'user_update','uses'=>'user@update'));
	Route::delete('user/(:num)', array('as'=>'user_delete', 'uses'=>'user@delete'));
	// GRUPOS
	Route::get('group',array('as'=>'group_list','uses'=>'group@index'));
	Route::get('group/(:num)',array('as'=>'group_detail','uses'=>'group@detail'));
	Route::get('group/new',array('as'=>'group_new','uses'=>'group@new'));
	Route::post('group/new',array('uses'=>'group@new'));
	Route::put('group/(:num)',array('uses'=>'group@update'));
});

// Acceso a REALIZADO // CON MODIFICACION
Route::group(array('before' => 'mod_realizado'), function() {
	Route::get('actividad/(:num)/(:num)/(:num)/edit', array('as'=>'actividad_edit','uses'=>'olga@actividad_edit'));
	// actividad/2014/9/1127/lote
	Route::get('actividad/(:num)/(:num)/(:num)/lote', array('as'=>'actividad_addlote','uses'=>'lote@actividad_addlote'));
});

// Acceso a REALIZADO
Route::group(array('before' => 'realizado'), function() {
	// REALIZADO - ACTIVIDAD & PROYECTOS
	Route::get('actividad/(:num)/(:num)/(:num?)', array('as'=>'actividad_view','uses'=>'olga@actividad_view'));
	Route::post('actividad/update', array('uses'=>'olga@actividad_update'));
	Route::post('actividad/lote/update', array('uses'=>'olga@actividad_lote_update'));
	Route::get('proyecto/(:num)/(:num)/(:num?)', array('as'=>'proyecto_detail','uses'=>'olga@proyecto'));
	Route::get('cliente', array('as'=>'clientes_list','uses'=>'olga@cliente'));
	// VALORES
	Route::get('valores/(:num)/(:num)', array('as'=>'valor_detail','uses'=>'valor@view'));
	Route::put('valores/(:num)/(:num)', array('uses'=>'valor@update'));
	Route::post('valores/(:num)/(:num)', array('uses'=>'valor@insert'));
	// CHECKPROYECTO
	Route::get('actividad/(:num)/(:num)/(:num)/check/(:num?)', array('as'=>'toggle_check','uses'=>'checkproyecto@toggle'));
	// PERSUB
	Route::post('persub/(:any)',array('uses'=>'persub@tarifa'));
	// STICKERS
	Route::get('sticker/(:num?)',array('uses'=>'sticker@view'));
	Route::get('sticker/new',array('uses'=>'sticker@new'));
	Route::post('sticker/(:num?)',array('uses'=>'sticker@new'));
	Route::put('sticker/(:num)',array('uses'=>'sticker@update'));
	Route::delete('sticker/(:num)',array('uses'=>'sticker@delete'));
	// FACTURACION
	Route::get('lotes/(:num)', array('as'=>'proyecto_facturacion','uses'=>'olga@proyecto_facturacion'));
	Route::get('lote/new/(:num)', array('as'=>'nuevo_lote','uses'=>'lote@add_lote'));
	Route::get('facturar/(:num)', array('as'=>'facturar_lote','uses'=>'lote@facturar_lote'));
	Route::post('lote/(:num)',array('as'=>'modificar_lote','uses'=>'lote@modificar_lote'));
	Route::delete('lote/(:num)',array('as'=>'eliminar_lote','uses'=>'lote@eliminar_lote'));
	Route::post('lote/ajuste',array('as'=>'add_lote_ajuste','uses'=>'lote@add_lote_ajuste'));
	// correo@get
	Route::get('lote/mail/(:num)', array('uses'=>'facturar@correo'));
	// Solicitudes
	Route::get('solicitud/(:num)',array('as'=>'detalle_solicitud','uses'=>'solicitud@detalle'));
	Route::post('api/auxcli',array('uses'=>'auxcli@auxcli'));
	Route::get('solicitud/dtes/(:any)',array('uses'=>'solicitud@dtes'));
});

// Gestión Facturación Software Factory
Route::group(array('before' => 'factory'), function() {
	Route::post('factory/upload',array('uses'=>'factory@upload'));
});

// Acceso a VACACIONES
Route::group(array('before' => 'mis_vacaciones'), function() {
	// VACACIONES
	Route::get('vacaciones',array('as'=>'vacaciones_index','uses'=>'vacaciones@index'));
	Route::post('vacaciones',array('uses'=>'vacaciones@new'));
	Route::post('vacaciones/dias',array('uses'=>'vacaciones@dias'));
});
// Acceso a RRHH
Route::group(array('before' => 'rrhh'), function() {
	// Ficha de personal
	Route::get('personal',array('as'=>'personal_index','uses'=>'personal@index'));
	Route::get('personal/(:num)',array('as'=>'personal_detail','uses'=>'personal@detail'));
	Route::get('reuniones/(:num)', array('as'=>'reuniones','uses'=>'reuniones@index'));
	// API obtener usuario json
	Route::get('api/personal',array('uses'=>'personal@api_name'));
	Route::get('api/personal/(:num)',array('uses'=>'personal@api_id'));
	// org chart
	Route::post('personal/(:num)/organization',array('uses'=>'personal@org_update'));
});
// Acceso a Candidatos
Route::group(array('before' => 'candidatos'), function() {
	Route::get('candidato',array('as'=>'candidatos','uses'=>'tarifas@index'));
	Route::get('candidato/new',array('as'=>'new_candidato','uses'=>'tarifas@new_form'));
	Route::get('candidato/(:num)/edit',array('as'=>'edit_candidato','uses'=>'tarifas@edit_form'));
	Route::get('candidato/(:num)',array('as'=>'view_candidato','uses'=>'tarifas@view'));
	Route::get('candidato/(:num)/pdf',array('as'=>'pdf_candidato','uses'=>'tarifas@pdf'));
	Route::post('candidato/calcular',array('uses'=>'tarifas@update'));
	Route::post('candidato/new',array('uses'=>'tarifas@new'));
});

Route::controller('main');

// OLD - not in use anymore
//Route::controller('llamada');

Route::filter('sentry', function() {
	if (!Sentry::check()) return Redirect::to('login');
});

Route::filter('realizado',function() {
	if (!Sentry::check()) return Redirect::to('login');
	if (!Sentry::user()->has_access('realizado')) return Redirect::to('main');
});

Route::filter('mod_realizado',function() {
	if (!Sentry::check()) return Redirect::to('login');
	if (!Sentry::user()->has_access('mod_realizado')) return Redirect::to('main');
});

Route::filter('is_admin',function() {
	if (!Sentry::check()) return Redirect::to('login');
	if (!Sentry::user()->has_access('is_admin')) return Redirect::to('main');
});

Route::filter('mis_vacaciones',function() {
	if (!Sentry::check()) return Redirect::to('login');
	if (!Sentry::user()->has_access('mis_vacaciones')) return Redirect::to('main');
});

Route::filter('rrhh',function() {
	if (!Sentry::check()) return Redirect::to('login');
	if (!Sentry::user()->has_access('rrhh')) return Redirect::to('main');
});

Route::filter('candidatos',function() {
	if (!Sentry::check()) return Redirect::to('login');
	if (!Sentry::user()->has_access('candidatos')) return Redirect::to('main');
});

Route::filter('factory',function() {
	if (!Sentry::check()) return Redirect::to('login');
	if (!Sentry::user()->has_access('factory')) return Redirect::to('main');
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
*/

Event::listen('404', function() {
	return Response::error('404');
});

Event::listen('500', function($exception) {
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
*/

Route::filter('before', function() {
	// Do stuff before every request to your application...
});

Route::filter('after', function($response) {
	// Do stuff after every request to your application...
});

Route::filter('csrf', function() {
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function() {
	if (Auth::guest()) return Redirect::to('login');
});

/*
|--------------------------------------------------------------------------
| Assets
|--------------------------------------------------------------------------
 */
View::composer(array('layouts/main', 'layouts/main_fluid'), function($view) {
	Asset::add('jquery', 'js/jquery-1.8.0.min.js');
	Asset::add('scripts', 'js/scripts.js', 'jquery');

//	Asset::add('handlebars', 'js/handlebars.js');
//	Asset::container('bootstrapper')->styles();
//	Asset::container('bootstrapper')->scripts();

	Asset::add('bootstrap-js', 'js/bootstrap.min.js', 'jquery');
	Asset::add('bootstrap-css', 'css/bootstrap.min.css','bootstrap-js');
	Asset::add('font-awesome', 'css/font-awesome.min.css','bootstrap-css');

	Asset::add('style', 'css/style.css');
});
