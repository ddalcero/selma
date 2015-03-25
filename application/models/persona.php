<?php

Class Persona {

	private static $campos=array('p.per_id',
	'CONVERT(varchar(200),RTRIM(per_prenom)+\' \'+RTRIM(per_nom)) as consultor',
	'p.per_matricule as matricula',	'p.per_no_secu as ficha','p.per_activite',
	'case when (p.per_date_depart is null) then \'Si\' else \'No\' end as activo');

	private static $campos_todos=array('p.per_id',
		'CONVERT(varchar(200),RTRIM(per_prenom)+\' \'+RTRIM(per_nom)) as consultor',
		'p.per_matricule as matricula',	'p.per_no_secu as ficha',
		'p.per_date_naissance','p.per_adrs1','p.per_adrs2',
		'p.per_adrs3','p.per_cp','p.per_ville','p.per_tel','p.per_fax','p.per_email',
		'p.per_date_arrive','p.per_date_depart','p.per_activite','p.per_categorie',
		'p.per_convention','p.per_contrat','p.per_modalite','p.per_modalite_date_effet');

	private static $filter="p.per_activite!='X'";

	public static function all($filter=null) {
		return self::get(self::$campos_todos,$filter);
	}

	public static function find($per_id) {

		$query_persona ="SELECT * FROM personnel WHERE per_id=".$per_id;

		$db=new OlgaConnection();
		$db->query($query_persona);
		$persona=$db->all();

		return (is_array($persona)?$persona[0]:null);

	}

	public static function get($campos=null,$filter=null) {

		if ($campos==null) $campos=self::$campos;
		$str_campos=implode(',',$campos);

		$filter=($filter==null)?(self::$filter):($filter.' and '.self::$filter);
		$where='where '.$filter;

		$query_persona ="SELECT ".$str_campos." FROM personnel p ".$where." order by per_prenom,per_nom";

		$db=new OlgaConnection();
		$db->query($query_persona);
		return $db->all();

	}

	public static function get_id($id) {
		$query_persona="select per_id,per_libelle from vdw_d_personnel where per_id=".$id;

		$db=new OlgaConnection();
		$db->query($query_persona);
		return $db->all();
	}

	public static function get_cumple() {
		$query_cumple=<<<EOT
SELECT
      per_prenom+' '+per_nom as nombre
      ,convert(varchar,per_date_naissance,105) as birthdate
      ,day(per_date_naissance) as dia
      ,year(getdate())-year(per_date_naissance) as anyos
FROM personnel
WHERE ( (per_date_depart is null) and (month(per_date_naissance)=month(getdate())))
ORDER BY day(per_date_naissance)
EOT;

		$db=new OlgaConnection();
		$db->query($query_cumple);
		return $db->all();

	}

	public static function get_name($filter) {
		$filter=str_replace(array('\'','"','<','>','\\','(',')','?'),'',$filter);

		$sqlFilter="per_libelle like '%" . $filter . "%'";
		$query_persona="select per_id,per_libelle from vdw_d_personnel where ".$sqlFilter." order by per_libelle";

		$db=new OlgaConnection();
		$db->query($query_persona);
		return $db->all();
	}

	public static function get_actividad($per_id) {

		$query_actividad=<<<EOT
SELECT CASE A.tac_id
 WHEN 0 THEN 'Proyectos'
 WHEN 1 THEN 'Comercial'
 WHEN 2 THEN 'Gestiones internas' 
 WHEN 3 THEN 'Licencia'
 WHEN 4 THEN 'Delegado'
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
 act_nb_jours_sem5+ act_nb_jours_sem6 as imputado,
 act_taux_jours*act_nb_jours_facturables as realizado,
 fdt_mois_annee
FROM activite A, feuille_temps F, personnel P, type_activite T
WHERE A.fdt_id = F.fdt_id
 AND F.per_id = P.per_id
 AND P.per_id = $per_id
 AND F.fdt_ok_da = 1
 AND T.tac_id = A.tac_id
ORDER BY fdt_mois_annee desc
EOT;

		$db=new OlgaConnection();
		$db->query($query_actividad);
		return $db->all();

	}

}