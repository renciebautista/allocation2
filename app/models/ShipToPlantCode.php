<?php

class ShipToPlantCode extends \Eloquent {
	protected $fillable = ['ship_to_code', 'plant_code'];
	public $timestamps = false;

	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('ship_to_plant_codes')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->ship_to_name)){
					$maping = new ShipToPlantCode;
					$maping->group_code = $row->group_code;
					$maping->group = $row->group;
					$maping->area_code = $row->area_code;
					$maping->area = $row->area;
					$maping->customer_code = $row->customer_code;
					$maping->customer = $row->customer;
					$maping->distributor_code = $row->distributor_code;
					$maping->distributor_name = $row->distributor_name;
					$maping->plant_code = $row->plant_code;
					$maping->ship_to_name = $row->ship_to_name;
					$maping->save();
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}
}