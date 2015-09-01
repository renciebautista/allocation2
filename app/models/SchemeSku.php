<?php

class SchemeSku extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	
	public static function getSkus($id){
		$skus = array();
		foreach(self::where('scheme_id',$id)->get() as $sku){
			$skus[] = $sku->sku;
		}

		return $skus;
	}


	
}