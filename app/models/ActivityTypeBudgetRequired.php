<?php

class ActivityTypeBudgetRequired extends \Eloquent {
	protected $fillable = [];

	public static function required($id){
		$required = self::where('activity_type_id', $id)->get();
		$data = array();
		foreach ($required as $key => $value) {
			$data[] = $value->budget_type_id;
		}

		return $data;
	}
}