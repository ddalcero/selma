<?php

Class Proyecto {

	public static function get($params) {

		// check the input params
		$rules = array(
		    'month' => 'required|integer',
		    'year' => 'required|integer',
		);

		$validation = Validator::make($params, $rules);

		if ($validation->fails()) {
		    return $validation->errors;
		}

		if ($params['clt_id']>0) {
			$whereCli=' and p.clt_id='.$params['clt_id'];
		}
		else $whereCli='';

		$query_proyecto =
			'SELECT  p.clt_id, s.spj_id, s.prj_id, ' .
			' substring(str(prj_no / 10000.0, 6, 4), 3, 4) + \'-\' + ' .
			' substring(str(spj_index / 100.0, 4, 2), 3, 4) + \' \' + spj_libelle AS spj_libelle ' .
			'FROM ' .
			' ss_projet s, projet p ' .
			'WHERE ' .
			' s.prj_id = p.prj_id ' . $whereCli .
			' and s.spj_id in ( ' .
			'  SELECT S.spj_id ' .
			'  FROM activite A, feuille_temps F, ss_projet S ' .
			'  WHERE A.fdt_id = F.fdt_id ' .
			'   AND A.tac_id != 40 AND A.tac_id != 41 ' .
			'   AND F.fdt_ok_da = 1 ' .
			'   AND A.spj_id = S.spj_id ' .
			'   AND month(fdt_mois_annee)=' . $params['month'] .
			'   AND year(fdt_mois_annee)=' . $params['year'] .
			' ) ' .
			'order by ' .
			' s.prj_id, p.clt_id ';

		$db=new OlgaConnection();
		$db->query($query_proyecto);
		return $db->all();

	}

	public static function lotes($params) {

		if ($params['clt_id']>0) {
			$whereCli=' and p.clt_id='.$params['clt_id'];
		}
		else $whereCli='';

		$query_proyecto=<<<EOT
SELECT  p.clt_id, s.spj_id, s.prj_id, 
 substring(str(prj_no / 10000.0, 6, 4), 3, 4) + '-' + 
 substring(str(spj_index / 100.0, 4, 2), 3, 4) + ' ' + spj_libelle AS spj_libelle
FROM
 ss_projet s, projet p
WHERE
 s.prj_id = p.prj_id $whereCli
 and s.spj_id in (
  SELECT S.spj_id
  FROM activite A, feuille_temps F, ss_projet S
  WHERE A.fdt_id = F.fdt_id
   AND A.tac_id != 40 AND A.tac_id != 41
   AND F.fdt_ok_da = 1
   AND A.spj_id = S.spj_id
 )
order by s.prj_id, p.clt_id 
EOT;

		$db=new OlgaConnection();
		$db->query($query_proyecto);
		return $db->all();

	}

	public static function datos($spj_id) {

		$query_proyecto=<<<EOT
SELECT p.clt_id, c.clt_nom, s.spj_id, s.prj_id, 
 substring(str(prj_no / 10000.0, 6, 4), 3, 4) + '-' + 
 substring(str(spj_index / 100.0, 4, 2), 3, 4) + ' ' + spj_libelle AS spj_libelle
FROM
 ss_projet s, projet p, client c
WHERE
 s.prj_id = p.prj_id
 and c.clt_id = p.clt_id 
 and s.spj_id = $spj_id
EOT;

		$db=new OlgaConnection();
		$db->query($query_proyecto);
		return $db->all();

	}


}