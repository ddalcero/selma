<?php

class Tarifas_Controller extends Base_Controller {

	public $restful=true;

	public function get_index(){
		$candidatos=Candidato::order_by('created_at','desc')->paginate(20);
		return View::make('tarifas.index',array(
			'candidatos'=>$candidatos,
			'title'=>'Gestión de candidatos',
		));
	}

	public function get_new_form() {
		$salario=new Salario();
		$candidato=new Candidato();

		Asset::add('jqueryui', 'js/jquery-ui-1.10.3.custom.js','jquery');
		Asset::add('jqueryui-css','css/ui-lightness/jquery-ui-1.10.3.custom.min.css','jqueryui');
		Asset::add('jqueryui-i18n','js/jquery.ui.datepicker-es.js','jqueryui');
		Asset::add('jquery-rut','js/jquery.rut.min.js','jquery');

		return View::make('tarifas.candidato_form',array(
			'title'=>'Nuevo candidato',
			'afps'=>$salario->afps(),
			'plazos'=>$salario->plazos(),
			'candidato'=>$candidato,
			'salario'=>$salario,
			'check'=>true,
			'valor'=>3450,
		));
	}

	public function get_edit_form($id) {
		$candidato=Candidato::find($id);
		if (!$candidato) return Redirect::to_route('new_candidato');

		$salario=new Salario(json_decode($candidato->salario_json,true));

		Asset::add('jqueryui', 'js/jquery-ui-1.10.3.custom.js','jquery');
		Asset::add('jqueryui-css','css/ui-lightness/jquery-ui-1.10.3.custom.min.css','jqueryui');
		Asset::add('jqueryui-i18n','js/jquery.ui.datepicker-es.js','jqueryui');
		Asset::add('jquery-rut','js/jquery.rut.min.js','jquery');

		if ($salario->tickets_dia>0) {
			$check=true;
			$valor=$salario->tickets_dia;
		}
		else {
			$check=false;
			$valor=$salario->colacion;
		}

		return View::make('tarifas.candidato_form',array(
			'title'=>'Modificación datos candidato',
			'afps'=>$salario->afps(),
			'plazos'=>$salario->plazos(),
			'candidato'=>$candidato,
			'salario'=>$salario,
			'check'=>$check,
			'valor'=>$valor,
		));
	}

	public function get_view($id) {
		$candidato=Candidato::find($id);
		if (!$candidato) return Redirect::to_route('new_candidato');

		$salario=new Salario(json_decode($candidato->salario_json,true));

		if ($salario->tickets_dia>0) {
			$check=true;
			$valor=$salario->tickets_dia;
		}
		else {
			$check=false;
			$valor=$salario->colacion;
		}

		return View::make('tarifas.candidato_view',array(
			'title'=>'Ficha candidato: '.$candidato->nombre.' '.$candidato->apellidos,
			'afps'=>$salario->afps(),
			'plazos'=>$salario->plazos(),
			'candidato'=>$candidato,
			'salario'=>$salario,
			'check'=>$check,
			'valor'=>$valor,
		));
	}

	public function post_new() {
		// recalculo del formulario
		$cheques=Input::get('ticketsino',function(){return 0;});
		$valor=Input::get('valor');

		$variables=Input::get();
		unset($variables['ticketsino']);
		unset($variables['valor']);

		if ($cheques) {
			$variables['tickets_dia']=$valor;
		}
		else {
			$variables['colacion']=$valor;
			$variables['tickets_dia']=0;
		}
		$salario=new Salario($variables);

		$candidato=new Candidato(array(
			'genero'=>Input::get('genero'),
			'nombre'=>Input::get('nombre'),
			'apellidos'=>Input::get('apellidos'),
			'rut'=>Input::get('rut'),
			'puesto'=>Input::get('puesto'),
			'fecha'=>ViewFormat::DateToDB(Input::get('fecha')),
			'liquido'=>Input::get('liquido'),
			'salario_json'=>json_encode($salario->to_array())
		));

		$candidato->save();

		return Redirect::to_route('candidatos');

	}

