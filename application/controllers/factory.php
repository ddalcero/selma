<?php

class Factory_Controller extends Base_Controller {

	public $restful=true;

	// detalle stickers por subproyecto
	public function post_upload() {
		if(!Input::has_file('excel')) {
			Session::flash('warning','Fichero no anexado.');
			return Redirect::to('/main/factory');
		}

		$file=Input::file('excel');
		$mesquarter=explode('/',Input::get('lPeriodos'));
		$file_path=$file['tmp_name'];

		switch ($file['type']) {
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			case 'application/vnd.ms-excel':
			case 'application/msexcel':
			case 'application/x-msexcel':
			case 'application/x-ms-excel':
			case 'application/x-excel':
			case 'application/x-dos_ms_excel':
			case 'application/xls':
			case 'application/x-xls':
				$file_ok=true;
				break;
			default:
				Session::flash('warning','El fichero anexado no es un Excel.');
				return Redirect::to('main/factory');
		}

		// Turn XLS file into an array
		require_once 'bundles/laravel-phpexcel/PHPExcel/IOFactory.php';

		try {
			$objPHPExcel = PHPExcel_IOFactory::load($file_path);
			$rows = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

			// get the column names
			$xls_fields = isset($rows[1]) ? $rows[1] : array();
			if (! empty($xls_fields))
				unset($rows[1]);

			// xls returns $value = array('A' => 'value'); so you have to remove keys
			$fields = array();
			foreach ($xls_fields as $field) {
				$fields[] = strtolower($field);
			}
			$campos=implode(',',$fields);

			// find each column's position from available data set
			$artefacto_pos = array_search('gfpro000xxxxx', $fields);
			$tarifa_pos = array_search('tarifa por hora', $fields);
			$horas_pos = array_search('número horas', $fields);
			$importe_pos = array_search('importe total', $fields);

			// check the file, if it has all the needed columns
			if ($artefacto_pos===false || $tarifa_pos===false || $horas_pos===false) {
				Session::flash('warning','El Excel anexado no contiene las columnas requeridas (gfpro000xxxxx, tarifa por hora, número horas). -'.$campos);
				return Redirect::to('main/factory');
			}

			foreach ($rows as $row) {
				// remove keys again
				$data = array();
				foreach ($row as $key => $value) {
					$data[] = $value;
				}

				// getting data read for insertion
				$artefacto  = $data[$artefacto_pos];
				if (substr($artefacto,0,5)==='GFPRO') {
					// we hace a line, get the data
					$tarifa     = $data[$tarifa_pos];
					$horas      = $data[$horas_pos];
					$importe    = $data[$importe_pos];

					// TO-DO: insert the row into the database
					$cadena[]=array($artefacto,$tarifa,$horas,$importe);
				}
			}

			unset($rows);
			unset($objPHPExcel);

		}
		catch (Exception $e) {
			Session::flash('error','Error: '.$e->getMessage());
			return Redirect::to('main/factory');
		}

		// TO-DO: create the response with the results of the import
		return Response::json(array('Input'=>Input::get(),'archivo'=>$file,'cadena'=>$cadena,'periodo'=>$mesquarter));
	}
}
