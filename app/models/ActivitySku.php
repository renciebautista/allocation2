<?php

class ActivitySku extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	
	public static function getSkus($id){
		$skus = array();
		foreach(self::where('activity_id',$id)->get() as $sku){
			$skus[] = $sku->sap_code;
		}

		return $skus;
	}

	public static function tradedealSkus($activity){
		return self::select('sap_code', 'sap_desc AS full_desc')
			->where('activity_id',$activity->id)
			->lists('full_desc', 'sap_code');
	}

	public static function getInvolves($id){
		return self::where('activity_id',$id)
			->join('pricelists', 'activity_skus.sap_code', '=', 'pricelists.sap_code')
			->orderBy('pricelists.sap_desc')
			->get();
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