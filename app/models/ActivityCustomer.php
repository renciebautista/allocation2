<?php

class ActivityCustomer extends \Eloquent {
	protected $fillable = [];

	public static function customers($id){
		$customers = array();
		$records = self::where('activity_id',$id)->orderBy('id')->get();
		foreach( $records as $customer){
			$customers[] = $customer->customer_node;
		}

		return $customers;
	}
	
	public static function getSelectedAreas($activity_id){
		$selected = self::customers($activity_id);
		$areas = array();
		foreach ($selected as $row) {
			$_selected_customer = explode(".", $row);
			if(!empty($_selected_customer[1])){
				$sel_area = Area::where('area_code',$_selected_customer[1])->first();
				$areas[$sel_area->area_name] = $sel_area->area_name;
			}else{
				//get all areas
				$sel_areas = Area::where('group_code',$_selected_customer[0])->get();
				foreach ($sel_areas as $sel_area) {
					// echo $sel_area->area_name;
					$areas[$sel_area->area_name] = $sel_area->area_name;
				}

			}
		}
		return $areas;
	}
}