	public function post_update() {
		$cheques=Input::get('ticketsino',function(){return 0;});
		$valor=Input::get('valor');

		$variables=Input::get();
		unset($variables['ticketsino']);
		unset($variables['valor']);

		if ($cheques) {
			$variables['tickets_dia']=$valor;
		}
		else {
			$variables['colacion']=$valor;
			$variables['tickets_dia']=0;
		}

		$salario=new Salario($variables);
		return View::make('tarifas.detalle',array(
			'salario'=>$salario,
		));
	}

	public function get_pdf($id) {
		setlocale(LC_ALL, 'es_ES.UTF-8');
		$candidato=Candidato::find($id);
		if (!$candidato) return Redirect::to_route('candidatos');

		$salario=new Salario(json_decode($candidato->salario_json,true));

		// Instanciation of inherited class
		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetFont('helvetica','',10);
		$pdf->Cell(145);
		$pdf->Cell(0,10,'Las Condes, '.date('d-m-Y'),0,1);
		$pdf->Cell(0,10,(($candidato->genero)?'Estimada ':'Estimado ').$candidato->nombre.',',0,1);
		$html="por la presente nos es grato enviarle nuestra propuesta de postulación para su ingreso a<b>CVTeam</b>.<br>".
			  "Esperamos que nuestra propuesta sea de su agrado y le invitamos a revisar con atención sus datos y los ".
			  "valores aquí indicados.<br>".
			  "Quedamos a su completa disposición para cualquier consulta.";
		$pdf->WriteHTML(5,$html);
		$pdf->Ln(8);
		$pdf->WriteHTML(5,"Antecedentes de postulación para el cargo de: ".$candidato->puesto);
		$pdf->Ln(8);

		$pdf->Cell(5);
		$pdf->SetFont('helvetica','B',12);
		$pdf->Cell(60,10,"Datos personales",0,1);

		$pdf->Cell(25);
		$pdf->SetFont('helvetica','I',10);
		$pdf->Cell(60,5,"Nombre y apellidos",0,0);
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell(0,5,$candidato->nombre." ".$candidato->apellidos,0,1);

		$pdf->Cell(25);
		$pdf->SetFont('helvetica','I',10);
		$pdf->Cell(60,5,"RUT");
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell(0,5,$candidato->rut,0,1);

		$pdf->Cell(25);
		$pdf->SetFont('helvetica','I',10);
		$pdf->Cell(60,5,"Fecha de ingreso prevista");
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell(0,5,date("d F Y",strtotime($candidato->fecha)),0,1);

		$pdf->Cell(25);
		$pdf->SetFont('helvetica','I',10);
		$pdf->Cell(60,5,"Salario líquido mensual");
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell(0,5,"$ ".ViewFormat::NFL($salario->liquido),0,1);

		$pdf->Cell(25);
		$pdf->SetFont('helvetica','I',10);
		$pdf->Cell(60,5,"Tipo de contrato");
		$pdf->SetFont('helvetica','B',10);
		$pdf->Cell(0,5,(($salario->plazo=='indefinido')?"Indefinido":("Plazo fijo ".$salario->duracion)),0,1);

		$pdf->Cell(5);
		$pdf->SetFont('helvetica','B',12);
		$pdf->Cell(60,10,"Estructura de remuneración",0,1);

		// TABLA Remuneraciones
		// FILA 1
		$pdf->Cell(15);
		$pdf->SetFont('helvetica','I',10);
		$pdf->Cell(74,8,"Haberes",0);
		$pdf->Cell(10);
		$pdf->Cell(74,8,"Descuentos",0,1);

		// FILA 2
		$pdf->Cell(15);
		$pdf->SetFont('helvetica','',10);
		$pdf->Cell(44,5,"Sueldo base",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->base),1,0,'R');
		$pdf->Cell(10);
		$pdf->Cell(44,5,"AFP (".$salario->afp.")",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->afp_total),1,1,'R');

		// FILA 3 - Gratificación Legal -- Salud
		$pdf->Cell(15);
		$pdf->Cell(44,5,"Gratificación Legal",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->gratificacion),1,0,'R');
		$pdf->Cell(10);
		$pdf->Cell(44,5,"Salud 7%",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->salud),1,1,'R');

		// FILA 4 - Otros no imponibles -- Cesantía (0,6%)
		$pdf->Cell(15);
		$pdf->Cell(44,5,"Otros no imponibles",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->no_imponibles),1,0,'R');
		$pdf->Cell(10);
		$pdf->Cell(44,5,"Cesantía (0,6%)",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->afp_total),1,1,'R');

		// FILA 5 - Total imponible -- Impuesto único
		$pdf->Cell(15);
		$pdf->Cell(44,5,"Total imponible",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->imponible),1,0,'R');
		$pdf->Cell(10);
		$pdf->Cell(44,5,"Impuesto único",1);
		$pdf->Cell(30,5,ViewFormat::NFL($salario->impuesto_unico),1,1,'R');

		// FILA 6 - Total Haberes -- Total Descuentos
		$pdf->SetFont('helvetica','B',11);
		$pdf->Cell(15);
		$pdf->Cell(44,8,"Total haberes",0);
		$pdf->Cell(30,8,"$ ".ViewFormat::NFL($salario->total_haberes),1,0,'R');
		$pdf->Cell(10);
		$pdf->Cell(44,8,"Total descuentos",0);
		$pdf->Cell(30,8,"$ ".ViewFormat::NFL($salario->total_descuentos),1,1,'R');

		// BENEFICIOS
		$pdf->Ln(5);
		$pdf->Cell(15);
		$pdf->SetFont('helvetica','I',10);
		$pdf->Cell(74,5,"Beneficios",0,1);

		$pdf->SetFont('helvetica','',8);
		// Tickets 
		if ($salario->tickets_dia > 0) {
			$pdf->Cell(15);
			$pdf->Cell(0,3,"- Tickets restaurants ($ ".ViewFormat::NFL($salario->tickets_dia)." diarios)",0,1);
		}

		$pdf->Cell(15);
		$pdf->Cell(0,3,"- Aguinaldos Fiestas Patrias y Navidad (proporcional a la fecha de ingreso)",0,1);
		$pdf->Cell(15);
		if ($salario->plazo=='indefinido')
			$pdf->Cell(0,3,"- Seguro Complementario de Salud",0,1);
		else
			$pdf->Cell(0,3,"- Seguro Complementario de Salud al pasar a contrato indefinido",0,1);

		$pdf->Ln(22);
		$pdf->SetFont('helvetica','',10);
		$pdf->Cell(15);
		$pdf->Cell(64,8,$candidato->nombre." ".$candidato->apellidos,'T');
		$pdf->Cell(20);
		$pdf->Cell(64,8,"Por CVTeam SpA",'T');

		// BENEFICIOS
		$pdf->Ln(10);
		$pdf->SetFont('helvetica','I',9);
		$pdf->Cell(74,8,"Notas",0,1);
		$pdf->SetFont('helvetica','',7);
		$texto=<<<EOT
 1. Por todo ingreso que se concrete a partir del día 15 de cada mes, el primer sueldo será cancelado dentro de los primeros 5 días
 del mes siguiente.<br>
