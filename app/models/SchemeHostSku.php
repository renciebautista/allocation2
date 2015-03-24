<?php

class SchemeHostSku extends \Eloquent {
	protected $fillable = [];

	public static function getHosts($id){
		$hosts = array();
		foreach(self::where('scheme_id',$id)->get() as $host){
			$hosts[] = $host->sap_code;
		}

		return $hosts;
	}
}