<?php

class OrgChart extends Eloquent {

	public static $timestamps = false;
	public static $connection = 'mysql';
	public static $table='orgchart';

	public static function descendants($parent) {
		return DB::query('SELECT hi.id AS treeitem,hierarchy_sys_connect_by_path(\'/\',hi.id) AS path,parent,level FROM(SELECT hierarchy_connect_by_parent_eq_prior_id_with_level(id,5) AS id,CAST(@level AS SIGNED) AS level FROM(SELECT @start_with:=?,@id:=@start_with,@level:=0) vars, orgchart WHERE @id IS NOT NULL) ho JOIN orgchart hi ON hi.id = ho.id',$parent);
	}

	public static function desc_ids($parent) {
		$descendants=self::descendants($parent);
		foreach ($descendants as $descendant) $ids[]=$descendant->treeitem;
		return (isset($ids) and count($ids)>0)?$ids:null;
	}

}
