<?php

class Lote {

	// Listado de lotes por un subproyecto en un periodo
	public static function get_period($year, $month, $spj_id) {

		$query_lote=<<<EOT
select l.lot_id
	,l.lot_libelle
	,l.lot_libelle_fac_clt
	,l.lot_date_previ_fac
	,l.lot_montant_euro
	,coalesce(l.fsi_id,0) as fsi_id
	,coalesce(c.fcc_date,l.lot_date_previ_fac,0) as lot_fecha
from lot l
left join facture_sii s on l.fsi_id=s.fsi_id
left join facture_clt c on s.fcc_id=c.fcc_id
where 
	spj_id= $spj_id
	and month(lot_date_previ_fac)= $month
	and year(lot_date_previ_fac)= $year
EOT;

		$db=new OlgaConnection();
		$db->query($query_lote);
		return $db->all();

	}

	// Listado de lotes por un subproyecto
	public static function get($spj_id) {

		$query_lote=<<<EOT
select l.lot_id
	,l.lot_libelle
	,l.lot_libelle_fac_clt
	,l.lot_date_previ_fac
	,l.lot_montant_euro
	,coalesce(l.fsi_id,0) as fsi_id
	,coalesce(c.fcc_date,l.lot_date_previ_fac,0) as lot_fecha
from lot l
left join facture_sii s on l.fsi_id=s.fsi_id
left join facture_clt c on s.fcc_id=c.fcc_id
where 
	spj_id= $spj_id
order by
	l.lot_id
EOT;

		$db=new OlgaConnection();
		$db->query($query_lote);
		return $db->all();

	}

	// Detalle de un lote
	public static function getLote($lot_id) {

		$query_lote=<<<EOT
select l.lot_id
	,l.lot_libelle
	,l.lot_libelle_fac_clt
	,l.lot_date_previ_fac
	,l.lot_montant_euro
	,l.spj_id
	,l.lot_tva
	,l.lot_taux_tva
	,coalesce(l.fsi_id,0) as fsi_id
	,coalesce(c.fcc_date,l.lot_date_previ_fac,0) as lot_fecha
from lot l
left join facture_sii s on l.fsi_id=s.fsi_id
left join facture_clt c on s.fcc_id=c.fcc_id
where 
	l.lot_id= $lot_id
EOT;

		$db=new OlgaConnection();
		$db->query($query_lote);
		return $db->all();

	}

	public static function get_pendientes($year, $month, $per_id=0) {
		if ($per_id!=0) $filtro_comercial="and (S.per_id_com=$per_id or S.per_id_cdp=$per_id)";
		else $filtro_comercial="";

		$query_lote=<<<EOT
select l.lot_id
	,sp.clt_id
	,cl.clt_nom
	,l.lot_libelle
	,l.lot_libelle_fac_clt
	,l.lot_date_previ_fac
	,l.lot_montant_euro
	,coalesce(l.fsi_id,0) as fsi_id
	,coalesce(c.fcc_date,l.lot_date_previ_fac,0) as lot_fecha
from lot l
left join facture_sii s on l.fsi_id=s.fsi_id
left join facture_clt c on s.fcc_id=c.fcc_id
left join ss_projet sp on l.spj_id=sp.spj_id
left join client cl on sp.clt_id=cl.clt_id
where 
	month(lot_date_previ_fac)=3
	and year(lot_date_previ_fac)=2015
	and (l.fsi_id = 0 or l.fsi_id is null) 
	and l.spj_id in (
		SELECT s.spj_id
		FROM ss_projet s, projet p 
		WHERE s.prj_id = p.prj_id $filtro_comercial
	)
order by cl.clt_nom
EOT;

		$db=new OlgaConnection();
		$db->query($query_lote);
		return $db->all();
		
	}

	// actualiza los datos de un lote, dado su ID
	public static function update($lot_id,$total,$fecha=null,$nombre=null,$desc=null) {
		$appendSql=" ";
		if ($fecha!=null) {
			$appendSql=",lot_date_previ_fac='".ViewFormat::dateToDB($fecha)."'";
		}
		if ($nombre!=null) {
			$nombre=str_replace(array('\'','"','<','>','\\','(',')','?'),'',$nombre);
			$appendSql.=",lot_libelle='".$nombre."'";
		}
		if ($desc!=null) {
			$desc=str_replace(array('\'','"','<','>','\\','(',')','?'),'',$desc);
			$appendSql.=",lot_libelle_fac_clt='".$desc."'";
		}
		$query="update lot set lot_montant_euro=".intval($total).$appendSql." where lot_id=".$lot_id;
		$db=new OlgaConnection();
		return $db->update($query);
	}

	// añade un lote
	public static function addlote($fecha,$monto,$spj_id) {
		$db=new OlgaConnection();
		return $db->addlote($fecha,$monto,$spj_id);
	}

	// elimina un lote
	public static function delete($lot_id) {
		$query_lote="delete from lot where lot_id = ".$lot_id;

		$db=new OlgaConnection();
		$db->query($query_lote);
		return $db->result();
	}

}
