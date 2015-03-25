<?php

class Lote_Controller extends Base_Controller {

    public $restful=true;

    /**
     * Listado de lotes por proyecto
     * @param $spj_id
     * @return mixed View: proyecto.lotes
     */
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

            // return Response::json($actividad);
        }
        catch (Exception $e) {
            Session::flash('error','Error: '.$e->getMessage());
            return Redirect::to('main');
        }
    }

    /**
     * @param $lot_id
     * @return mixed
     */
    public function post_modificar_lote($lot_id) {
        $input=Input::get();

        $importeClp="importe_clp".strval($lot_id);
        $importe=ViewFormat::NFFS($input[$importeClp]);

        $fechaLote="fechaLote".strval($lot_id);
        $fecha=$input[$fechaLote];

        $libelleLote="libelle".strval($lot_id);
        $libelle=$input[$libelleLote];

        $libelle_fac_cltLote="libelle_fac_clt".strval($lot_id);
        $libelle_fac_clt=$input[$libelle_fac_cltLote];

        try {
            Lote::update($lot_id,$importe,$fecha,$libelle,$libelle_fac_clt);
            Session::flash('success','Lote '.$lot_id.' actualizado.');
        }
            //return Response::json($input);
        catch (Exception $e) {
            Session::flash('error','Error actualizando el lote '.$lot_id.': '.$e->getMessage());
        }
        return Redirect::back();

    }

    /**
     * @param $lot_id
     * @return mixed
     */
    public function delete_eliminar_lote($lot_id) {
        try {
            Lote::delete($lot_id);
            Session::flash('success','Eliminando lote '.$lot_id);
        }
        catch (Exception $e) {
            Session::flash('error','Error eliminando el lote '.$lot_id.': '.$e->getMessage());
        }
        return Redirect::back();
    }

    /**
     * @return mixed Redirect a la página de la petición
     */
    public function post_add_lote_ajuste() {
        $input=Input::get();

        $fecha=$input['fechaLoteAjuste'];
        $importe=ViewFormat::NFFS($input['importe_clp']);
        $spj_id=$input['spj_id'];

        try {
            Lote::addlote($fecha,$importe,$spj_id);
            Session::flash('success','Lotes creados correctamente.');
        }
        catch (Exception $e) {
            Session::flash('error','Error creando lotes de ajuste: '.$e->getMessage());
        }

        //return Response::json(array($fecha,$importe,$spj_id));
        return Redirect::back();
    }

    /**
     * Añade un lote a un sub-proyecto en la fecha actual
     * @param $spj_id
     * @return mixed
     */
    public function get_add_lote($spj_id) {
        $fecha=date('d-m-Y');
        return self::addLote($fecha,$spj_id);
    }

    /**
     * Añade un lote a un subproyecto en un periodo determinado
     * @param $year
     * @param $month
     * @param $spj_id
     * @return mixed
     */
    public function get_actividad_addlote($year,$month,$spj_id) {
        $fecha=date("t-m-Y",strtotime($year.'-'.$month.'-'.'25'));
        return self::addLote($fecha,$spj_id);
    }

    /**
     * Añade un lote - requiere fecha
     * @param $fecha
     * @param $spj_id
     * @return mixed Redirect a la página que origina la petición
     */
    private static function addLote($fecha,$spj_id) {
        try {
            Lote::addlote($fecha,0,$spj_id);
            Session::flash('success','Lote creado correctamente.');
        }
        catch (Exception $e) {
            Session::flash('error','Error creando el lote: '.$e->getMessage());
        }
        return Redirect::back();
    }

} 