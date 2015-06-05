<?php

class Dtes extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'bob';
	public static $table = 'softland.selma_dte';

	public static function emitidos($month,$year) {
		Config::set('database.fetch', PDO::FETCH_ASSOC);
		$query=<<<EOT
select
  t1.codaux
 ,t2.nomaux
 ,sum(t1.netoexento+t1.netoafecto) neto
from
 softland.iw_gsaen t1
inner join softland.cwtauxi t2 on t1.codaux=t2.codaux
where
(tipo='F' or tipo='N')
and month(t1.fecha)=?
and year(t1.fecha)=?
and t1.folio in (
 select folio from softland.dte_doccab where aceptadosii=1 and tipodte in (33,34,60,61,101) and rutemisor='76107191-2'
)
group by t1.codaux,t2.nomaux
order by neto desc;
EOT;
		return DB::connection('bob')->query($query,array($month,$year));
	}

}
