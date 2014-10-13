<?php

class Olga_Controller extends Base_Controller {

	public $restful=true;

	public function get_actividad_view($year,$month,$spj_id=0) {
		try {
			$cliente=Cliente::name(Session::get('sCliente'));
			$valores=Valor::periodo($year,$month)->first();
			if ($spj_id!=0) {
				$actividad=Actividad::get(array('spj_id'=>$spj_id,'month'=>$month,'year'=>$year));
				$edit_link=URL::to_route('actividad_edit',array($year,$month,$spj_id));
			}
			else {
				$edit_link=null;
				$actividad=null;
				$ids=explode(',',Input::get('proyectos'));
				foreach ($ids as $spj_id) {
					$act_ind=Actividad::get(array('spj_id'=>$spj_id,'month'=>$month,'year'=>$year));
					if (count($act_ind)>0) {
						if ($actividad!=null) $actividad=array_merge($actividad,$act_ind);
						else $actividad=$act_ind;
					}
				}
			}
			if ($actividad) {
				$total=array('realizado_uf'=>0,'realizado'=>0);
				$descuentos=null;
				array_walk($actividad, function(&$ar) use($valores,&$total,$year,$month,&$descuentos) {
					$ar['per-spj']=$ar['per_id'].'-'.$ar['spj_id'];

					$persub=Persub::where('persub','=',$ar['per-spj'])->first();
					if ($persub==null) $persub=new Persub;

					// carga los descuentos
					$descuento=Actividad::get_descuentos($year,$month,$ar['per_id']);
					if (count($descuento)>0) {
						if ($descuentos!=null) $descuentos=array_merge($descuentos,$descuento);
						else $descuentos=$descuento;
					}

					$ar['tarifa_uf']=$persub->uf;
					$ar['descuento']=$valores->pdays - $ar['imputado'];
					$ar['realizado']=round($ar['realizado'],0);
					$ar['valoruf']=round($ar['realizado'] / $valores->uf,2);
					$total['realizado_uf']+=$ar['valoruf'];
					$total['realizado']+=$ar['realizado'];
				});
			}

			return View::make('proyecto.view_actividad',array(
				'actividad'=>$actividad,
				'valores'=>$valores,
				'total'=>(isset($total))?$total:null,
				'title'=>'Resumen de la actividad mensual',
				'edit_link'=>$edit_link,
				'descuentos'=>$descuentos,
				'cliente'=>$cliente,
			));

			return Response::json($actividad);
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}

		// return Response::json(Actividad::get(array('spj_id'=>$spj_id,'month'=>$month,'year'=>$year)));
	}

	public function get_facturar_lote($spj_id) {
		try {

			$lotes=Lote::get($spj_id);
			$proyecto=Proyecto::datos($spj_id);
			$facturado=0;
			if (count($lotes)>0) $facturado=$lotes[0]['fsi_id'];

			$pdaymax=UfDia::max('pday');
			$lastuf=UfDia::where('pday','=',$pdaymax)->first();

			$total=array('total_uf'=>0,'total_clp'=>0);
			array_walk($lotes, function(&$lot) use($lastuf,&$total) {
				$ufday=date('Y-m-d',strtotime($lot['lot_fecha']));
				$uf=UfDia::where('pday','=',$ufday)->first();
				if (!$uf) $uf=$lastuf;
				$lot['lot_montant_uf']=$lot['lot_montant_euro']/$uf->uf;
				$lot['valor_uf']=$uf->uf;
				$total['total_clp']+=$lot['lot_montant_euro'];
				$total['total_uf']+=$lot['lot_montant_uf'];
			});

			Asset::add('jqueryui', 'js/jquery-ui-1.10.3.custom.js','jquery');
			Asset::add('jqueryui-css','css/ui-lightness/jquery-ui-1.10.3.custom.min.css','jqueryui');
			Asset::add('jqueryui-i18n','js/jquery.ui.datepicker-es.js','jqueryui');

			return View::make('proyecto.lotes',array(
				'lotes'=>$lotes,
				'proyecto'=>$proyecto,
				'spj_id'=>$spj_id,
				'total'=>$total,
			));

			return Response::json($actividad);
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}
	}

	public function get_actividad_addlote($year,$month,$spj_id) {
		try {
			// date("Y-m-t", strtotime($a_date));
			$fecha=date("t-m-Y",strtotime($year.'-'.$month.'-'.'25'));

			Lote::addlote($fecha,0,$spj_id);

			Return Redirect::to('/actividad/'.$year.'/'.$month.'/'.$spj_id.'/edit'); //->with_errors($errors);
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}
	}

