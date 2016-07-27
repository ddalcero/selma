<?php

Class Isban {

	public static function get($year,$month) {

		$proyecto="%ISBAN AT ".$year."%";
		$query_actividad=<<<EOT
select l.lot_id
    ,l.spj_id
    ,p.per_id
    ,l.lot_montant_euro as importe
    ,a.act_taux_jours as tarifa
    ,a.act_nb_jours_facturables as dias_facturables
    ,CONVERT(varchar(200), RTRIM(per_prenom) + ' ' + RTRIM(per_nom)) as consultor
    ,act_nb_jours_sem1+ act_nb_jours_sem2+ act_nb_jours_sem3+ act_nb_jours_sem4+
     act_nb_jours_sem5+ act_nb_jours_sem6 as imputado
    ,l.lot_libelle
    ,coalesce(l.fsi_id,0) as fsi_id
    ,coalesce(c.fcc_date,l.lot_date_previ_fac,0) as lot_fecha
from lot l
left join facture_sii s on l.fsi_id=s.fsi_id
left join facture_clt c on s.fcc_id=c.fcc_id
left join activite a on l.spj_id=a.spj_id
left join feuille_temps f on a.fdt_id=f.fdt_id
left join personnel p on f.per_id=p.per_id
where
    l.spj_id in (select spj_id from ss_projet where prj_id=(select prj_id from projet where prj_libelle like '$proyecto'))
    and month(f.fdt_mois_annee)= $month
    and year(f.fdt_mois_annee)= $year
    and month(lot_date_previ_fac)= $month
    and year(lot_date_previ_fac)= $year
order by p.per_nom asc
EOT;

		$db=new OlgaConnection();
		$db->query($query_actividad);
		return $db->all();
	}

	// TO-DO: Clear? not used at the moment
	public static function tarifa($spj_id) {
		$query_tarifa=<<<EOT
SELECT act_taux_jours as tarifa, per_prenom as nombre, per_nom as apellido
FROM activite A, feuille_temps F, personnel P
WHERE A.fdt_id = F.fdt_id
AND F.per_id = P.per_id
AND A.spj_id=$spj_id
EOT;
		$db=new OlgaConnection();
		$db->query($query_tarifa);
		return $db->all();
	}

}
