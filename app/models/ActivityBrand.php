<?php

class ActivityBrand extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	
	public static function selected_brand($id){
		$brands = self::select('brand_code')->where('activity_id', $id)->get();
		$_brands = array();
		if(!empty($brands)){
			foreach ($brands as $brand) {
				$_brands[] = $brand->brand_code;
			}
		}

		return $_brands;
	}
}