	public function get_actividad_edit($year,$month,$spj_id) {
		try {
			$valores=Valor::periodo($year,$month)->first();
			$actividad=Actividad::get(array('spj_id'=>$spj_id,'month'=>$month,'year'=>$year));

			if (!$valores) {
				throw new Exception("Valores de UF y jornadas no definidos en el periodo.", 1);
			}

			if ($actividad) {
				$total=array('realizado_uf'=>0,'realizado'=>0);
				array_walk($actividad, function(&$ar) use($valores,&$total) {
					$ar['per-spj']=$ar['per_id'].'-'.$ar['spj_id'];
					$persub=Persub::where('persub','=',$ar['per-spj'])->first();
					if ($persub==null) $persub=new Persub;
					$ar['tarifa_uf']=$persub->uf;
//					$ar['descuento']=$valores->pdays - $ar['imputado'];
					$ar['realizado']=round($ar['realizado'],0);
					$ar['valoruf']=round($ar['realizado'] / $valores->uf,2);
					$total['realizado_uf']+=$ar['valoruf'];
					$total['realizado']+=$ar['realizado'];
				});
			}

			// checked
			$chk=ChkProyecto::proyecto($spj_id,$year,$month)->first();
			if ($chk==null) {
				$checked=0;
			}
			else {
				$checked=($chk->is_checked())?1:0;
			}

			$lotes=Lote::get_period($year,$month,$spj_id);
			$facturado=0;
			if (count($lotes)>0) $facturado=$lotes[0]['fsi_id'];

			Asset::add('jqueryui', 'js/jquery-ui-1.10.3.custom.js','jquery');
			Asset::add('jqueryui-css','css/ui-lightness/jquery-ui-1.10.3.custom.min.css','jqueryui');
			Asset::add('jqueryui-i18n','js/jquery.ui.datepicker-es.js','jqueryui');

			return View::make('proyecto.edit_actividad',array(
				'actividad'=>$actividad,
				'valores'=>$valores,
				'total'=>(isset($total))?$total:null,
				'title'=>'Recalcular actividad mensual',
				'view_link'=>URL::to_route('actividad_view',array($year,$month,$spj_id)),
				'check_link'=>URL::to_route('toggle_check',array($year,$month,$spj_id)),
				'checked'=>$checked,
				'lotes'=>$lotes,
				'facturado'=>$facturado,
				'spj_id'=>$spj_id,
			));

			// return Response::json($actividad);
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}
	}

	public function get_proyecto($year,$month,$clt_id=0) {
		try {
			Session::put('sCliente',$clt_id);
			Session::put('sPeriodo',$year.'/'.$month);

			$proyectos=Proyecto::get(array('month'=>$month,'year'=>$year,'clt_id'=>$clt_id));

			// añade un elemento all'array (en este caso Link)
			if ($proyectos) {
				array_walk($proyectos, function(&$n) use($year,$month) { 
					$n['chk_id'] = Form::checkbox($n['spj_id'],1,false,array('class'=>'chk_proyecto'));
					$n['link_c'] = HTML::link_to_route('actividad_edit','Calcular', array($year,$month,$n['spj_id']));
					$n['link_v'] = HTML::link_to_route('actividad_view','Visualizar', array($year,$month,$n['spj_id']));

					// checked
					$chk=ChkProyecto::proyecto($n['spj_id'],$year,$month)->first();
					if ($chk==null) {
						$n['checked']='';
					}
					else {
						$n['checked']=($chk->is_checked())?'<i class="icon-ok"></i> ':'';
					}
				});
			}

			return View::make('proyecto.index',array(
				'proyecto'=>$proyectos,
			));
			// return Response::json(Proyecto::get(array('month'=>$month,'year'=>$year,'clt_id'=>$clt_id)));
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}
	}

	public function get_proyecto_facturacion($clt_id=0) {
		try {
			Session::put('sCliente',$clt_id);

			$proyectos=Proyecto::lotes(array('clt_id'=>$clt_id));

			// añade un elemento all'array (en este caso Link)
			if ($proyectos) {
				array_walk($proyectos, function(&$n) {
					$n['link_f'] = HTML::link_to_route('facturar_lote','Ver lotes', array($n['spj_id']));

				});
			}

			return View::make('proyecto.facturacion',array(
				'proyecto'=>$proyectos,
			));
			// return Response::json(Proyecto::get(array('month'=>$month,'year'=>$year,'clt_id'=>$clt_id)));
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}

	}

	public function post_actividad_update() {
		try {
			$actividades=Input::get();
			foreach($actividades as $actividad) {
				$ok=Actividad::update($actividad['act_id'],$actividad['dias_fact'],$actividad['tarifa_dia']);
			}

			if ($ok) return Label::success(' Actividad OK ').' ';
			else return Label::warning(' Actividad no actualizada! ').' ';
		}
		catch (Exception $e) {
			return Label::warning(' Actividad no actualizada! ').Alert::error($e->getMessage());
		}
	}

	public function post_actividad_lote_update() {
		try {
			$lotes=Input::get();
			foreach($lotes as $lote) {
				$ok=Lote::update($lote['lot_id'],$lote['total']);
			}

			if ($ok) return Label::success(' Lote OK ').' ';
			else return Label::warning(' Lote no actualizado! ').' ';
		}
		catch (Exception $e) {
			return Label::warning(' Lote no actualizado! ').Alert::error($e->getMessage());
		}

	}

	public function get_cliente() {
		return Response::json(Cliente::get());
	}

	public function post_modificar_lote($lot_id) {
		$input=Input::get();

		$importe=ViewFormat::NFFS($input['importe_clp']);
		$fechaLote="fechaLote".strval($lot_id);
		$fecha=$input[$fechaLote];
		$libelle=$input['libelle'];

		Lote::update($lot_id,$importe,$fecha,$libelle);

		//return Response::json($input);
		return Redirect::to($input['backUrl']);
	}

}