2. Si el Contrato de Trabajo es inferior a 1 mes, su sueldo se cancelará proporcional a los días trabajados.<br>
3. Los montos presentados en la "Estructura de Remuneración" son valores referenciales, los que podrán variar
de acuerdo a las condiciones particulares acordados entre el trabajador y su respectiva AFP o ISAPRE. En el caso
que el Trabajador convenga un plan de salud por sobre el 7%, será de cargo exclusivo del trabajador.<br>
4. Al confirmarle su ingreso deberá presentar sus Certificados de afiliación de AFP e ISAPRE o Fonasa, Ficha 
Personal, formulario de Estructura de Remuneraciones, dentro de los dos primeros días hábiles a contar de su ingreso.
Cualquier demora por sobre el plazo anterior, CVTeam SpA no será responsable de la constitución del respectivo contrato de trabajo,
dentro del plazo estipulado por la Ley.<br>
5. Al estar contratado a Plazo Fijo es el empleador quién paga el seguro de cesantía en su totalidad. Al pasar a contrato
indefinido, el trabajador deberá pagar el 0,6% y de cargo del empleador será el 2,4% de la remuneración imponible del
trabajador, con un tope de UF 105,4.
EOT;

		$pdf->WriteHTML(3,$texto);

		$headers = array('Content-Type' => 'application/pdf');
		return Response::make($pdf->Output(), 200, $headers);		
	}
}
