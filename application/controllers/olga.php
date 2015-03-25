<?php

class Olga_Controller extends Base_Controller {

	public $restful=true;

    /**
     * Visualiza la actividad realizada en un subproyecto para un periodo
     * TODO: tratamiento de los diferentes tipos de proyectos
     * @param $year
     * @param $month
     * @param int $spj_id
     * @return mixed View: proyecto.view_actividad
     */
    public function get_actividad_view($year,$month,$spj_id=0) {
		try {
			$valores=Valor::periodo($year,$month)->first();
			if (!$valores) {
				throw new Exception("Valores de UF y jornadas no definidos en el periodo.", 1);
			}
			$cliente=Cliente::name(Session::get('sCliente'));
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

                // Work in progress method
                $wip_method=Proyecto::getWIPMethod($spj_id);
                if ($wip_method!=null) $wipm=$wip_method[0]['hpa_meth_id'];
                else $wipm=0;

                // Es FP?
                $history=null;
                if ($wipm==5) {
                    $getHistory=Proyecto::getHistory($spj_id);
                    // girar array para highcharts
                    foreach ($getHistory as $avance) {
                        $history[ViewFormat::dateFromDB($avance['hpa_date'])]=$avance['hpa_prct_avct'];
                    }
                }

                // Tipo proyecto
                $tipo_proyecto=TipoProyecto::where('spj_id','=',$spj_id)->first();
                if ($tipo_proyecto==null) {
                    // no lo encuentro: creo nuevo y grabo, valor por defecto
                    $tipo_proyecto=new TipoProyecto();
                    $tipo_proyecto->spj_id=$spj_id;
                    $tipo_proyecto->tipoproyecto=0;
                    $tipo_proyecto->save();
                }

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

            Asset::add('highcharts-js', 'js/highcharts.js', 'jquery');

            return View::make('proyecto.view_actividad',array(
				'actividad'=>$actividad,
				'valores'=>$valores,
				'total'=>(isset($total))?$total:null,
				'title'=>'Resumen de la actividad mensual',
				'edit_link'=>$edit_link,
				'descuentos'=>$descuentos,
				'cliente'=>$cliente,
                'wipm'=>$wipm,
                'history'=>$history,
                'tipo_proyecto'=>$tipo_proyecto,
			));

			// return Response::json($actividad);
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}

	}

    /**
     * Modificación de la actividad mensual
     * @param $year
     * @param $month
     * @param $spj_id
     * @return mixed View: proyecto.edit_actividad
     */
    public function get_actividad_edit($year,$month,$spj_id) {
		try {
			// Tenemos los valores de UF y jornadas?
			$valores=Valor::periodo($year,$month)->first();
			if (!$valores) {
				throw new Exception("Valores de UF y jornadas no definidos en el periodo.", 1);
			}

			// Tipo proyecto
			$tipo_proyecto=TipoProyecto::where('spj_id','=',$spj_id)->first();
			if ($tipo_proyecto==null) {
				// no lo encuentro: creo nuevo y grabo, valor por defecto
				$tipo_proyecto=new TipoProyecto();
				$tipo_proyecto->spj_id=$spj_id;
				$tipo_proyecto->tipoproyecto=0;
				$tipo_proyecto->save();
			}

            $wip_method=Proyecto::getWIPMethod($spj_id);
            if ($wip_method!=null) $wipm=$wip_method[0]['hpa_meth_id'];
            else $wipm=0;

			// Como tratamos los proyectos
			// tipos proyectos
            // 0 'AT - En UF mensualizado',
            // 1 'AT - Tarifa en UF por día',
            // 2 'AT - Tarifa en CLP por día',
            // 3 'FP - Llave en mano en UF',
            // 4 'FP - Llave en mano en CLP',
            // 5 'LM - Mantenimiento/soporte en UF por mes'

			// El proyecto está marcado?
			$chk=ChkProyecto::proyecto($spj_id,$year,$month)->first();
			if ($chk==null) {
				$checked=0;
			}
			else {
				$checked=($chk->is_checked())?1:0;
			}

			// Obtenemos la actividad (tipo 0)
			$actividad=Actividad::get(array('spj_id'=>$spj_id,'month'=>$month,'year'=>$year));
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

			// Obtengo los lotes por el periodo
			$lotes=Lote::get_period($year,$month,$spj_id);

			// Solo aplicable a proyectos T&M
			// Revisar si se ha de modificar cuando tenemos proyectos con más de un lote en un mismo periodo
			$facturado=0;
			if (count($lotes)>0) $facturado=$lotes[0]['fsi_id'];

			// Añadir la vista
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
                'wipm'=>$wipm,
                'tipo_proyecto'=>$tipo_proyecto,
			));
			// return Response::json($actividad);
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}
	}

    /**
     * @param $year
     * @param $month
     * @param int $clt_id
     * @return mixed
     */
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

    /**
     * @param int $clt_id
     * @return mixed
     */
    public function get_proyecto_facturacion($clt_id=0) {
		try {
			Session::put('sCliente',$clt_id);

			$proyectos=Proyecto::lista(array('clt_id'=>$clt_id));

			if ($clt_id<>0)
				$nom_cliente=Cliente::name($clt_id);
			else $nom_cliente="Todos";

			// añade un elemento all'array (en este caso Link)
			if ($proyectos) {
				array_walk($proyectos, function(&$n) {
					$n['link_f'] = HTML::link_to_route('facturar_lote','Ver lotes', array($n['spj_id']));
				});
			}

			return View::make('proyecto.facturacion',array(
				'proyecto'=>$proyectos,
				'cliente'=>$nom_cliente,
			));
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}
	}

    /**
     * @return string
     */
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

    /**
     * @return string
     */
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

    /**
     * @return mixed
     */
    public function get_cliente() {
		return Response::json(Cliente::get());
	}

}
