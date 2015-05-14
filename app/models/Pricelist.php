<?php

class Pricelist extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function items(){
		return self::select('sap_code', DB::raw('CONCAT(sap_desc, "- ", sap_code) AS full_desc'))
			->orderBy('sap_desc')->lists('full_desc', 'sap_code');
	}

	public static function getSku($sap_code){
		return self::where('sap_code',$sap_code)->first();
	}
}