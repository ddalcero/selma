<?php

class Factura {

	/**
	 * Total facturas emitidas en olga en un periodo, agrupadas cliente
	 * @param $month
	 * @param $year
	 * @return array|null
	 */
	public static function emitidas($month,$year) {
		$query=<<<EOT
SELECT
	fcc.clt_id
	,sum(fsi.fsi_montant_euro) suma
FROM
     facture_clt fcc
     ,facture_sii fsi
WHERE
     fsi.fcc_id = fcc.fcc_id
     and fcc_type<>'S'
     and month(fcc_date)=$month
     and year(fcc_date)=$year
GROUP BY fcc.clt_id
ORDER BY suma desc
EOT;
		$db=new OlgaConnection();
		$db->query($query);
		return $db->all();
	}
}
