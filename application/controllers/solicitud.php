<?php

class Solicitud_Controller extends Base_Controller {

	public $restful=true;

	// detalle de una solicitud
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
			// si lo encontramos, le pasamos solo el ir del auxiliar
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

}
