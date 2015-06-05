<?php

class Main_Controller extends Base_Controller {

	/**
	 * periodos
	 * @return string
	 */
	private static function get_periodo() {
		$periodo=Session::get('sPeriodo');
		if (!$periodo) {
			$month2 = date('m');
			$year2 = date('Y');
			$month=date('n',strtotime($year2."-".$month2."-01 -1 months"));
			$year=date('Y',strtotime($year2."-".$month2."-01 -1 months"));
			$periodo=$year."/".$month;
			Session::put('sPeriodo',$periodo);
		}
		return $periodo;		
	}

	/**
	 * Filtra todas las peticiones: han de estar registrados (login via Sentry)
	 */
	public function __construct() {
		parent::__construct(); // Our layout will still be instantiated now.
		$this->filter('before', 'sentry');
		//->only('logout');
	}

	/**
	 * Main page
	 * @return mixed
	 */
	public function action_index() {
		return View::make('main.index',array(
			'title'=>'Inicio'));
	}

	/**
	 * Actualiza los valores de la UF desde SII.CL
	 * @return mixed
	 */
	public function action_updateuf() {

		require 'vendor/autoload.php';

		$max=strtotime(UfDia::max('pday'));

		$maxY=date('Y',$max);
		$lastY=strtotime($maxY.'-12-31');

		if ($max==$lastY) 
			$year=$maxY+1; 
		else 
			$year=$maxY;

		$url="http://www.sii.cl/pagina/valores/uf/uf".$year.".htm";

		try {
			$curl=new Curl;
			$contents=$curl->simple_get($url);
			//$contents = file_get_contents($url);

			libxml_use_internal_errors(true);

			$DOM = new DOMDocument();
			$DOM->loadHTML($contents);

			$tabla = $DOM->getElementsByTagName('td');
			$dia=1;
			$mes=1;
			$grabados=0;
			foreach ($tabla as $valor) {
				if (checkdate($mes, $dia, $year)) {
					$uf=Viewformat::NFFS($valor->nodeValue);
					$pday=$year.'-'.$mes.'-'.$dia;
					$pdaydate=strtotime($pday);
					if ($uf>0 && $pdaydate>$max) {
						UfDia::create(array(
							'pday'=>$pday,
							'uf'=>$uf
						));
						$grabados++;
					}
				}
				$mes++;
				if ($mes==13) {
					$mes=1;
					$dia++;
				}
			}
			Session::flash('success','Valores actualizados: '.$grabados);
		}
		catch(Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
		}
		Return Redirect::to('main');

	}

	/**
	 * @param $year
	 * @param $month
	 * @return mixed
	 */
	public function action_clientes($year,$month) {

		// chequeamos si el usuario puede modificar
		if (Sentry::user()->has_access('mod_realizado')) {
			// si puede modificar, obtenemos todos los clientes (per_id = 0)
			$per_id=0;
		}
		else {
			// si no puede modificar, obtenemos solo los clientes dónde tiene proyectos
			$per_id=Sentry::user()->get('metadata.per_id');
		}
		try {
			$clientes=Cliente::get_actividad($year,$month,$per_id);
		}
		catch(Exception $e) {
			Session::flash('error',$e->getMessage());
			Return Redirect::to('main');
		}

		return View::make('cliente.index',array(
			'clientes'=>$clientes
		));
	}

	/**
	 * facturación clientes
	 * @param $year
	 * @param $month
	 * @return mixed
	 */
	public function action_factura_clientes($year,$month) {

		try {
			$clientes=Cliente::get_facturacion($year,$month);
		}
		catch(Exception $e) {
			Session::flash('error',$e->getMessage());
			Return Redirect::to('main');
		}

		return View::make('cliente.facturacion',array(
			'clientes'=>$clientes
		));
	}


	/**
	 * gestión realizado mensual
	 * @return mixed
	 */
	public function action_realizado() {
		$periodo=self::get_periodo();
		list($year,$month)=explode('/',$periodo);

		// chequeamos si el usuario puede modificar
		if (Sentry::user()->has_access('mod_realizado')) {
			// si puede modificar, obtenemos todos los clientes (per_id = 0)
			$per_id=0;
		}
		else {
			// si no puede modificar, obtenemos solo los clientes dónde tiene proyectos
			$per_id=Sentry::user()->get('metadata.per_id');
		}
		try {
			$clientes=Cliente::get_actividad($year,$month,$per_id);
		}
		catch(Exception $e) {
			Session::flash('error',$e->getMessage());
			Return Redirect::to('main');
		}

		return View::make('main.realizado',array(
			'periodos' => Periodo::get(),
			'clientes' => $clientes,
			'title'=>'Pre-facturación OLGA',
		));
	}

	/**
	 * gestión facturación
	 * @return mixed
	 */
	public function action_facturacion() {

		// chequeamos si el usuario puede modificar
		if (Sentry::user()->has_access('facturacion_todos')) {
			// si puede modificar, obtenemos todos los clientes (per_id = 0)
			$per_id=0;
		}
		else {
			// si no puede modificar, obtenemos solo los clientes dónde tiene proyectos
			$per_id=Sentry::user()->get('metadata.per_id');
		}
		try {
			$clientes=Cliente::get_facturacion($per_id);
		}
		catch(Exception $e) {
			Session::flash('error',$e->getMessage());
			Return Redirect::to('main');
		}

		Asset::add('select2','js/select2.min.js','jquery');
		Asset::add('select2es','js/select2_locale_es.js','jquery');
		Asset::add('select2css','css/select2.css','jquery');

		return View::make('main.facturacion',array(
			'clientes' => $clientes,
			'title'=>'Facturación OLGA + Softland',
		));
	}

