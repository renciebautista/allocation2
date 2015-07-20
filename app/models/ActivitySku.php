<?php

class ActivitySku extends \Eloquent {
	protected $fillable = [];

	public static function getSkus($id){
		$skus = array();
		foreach(self::where('activity_id',$id)->get() as $sku){
			$skus[] = $sku->sap_code;
		}

		return $skus;
	}

	public static function getInvolves($id){
		return self::where('activity_id',$id)
			->join('pricelists', 'activity_skus.sap_code', '=', 'pricelists.sap_code')
			->orderBy('pricelists.sap_desc')
			->get();
	}
}