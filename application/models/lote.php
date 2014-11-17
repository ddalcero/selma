<?php

class Lote {

	// Listado de lotes por un subproyecto en un periodo
	public static function get_period($year, $month, $spj_id) {

		$query_lote=<<<EOT
select l.lot_id
	,l.lot_libelle
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
	,l.lot_date_previ_fac
	,l.lot_montant_euro
	,l.spj_id
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
