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

			$tipo_factura=$lote['lot_tva']?"AFECTA":"EXENTA";

			$username=Sentry::user()->get('metadata.first_name').' '.Sentry::user()->get('metadata.last_name');
			$email=Sentry::user()->get('email');

			$vista=View::make('proyecto.factura_mail',array(
				'lote'=>$lote,
				'proyecto'=>$proyecto,
				'username'=>$username,
				'email'=>$email,
				'tipo_factura'=>$tipo_factura,
			));

			// Create the Transport
/*
			  ->setTo(array(
			  	  'facturacion@siigroup.cl'
			  	  ))
*/

			$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
			// Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);
			// Create a message
			$message = Swift_Message::newInstance('Petición de facturación')
			  ->setFrom(array('noreply@siigroup.cl' => 'SELMA'))
			  ->setTo(array(
			  	  'facturacion@siigroup.cl'
			  	  ))
			  ->setCc(array(
			  	  $email=>$username
			  	  ))
			  ->setBody($vista->render(),'text/html');
			  ;
			// Send the message
			$result = $mailer->send($message);

			if ($result) {
				Session::flash('success','Petiión enviada');
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
