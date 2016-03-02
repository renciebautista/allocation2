<?php

class SchemePremuimSku extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	public static function getPremuim($id){
		$premuim = array();
		foreach(self::where('scheme_id',$id)->get() as $host){
			$premuim[] = $host->sap_code;
		}

		return $premuim;
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