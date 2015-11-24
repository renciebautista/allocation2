<?php

class ShipTo extends \Eloquent {
	protected $fillable = ['customer_code', 'ship_to_code', 'ship_to_name', 'dayofweek', 'leadtime', 'split', 'active'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->customer_code)){
				$attributes = array(
					'customer_code' => $row->customer_code,
					'ship_to_code' => $row->ship_to_code,
					'ship_to_name' => $row->ship_to_name,
					'dayofweek' => $row->day_of_week,
					'leadtime' => $row->leadtime,
					'split' => $row->split,
					'active' => ($row->active == 'Y') ? 1 : 0);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function import($records){
		DB::beginTransaction();
			try {
			$records->each(function($row)  {
				if(!is_null($row->ship_to_name)){
					$shipto = self::where('ship_to_name',$row->ship_to_name)
						->where('customer_code',$row->customer_code)
						->first();
					if(empty($shipto)){
						$shipto = new ShipTo;
						$shipto->customer_code = $row->customer_code;
						$shipto->ship_to_name = $row->ship_to_name;
						$shipto->ship_to_code = $row->ship_to_code;
						$shipto->split = $row->split;
						$shipto->dayofweek = $row->dayofweek;
						$shipto->leadtime = $row->leadtime;
						$shipto->active = $row->active;
						$shipto->save();
					}else{
						$shipto->customer_code = $row->customer_code;
						$shipto->ship_to_code = $row->ship_to_code;
						$shipto->ship_to_name = $row->ship_to_name;
						$shipto->split = $row->split;
						$shipto->dayofweek = $row->dayofweek;
						$shipto->leadtime = $row->leadtime;
						$shipto->active = $row->active;
						$shipto->update();
					}
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}
}