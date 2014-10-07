<?php

Class Actividad {

	public static function get($params) {

		// check the input params
		$rules = array(
		    'spj_id' => 'required|integer',
		    'month' => 'required|integer',
		    'year' => 'required|integer',
		);

		$validation = Validator::make($params, $rules);

		if ($validation->fails()) {
			throw new Exception($validation->errors);
		}

		$query_actividad =
			' SELECT act_id,fdt_mois_annee as periodo, S.spj_id, spj_libelle, prj_id,  ' .
			'  CONVERT(varchar(200), RTRIM(per_prenom) + \' \' + RTRIM(per_nom)) as consultor,  ' .
			'  act_nb_jours_sem1+ act_nb_jours_sem2+ act_nb_jours_sem3+ act_nb_jours_sem4+ ' .
			'  act_nb_jours_sem5+ act_nb_jours_sem6 as imputado,  ' .
			'  act_nb_jours_facturables as facturable,  ' .
			'  act_taux_jours as tarifa, P.per_id, ' .
			'  act_taux_jours*act_nb_jours_facturables as realizado ' .
			' FROM activite A, feuille_temps F, personnel P, ss_projet S ' .
			' WHERE A.fdt_id = F.fdt_id ' .
			'  AND F.per_id = P.per_id ' .
			'  AND A.tac_id != 40 AND A.tac_id != 41 ' .
			'  AND F.fdt_ok_da = 1 ' .
			'  AND A.spj_id = S.spj_id ' .
			'  AND S.spj_id = '. $params['spj_id'] .
			'  AND month(fdt_mois_annee) = ' . $params['month'] .
			'  AND year(fdt_mois_annee) = ' . $params['year'] .
			//'  AND act_taux_jours IS NOT NULL ' .
			' ORDER BY per_prenom,per_nom asc ';

		$db=new OlgaConnection();
		$db->query($query_actividad);
		return $db->all();

	}

	public static function update($act_id,$dias_fact,$tarifa) {
		$query="update activite set act_nb_jours_facturables=".$dias_fact.",act_taux_jours=".$tarifa." where act_id=".$act_id;
		$db=new OlgaConnection();
		return $db->update($query);
	}

	public static function get_descuentos($year,$month,$per_id,$solo_descuentos=true) {

		$filtro=($solo_descuentos)?'AND A.tac_id <> 0':'';

		$query_descuentos=<<<EOT
SELECT CONVERT(varchar(200), RTRIM(per_prenom) + ' ' + RTRIM(per_nom)) as consultor, 
CASE A.tac_id
 WHEN 0 THEN 'Proyectos'
 WHEN 1 THEN 'Comercial'
 WHEN 2 THEN 'Gestiones internas' 
 WHEN 3 THEN 'Licencia'
 WHEN 4 THEN 'Comites empresa'
 WHEN 5 THEN 'Otros'
 WHEN 6 THEN 'Sin Asignacion'
 WHEN 7 THEN 'Garantia'
 WHEN 8 THEN 'Capacitaciones'
 WHEN 9 THEN 'Vacaciones'
 WHEN 10 THEN 'Tareas administrativas'
 WHEN 11 THEN 'Reclutamiento'
 WHEN 12 THEN 'Licencia de larga duracion'
 WHEN 20 THEN 'Soporte comercial'
 WHEN 30 THEN 'Proyecto en otra filial'
 WHEN 40 THEN 'Dias adicionales'
 WHEN 41 THEN 'Dias de recuperacion'
 ELSE 'Otros'
 END as tac_libelle,
 act_nb_jours_sem1+ act_nb_jours_sem2+ act_nb_jours_sem3+ act_nb_jours_sem4+
 act_nb_jours_sem5+ act_nb_jours_sem6 as imputado
FROM activite A, feuille_temps F, personnel P, type_activite T
WHERE A.fdt_id = F.fdt_id
 AND F.per_id = P.per_id
 AND P.per_id = $per_id
 $filtro
 AND F.fdt_ok_da = 1
 AND T.tac_id = A.tac_id
 AND month(fdt_mois_annee) = $month
 AND year(fdt_mois_annee) = $year
ORDER BY fdt_mois_annee desc
EOT;

		$db=new OlgaConnection();
		$db->query($query_descuentos);
		return $db->all();

	}

}