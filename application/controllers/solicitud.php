<?php

class Solicitud_Controller extends Base_Controller {

	public $restful=true;

	/**
	 * detalle de una solicitud
	 * @param $sol_id
	 * @return string
	 */
	public function get_detalle($sol_id) {
		$solicitud=Solicitud::find($sol_id);
		if ($solicitud!==null) {

			$user=User::find($solicitud->user_id);
			$user_data=$user->metadata()->first(); 
			$username=$user_data->first_name.' '.$user_data->last_name;
			$email=$user->email;

			$tipo_factura=($solicitud->tasa_iva > 0)?"AFECTA":"EXENTA";

			// buscamos si ya tiene asociación de auxiliar
			$auxcli=Auxcli::byClt($solicitud->clt_id);
			// si lo encontramos, le pasamos solo el id del auxiliar
			$emitidos=null;
			if ($auxcli!==null) {
				$columnas_tabla = array('E_FechaEmision','C_Cliente','E_NumFact','E_TipoDTE','E_Importe');
				$auxcli=$auxcli->aux;
				$emitidos=Dtes::where('E_Estado','=','V')
					->where('C_IDAuxiliar','=',$auxcli)
					->order_by('E_FechaEmision','desc')
					->paginate(5,$columnas_tabla);
			}

			$auxiliares=Auxiliar::where('clacli','=','S')
				->order_by('nomaux')
				->get(array('codaux','nomaux'));

			// Convertir lista de auxiliares en array
			$auxs[0]="Selecciona un auxiliar...";
			foreach($auxiliares as $a){
				$auxs[$a->codaux] = $a->nomaux;
			}

			Asset::add('select2','js/select2.min.js','jquery');
			Asset::add('select2es','js/select2_locale_es.js','jquery');
			Asset::add('select2css','css/select2.css','jquery');

			return View::make('solicitud.detalle',array(
				'solicitud'=>$solicitud,
				'username'=>$username,
				'email'=>$email,
				'auxiliares'=>$auxs,
				'auxcli'=>$auxcli,
				'tipo_factura'=>$tipo_factura,
				'emitidos'=>$emitidos,
				));
		}
		return ('<h4>Solicitud no encontrada.</h4>');
	}

	/**
	 * Lista de dtes
	 * @param $auxcli
	 * @return mixed
	 */
	public function get_dtes($auxcli) {
		$columnas_tabla = array('E_FechaEmision','C_Cliente','E_NumFact','E_TipoDTE','E_Importe');
		$emitidos=Dtes::where('E_Estado','=','V')
			->where('C_IDAuxiliar','=',$auxcli)
			->order_by('E_FechaEmision','desc')
			->paginate(5,$columnas_tabla);
		return View::make('solicitud.dtes',array(
			'emitidos'=>$emitidos,
			));
	}

	/**
	 * elimina solicitud
	 * @param $sol_id
	 * @return mixed
	 */
	public function delete_delete($sol_id) {
		// Delete Solicitud $ID
		try {
			$so=Solicitud::find($sol_id);
			$so->delete();
			Session::flash('success','Eliminando solicitud #'.$sol_id);
		}
		catch (Exception $e) {
			Session::flash('error','Error eliminando la solicitud #'.$sol_id.': '.$e->getMessage());
		}
		return Redirect::back();
	}

	/**
	 * actualiza solicitud
	 * @param $sol_id
	 * @return mixed
	 */
	public function put_update($sol_id) {
		// Delete Solicitud $ID
		$input=Input::get();
//		return Response::json($input);
		try {
			$so=Solicitud::find($sol_id);
			$so->estado=1;
			$so->tipo_dte=$input['tipo_dte'];
			$so->nr_dte=$input['folio'];
			//$so->fecha_fac=$input['folio'];
			$so->save();
			//TO-DO Enviar Mail al solicitante
			Session::flash('success','Solicitud actualizada #'.$sol_id);
		}
		catch (Exception $e) {
			Session::flash('error','Error grabando la solicitud #'.$sol_id.': '.$e->getMessage());
		}
		//TO-DO No es un back, sino vuelve a la lista de solicitudes pendientes
		return Redirect::back();
	}

}
