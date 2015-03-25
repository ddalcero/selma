<?php

class Personal_Controller extends Base_Controller {

	public $restful=true;

    /**
     * Listado de personas
     * @return mixed
     */
    public function get_index() {

		Asset::add('datatables','js/jquery.dataTables.min.js','jquery');
		Asset::add('css_datatables','css/DT_bootstrap.css','datatables');

		Asset::add('datatablesCF','js/jquery.dataTables.columnFilter.js','datatables');
//		Asset::add('datatablesDT','js/DT_bootstrap.js','datatables');

		$personal=Persona::get();
		return View::make('personal.index',array(
			'personal'=>$personal,
			'title'=>'Gestión del personal',
		));

	}

    /**
     * api JSON -- /api/personal?filter= $filter
     * TODO: revisar seguridad
     * @return mixed
     */
    public function get_api_name() {
		$filter=Input::get('filter');
		return Response::json(Persona::get_name($filter));
	}

    /**
     * api JSON -- /api/personal?filter= $filter
     * @param $id
     * @return mixed
     */
    public function get_api_id($id) {
		return Response::json(Persona::get_id($id));
	}

    /**
     * @param $id
     * @return mixed
     */
    public function post_org_update($id) {
		// id -> parent
		// delete from orgchart where id=$id
		// delete from orgchart where parent=$id
		// insert into orgchart (id,parent) valus (id,parent)
		// foreach bossof as persona
		//  -> delete from orgchar where id=persona->per_id
		//  -> insert into orgchart (id,parent) values (persona->per_id,id)

		$parent=Input::get('parent');
		$bossof=Input::get('bossof');

		try {

            // delete parent and child relationships
			DB::table('orgchart')->where('id', '=', $id)->delete();
			DB::table('orgchart')->where('parent', '=', $id)->delete();

			// has a boss
			if (isset($parent)&&$parent>0) {
				OrgChart::create(array(
					'id'=>$id,
					'parent'=>$parent
				));
			}

			// has subordinates
			if (isset($bossof)&&count($bossof)>0) {
				foreach ($bossof as $persona) {
					OrgChart::create(array(
						'id'=>$persona,
						'parent'=>$id
					));
				}
			}
		}
		catch (Exception $e) {
			$errors = $e->getMessage();
			return Response::json(array('errors'=>$errors));
		}

		return Response::json(array(
			'id'=>$id,
			'input'=>Input::get(),
		));
	}

    /**
     * Ficha de persona
     * @param $per_id
     * @return mixed
     */
    public function get_detail($per_id) {
		$persona=Persona::find($per_id);
		$actividad=Persona::get_actividad($per_id);

		$actividad_resumen = array();
		$actividad_total=0;
		$realizado=0;
		$periodos=null;
		$resumen=null;
		$series=null;

		if (isset($actividad) && count($actividad)>0) {
			foreach($actividad as $item) {
				if (!isset($actividad_resumen[$item["tac_libelle"]])) $actividad_resumen[$item["tac_libelle"]] = 0;
				$actividad_resumen[$item["tac_libelle"]] += $item["imputado"];
				$realizado+=$item["realizado"];
				$actividad_total+=$item["imputado"];
				$periodo[]=$item["fdt_mois_annee"];
				$actividad_mes[$item["fdt_mois_annee"]][$item["tac_libelle"]]=$item["imputado"];
			}
			foreach($actividad_resumen as $key=>$val) $resumen[]=array($key,$val);
			// obtiene las series
			$x=0;
			foreach($actividad_mes as $reporte) {
				foreach ($reporte as $clase=>$dias) {
					$serie[$clase][$x]=$dias;
				}
				$x++;
			}

			// las normaliza con valores en 0
			$clases=array_keys($serie);
			foreach ($clases as $id=>$clase) {
				$series[$id]['name']=$clase;
			}
			for($i=0;$i<$x;$i++) {
				foreach ($clases as $id=>$clase) {
					if (!isset($serie[$clase][$i])) $series[$id]['data'][$i]=null;
					else $series[$id]['data'][$i]=floatval($serie[$clase][$i]);
				}
			}

			// periodos - x axis
			$periodo=array_unique($periodo,SORT_REGULAR);
			foreach ($periodo as $mes) $meses[]=ViewFormat::dateFromDB($mes);
			$periodos=json_encode(array_values($meses));
		}

		$nombre=$persona['per_prenom'].' '.$persona['per_nom'];

		Asset::add('select2','js/select2.min.js','jquery');
		Asset::add('select2es','js/select2_locale_es.js','jquery');
		Asset::add('select2css','css/select2.css','jquery');
		Asset::add('highcharts-js', 'js/highcharts.js', 'jquery');

		$with_boss=OrgChart::where('parent','<>',$per_id)->get('id');
		if (isset($with_boss)&&count($with_boss)>0) {
			foreach ($with_boss as $elem) {
				$filtrar[]=$elem->id;
			}
			$where='per_date_depart is null';
			// and per_id not in ('.implode(',',$filtrar).')';
		}
		else $where=null;

		$lista_personal=Persona::get(array('per_id','per_prenom+\' \'+per_nom as per_libelle'),$where);
		$select_personal[0]="-";
		foreach ($lista_personal as $personal) $select_personal[$personal['per_id']]=$personal['per_libelle'];

		$boss=OrgChart::find($per_id);
		// $boss_person=Persona::find($boss->parent);
		// $boss_name=(isset($boss_person))?($boss_person['per_prenom'].' '.$boss_person['per_nom']):null;

		$descendants=OrgChart::where('parent','=',$per_id)->get('id');
		if (isset($descendants)&&count($descendants)>0)
			foreach ($descendants as $id) $bossof[]=$id->id;
		else $bossof=null;

		return View::make('personal.ficha',array(
			'title'=>'Ficha de '.$nombre,
			'persona'=>$persona,
			'personal'=>$select_personal,
			'boss'=>(isset($boss->parent)?$boss->parent:null),
			'descendants'=>$bossof,
			'actividad'=>$actividad,
			'periodo'=>$periodos,
			'resumen'=>json_encode($resumen),
			'series'=>json_encode($series),
			'actividad_total'=>$actividad_total,
			'realizado'=>ViewFormat::NFL($realizado),
		));
	}

}
