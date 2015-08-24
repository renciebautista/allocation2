<?php

class LaunchSkuAccess extends \Eloquent {
	protected $fillable = [];
	protected $table = 'launch_sku_access';

	public static function selectedUser($sku_code){
		$data = array();
		$records = self::where('sku_code', $sku_code)->get();
		foreach ($records as $key => $value) {
			$data[] = $value->user_id;
		}
		return $data;
	}
}