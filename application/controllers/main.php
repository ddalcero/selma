<?php

class Main_Controller extends Base_Controller {

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

	// apply a filter using a constructor
    public function __construct() {
        parent::__construct(); // Our layout will still be instantiated now.
        $this->filter('before', 'sentry');
		//->only('logout');
    }

	public function action_index() {
		return View::make('main.index',array(
			'title'=>'Inicio'));
	}

	public function action_updateuf() {

		$max=strtotime(UfDia::max('pday'));

		$maxY=date('Y',$max);
		$lastY=strtotime($maxY.'-12-31');

		if ($max==$lastY) 
			$year=$maxY+1; 
		else 
			$year=$maxY;

		$url="http://www.sii.cl/pagina/valores/uf/uf".$year.".htm";
		$contents = file_get_contents($url);
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
		Return Redirect::to('main');

	}

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

		return View::make('main.facturacion',array(
			'clientes' => $clientes,
			'title'=>'Facturación OLGA + Softland',
		));
	}

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

	public function action_factory() {
		return View::make('main.factory',array(
			'title' => 'Gestión Software Factory ISBAN',
			'periodos' => Periodo::get(),
		));
	}

}
