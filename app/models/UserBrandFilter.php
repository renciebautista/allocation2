<?php

class UserBrandFilter extends \Eloquent {
	protected $fillable = [];

	public static function selected_brand(){
		$brands = self::select('brand_code')->where('user_id',  Auth::id())->get();
		$_brands = array();
		if(!empty($brands)){
			foreach ($brands as $brand) {
				$_brands[] = $brand->brand_code;
			}
		}

		return $_brands;
	}
}