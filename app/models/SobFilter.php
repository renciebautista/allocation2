<?php

class SobFilter extends \Eloquent {
	protected $fillable = [];

	public $timestamps = false;


	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('sob_filters')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->group_code)){
					$filter = new SobFilter;
					$filter->group_code = $row->group_code;
					$filter->area_code = $row->area_code;
					$filter->customer_code = $row->customer_code;
					$filter->save();
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
		}
	}
}