	/**
	 * stickers
	 * @return mixed
	 */
	public function action_stickers() {
		$periodo=self::get_periodo();
		list($year,$month)=explode('/',$periodo);

		$stickers=Sticker::order_by('created_at', 'desc')->paginate(10);

		return View::make('main.stickers',array(
			'stickers'=>$stickers,
			'title'=>'Stickers activos',
			'year'=>$year,
			'month'=>$month,
		));
	}

	/**
	 * proyectos pendientes (chk)
	 * @return mixed
	 */
	public function action_pendientes() {

		$periodo=self::get_periodo();
		list($year,$month)=explode('/',$periodo);

		// TO-DO: Obtener lista de proyectos pendientes

		return View::make('main.pendientes',array(
			'year'=>$year,
			'month'=>$month,
			'title'=>'Proyectos pendientes',
		));
	}

	/**
	 * gestión software factory
	 * @return mixed
	 */
	public function action_factory() {
		return View::make('main.factory',array(
			'title' => 'Gestión Software Factory ISBAN',
			'periodos' => Periodo::get(),
		));
	}

	/**
	 * gestión de facturas (confirmar facturación y añadir nr. docuemento)
	 * @return mixed
	 */
	public function action_gestionfacturas() {

		Asset::add('handlebars', 'js/handlebars.js','jquery');

		$pendientes=Solicitud::where_estado(0)->order_by('fecha_sol','desc')->paginate(10);

		return View::make('main.gestionfacturas',array(
			'title' => 'Gestión de facturas pendientes',
			'pendientes' => $pendientes,
		));
	}

	/**
	 * Visualiza DTEs emitidos
	 * @return mixed
	 */
	public function action_dtes() {

		$columnas_tabla = array('E_FechaEmision','C_Cliente','E_NumFact','E_TipoDTE','E_Importe');

		try {
			$emitidos=Dtes::where('E_Estado','=','V')
				->order_by('E_FechaEmision','desc')
				->paginate(10,$columnas_tabla);
		} catch (Exception $e) {
			Session::flash('error',$e->getMessage());
			Return Redirect::to('main');
		}

		return View::make('main.dtes',array(
			'title'=>'DTEs Emitidos',
			'emitidos'=>$emitidos,
		));
	}

	/**
	 * Visualiza DTEs pendientes de pago
	 * @return mixed
	 */
	public function action_dtesPendientes() {

		$columnas_tabla = array('E_FechaEmision','E_FechaVencimiento','C_Cliente','E_NumFact','E_TipoDTE','E_Importe');

		try {
			$emitidos=Dtes::where('E_Estado','=','V')
				->where_null('E_CompPago')
				->order_by('C_Cliente','asc')
				->order_by('E_FechaEmision','desc')
				->paginate(10,$columnas_tabla);
		} catch (Exception $e) {
			Session::flash('error',$e->getMessage());
			Return Redirect::to('main');
		}

		return View::make('main.dtesPendientes',array(
			'title'=>'DTEs Pendientes',
			'emitidos'=>$emitidos,
		));
	}

	public function action_dtesOlga(){

		Asset::add('jqueryui', 'js/jquery-ui-1.10.3.custom.js','jquery');
		Asset::add('jqueryui-css','css/ui-lightness/jquery-ui-1.10.3.custom.min.css','jqueryui');
		Asset::add('jqueryui-i18n','js/jquery.ui.datepicker-es.js','jqueryui');

		Asset::add('select2','js/select2.min.js','jquery');
		Asset::add('select2es','js/select2_locale_es.js','jquery');
		Asset::add('select2css','css/select2.css','jquery');

		return View::make('main.dtesolga',array(
			'title'=>'Facturación OLGA - Softland',
		));
	}

	public function action_dtesOlgaData($year,$month) {
		if ($month<1 or $month>12 or $month==null) $month=date('n');
		if ($year<1 or $year==null) $year=date('Y');
		$dtes=Dtes::emitidos($month,$year);

		// Busco facturas en OLGA
		$facturas=Factura::emitidas($month,$year);
		if ($facturas==null || $dtes==null) {
			return 'No hay facturas en el perido';
		}
		foreach ($facturas as $factura) $importe[$factura['clt_id']]=$factura['suma'];

		// Lista de clientes OLGA
		$clientes=Cliente::get();

		// Recorremos los DTES, buscamos el ID OLGA y el total
		array_walk($dtes,function(&$dt) use($year,$month,$importe) {
			$cli = Auxcli::byAux($dt['codaux']);
			// Check if we have it
			if ($cli!=null) {
				// importe en olga
				if (array_key_exists($cli->clt_id,$importe)) $dt['olga']=$importe[$cli->clt_id];
				else $dt['olga']=0;
				$dt['delta'] = $dt['neto'] - $dt['olga'];
			}
			else {
				// we don't have it - new one!
				$dt['olga']=0;
				$dt['delta']=0;
			}
		});

		return View::make('main.dtesolgadata',array(
			'dtes'=>$dtes,
			'auxiliar'=>$clientes,
			'year'=>$year,
			'month'=>$month,
		));
	}

}
