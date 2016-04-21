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
				DB::table('ship_tos')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->ship_to_name)){
					$shipto = new ShipTo;
					$shipto->customer_code = $row->customer_code;
					$shipto->ship_to_code = $row->ship_to_code;
					$shipto->ship_to_name = $row->ship_to_name;
					$shipto->split = $row->split;
					$shipto->leadtime = $row->leadtime;
					$shipto->mon = $row->mon;
					$shipto->tue = $row->tue;
					$shipto->wed = $row->wed;
					$shipto->thu = $row->thu;
					$shipto->fri = $row->fri;
					$shipto->sat = $row->sat;
					$shipto->sun = $row->sun;
					$shipto->active = $row->active;
					$shipto->save();
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}

	public function getDayOfWeek($id){
		switch ($id) {
			case '1':
				return $this->mon;
				break;
			case '2':
				return $this->tue;
				break;
			case '3':
				return $this->wed;
				break;
			case '4':
				return $this->thu;
				break;
			case '5':
				return $this->fri;
				break;
			case '6':
				return $this->sat;
				break;
			case '7':
				return $this->sun;
				break;
			
			default:
				# code...
				break;
		}

	}
}