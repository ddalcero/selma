<?php

class Facturar_Controller extends Base_Controller {

	public $restful=true;

	public static function get_correo($lot_id) {

		try {

			$lotes=Lote::getLote($lot_id);
			if (count($lotes)==1) {
				$lote=$lotes[0];
			}
			else {
				throw new Exception("Lote no encontrado");
			}
			$spj_id=$lote['spj_id'];
			$proyecto=Proyecto::datos($spj_id);

			$pdaymax=UfDia::max('pday');
			$lastuf=UfDia::where('pday','=',$pdaymax)->first();

			$ufday=date('Y-m-d',strtotime($lote['lot_fecha']));
			$uf=UfDia::where('pday','=',$ufday)->first();
			if (!$uf) $uf=$lastuf;
			$lote['lot_montant_uf']=$lote['lot_montant_euro']/$uf->uf;
			$lote['valor_uf']=$uf->uf;

			// Vemos si es factura afecta o exenta
			// Si exenta, ponemos a 0 la tasa de IVA (olga lo deja con valor)
			if ($lote['lot_tva']) {
				$tipo_factura="AFECTA";
			}
			else {
				$tipo_factura="EXENTA";
				$lote['lot_taux_tva']=0;
			}

			$username=Sentry::user()->get('metadata.first_name').' '.Sentry::user()->get('metadata.last_name');
			$email=Sentry::user()->get('email');

			// Graba la solicitud
			$user_id=Sentry::user()->get('id');

			$sol_data=array(
				'user_id'=>$user_id,
				'fecha_fac'=>$ufday,
				'clt_id'=>$proyecto[0]['clt_id'],
				'spj_id'=>$lote['spj_id'],
				'lot_id'=>$lot_id,
				'cliente'=>$proyecto[0]['clt_nom'],
				'glosa'=>$lote['lot_libelle'],
				'detalle'=>$lote['lot_libelle_fac_clt'],
				'importe_clp'=>$lote['lot_montant_euro'],
				'tasa_iva'=>$lote['lot_taux_tva'],
				'iva'=>$lote['lot_montant_euro']*$lote['lot_taux_tva']/100,
				'total'=>$lote['lot_montant_euro']*(1+($lote['lot_taux_tva']/100)),
				'valor_uf'=>$lote['valor_uf'],
				'total_uf'=>$lote['lot_montant_uf']
				);
			$solicitud=new Solicitud($sol_data);

			try {
				if ($solicitud->save()) {
					$last_id=DB::query('SELECT LAST_INSERT_ID() as id');
					$sol_id=$last_id[0]->id;
				}
				else {
					throw new Exception('error en solicitud->save en Facturar@GetCorreo');
				}
			} catch(Exception $e) {
				throw new Exception('Error grabando petición: '.$e->getMessage());
			}

			$link=HTML::link_to_route('detalle_solicitud','#'.$sol_id,array($sol_id));

			// Prepara el correo
			$vista=View::make('proyecto.factura_mail',array(
				'lote'=>$lote,
				'proyecto'=>$proyecto,
				'username'=>$username,
				'email'=>$email,
				'tipo_factura'=>$tipo_factura,
				'sol_id'=>$sol_id,
				'link'=>$link,
			));

			// Envía el correo
			$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
			// Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);
			// Create a message
/*
			  ->setTo(array(
			  	  'facturacion@siigroup.cl'
			  	  ))
*/
			$message = Swift_Message::newInstance('Petición de facturación')
			  ->setFrom(array('noreply@siigroup.cl' => 'SELMA'))
			  ->setTo(array(
			  	  $email=>$username
			  	  ))
			  ->setBody($vista->render(),'text/html');
			  ;
			// Send the message
			$result = $mailer->send($message);

			if ($result) {
				Session::flash('success','Petición enviada');
			}
			else {
				Session::flash('warning','Error enviando petición. Reenvie la petición a través de e-mail por favor.');
			}
			return Redirect::back();
		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main');
		}

	}

}
