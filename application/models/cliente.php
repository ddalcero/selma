<?php

class Cliente {

	public static function name($clt_id) {
		$query_nombre = 'SELECT TOP 1 clt_nom from client where clt_id='.$clt_id;
		$name=[];
		$db=new OlgaConnection();
		$db->query($query_nombre);
		while ($row=$db->fetch()) 
			$name=$row['clt_nom'];
		return $name;
	}

	public static function get() {
		$query_cliente = 'SELECT clt_id,clt_nom from client order by clt_nom' ;
		$cliente=[];
		$db=new OlgaConnection();
		$db->query($query_cliente);
		while ($row=$db->fetch()) 
			$cliente[$row['clt_id']]=$row['clt_nom'];
		return $cliente;
	}

	public static function get_actividad($year,$month,$per_id=0) {

		if ($per_id!=0) $filtro_comercial="AND (S.per_id_com=$per_id or S.per_id_cdp=$per_id)";
		else $filtro_comercial="";

		$query_cliente=<<<EOT
select clt_id,clt_nom from client where clt_id in
(SELECT distinct(p.clt_id)
FROM 
 ss_projet s, projet p 
WHERE 
 s.prj_id = p.prj_id
 and s.spj_id in ( 
  SELECT S.spj_id 
  FROM activite A, feuille_temps F, ss_projet S 
  WHERE A.fdt_id = F.fdt_id 
   AND A.tac_id != 40 AND A.tac_id != 41 
   AND F.fdt_ok_da = 1 
   AND A.spj_id = S.spj_id 
   AND month(fdt_mois_annee)= $month
   AND year(fdt_mois_annee)= $year
   $filtro_comercial
)) order by clt_nom
EOT;

		$db=new OlgaConnection();
		$db->query($query_cliente);
		$cliente[0]='Seleccione un cliente...';
		while ($row=$db->fetch()) 
			$cliente[$row['clt_id']]=$row['clt_nom'];
		return (count($cliente)>1)?$cliente:null;

	}

	public static function get_facturacion($per_id=0) {

		if ($per_id!=0) $filtro_comercial="and (S.per_id_com=$per_id or S.per_id_cdp=$per_id)";
		else $filtro_comercial="";

		$query_cliente=<<<EOT
select clt_id,clt_nom from client where clt_id in
(SELECT distinct(p.clt_id)
FROM 
 ss_projet s, projet p 
WHERE 
 s.prj_id = p.prj_id $filtro_comercial
) order by clt_nom
EOT;

		$db=new OlgaConnection();
		$db->query($query_cliente);
		$cliente[0]='Seleccione un cliente...';
		while ($row=$db->fetch()) 
			$cliente[$row['clt_id']]=$row['clt_nom'];
		return (count($cliente)>1)?$cliente:null;

	}

}
