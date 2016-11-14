<?php

class ShipTo extends \Eloquent {
	protected $fillable = ['customer_code', 'ship_to_code', 'ship_to_name', 'dayofweek', 'leadtime', 'split', 'active'];
	public $timestamps = false;

	public static function getShipToByCustomers($customer){
		return self::where('customer_code', $customer->customer_code)
			->where('active',1)
			->get();
	}

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
					$shipto->sold_to_code = $row->sold_to_code;
					$shipto->ship_to_code = $row->ship_to_code;
					$shipto->plant_code = $row->plant_code;
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

	public static function getAllShipTo(){
		$group_code = array();
		$area_code = array();
		$sold_to_code = array();

		$filters = SobFilter::all();
		foreach ($filters as $filter) {
			if($filter->group_code != "0"){
				if (!in_array($filter->group_code, $group_code)) {
				    $group_code[] = $filter->group_code;
				}
			}

			if($filter->area_code != "0"){
				if (!in_array($filter->area_code, $area_code)) {
				    $area_code[] = $filter->area_code;
				}
			}

			if($filter->customer_code != "0"){
				if (!in_array($filter->customer_code, $sold_to_code)) {
				    $sold_to_code[] = $filter->customer_code	;
				}
			}
		}

		$shiptos =  self::join('customers', 'customers.customer_code', '=', 'ship_tos.customer_code')
			->join('areas', 'areas.area_code', '=', 'customers.area_code')
			->join('groups', 'groups.group_code', '=', 'areas.group_code')
			->where('customers.active',1)
			->groupBy('ship_tos.ship_to_code')
			->orderBy('areas.id')
			->orderBy('ship_tos.id')
			->get();

		$new = $shiptos->filter(function($shipto) use ($group_code,$area_code, $sold_to_code)
	    {
	        if((in_array($shipto->group_code, $group_code)) || (in_array($shipto->area_code, $area_code))|| (in_array($shipto->customer_code, $sold_to_code))){
				return true;
			}
	    });

		return $new;

	}

	public static function getOlr($activity_type){
		
	}
}