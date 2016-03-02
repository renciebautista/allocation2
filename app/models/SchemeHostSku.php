<?php

class SchemeHostSku extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	
	public static function getHosts($id){
		$hosts = array();
		foreach(self::where('scheme_id',$id)->get() as $host){
			$hosts[] = $host->sap_code;
		}

		return $hosts;
	}

	public static function alreadyUsed($sap_code){
		$used = self::where('sap_code',$sap_code)->get();
		if(count($used)>0){
			return true;
		}else{
			return false;
		}

	}
}