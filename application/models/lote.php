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

	// actualiza el importe de un lote, dado su ID
	public static function update($lot_id,$total) {
		$query="update lot set lot_montant_euro=".$total." where lot_id=".$lot_id;
		$db=new OlgaConnection();
		return $db->update($query);
	}
}
