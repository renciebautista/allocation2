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


	public static function getSelectedChannels($activity){
		$channels = [];
		$channel_nodes = self::where('activity_id', $activity->id)->get();
		foreach ($channel_nodes as $channel_node) {
		 	$node = explode('.', $channel_node->customer_node);

		 	$channels[] = $node[0];
		}

		return array_unique($channels); 
	}


	public static function getNodeSelection($customers){
		$object = new stdClass();
		if(!empty($customers)){
			foreach ($customers as $selected_customer) {
				$n = explode('.', $selected_customer);
				if(count($n) == 1){
					$n_nodes = CustomerTree::where('channel_code', $n[0])->get();
					foreach ($n_nodes as $n_node) {

						if($n_node->plant_code != ''){
							$object->ship_nodes[] = $n_node->plant_code;
						}

						$object->cust_nodes[] = $n_node->customer_code;

					}
					
				}

				if(count($n) == 2){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$object->ship_nodes[] = $n_node->plant_code;
						}

						$object->cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 3){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$object->ship_nodes[] = $n_node->plant_code;
						}

						$object->cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 4){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->where('customer_code', $n[3])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$object->ship_nodes[] = $n_node->plant_code;
						}

						$object->cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 5){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->where('customer_code', $n[3])
						->where('plant_code', $n[4])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$object->ship_nodes[] = $n_node->plant_code;
						}

						$object->cust_nodes[] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 6){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->where('customer_code', $n[3])
						->where('plant_code', $n[4])
						->where('account_id', $n[5])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->plant_code != ''){
							$object->ship_nodes[] = $n_node->plant_code;
						}

						$object->cust_nodes[] = $n_node->customer_code;
					}
					
				}
			}
		}

		return $object;
	}
}