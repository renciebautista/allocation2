<?php

class ShipHoliday extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getSelected($id){
		$records = self::where('sob_holiday_id',$id)->get();
		$data = [];
		foreach ($records as $key => $value) {
			$data[] = $value->ship_to_code;
		}

		return $data;
	}
}