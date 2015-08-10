<?php

class ActivityCutomerList extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;
	protected $table = 'activity_customer_lists';

	private static function isSelected($key,$collections){

		if(in_array($key, $collections)){
			return true;	
		}
		return false;
	}

	public static function addCustomer($activity_id,$collections){
		self::where('activity_id',$activity_id)->delete();

		$selected_customers = array();
		if(count($collections) > 0){
			foreach ($collections as $collection) {
				$selected_customers[] = $collection['customer_node'];
			}
		}
		
		$groups = Customer::getCustomerList();
		$data = array();
		foreach ($groups as $group) {
			$grp_selected = self::isSelected($group['key'],$selected_customers);
			$data[] = array('activity_id' => $activity_id,
				'parent_id' =>null,
				'title' => $group['title'], 
				'isfolder' => $group['isfolder'], 
				'key' => $group['key'], 
				'unselectable' => $group['unselectable'],
				'selected' => $grp_selected);
			if(count($group['children'])>0){
				foreach ($group['children'] as $area) {
					$area_selceted = self::isSelected($group['key'].".".$area['key'],$selected_customers) || $grp_selected;
					$data[] = array('activity_id' => $activity_id,
						'parent_id' => $group['key'],
						'title' => $area['title'], 
						'isfolder' => $area['isfolder'], 
						'key' => $area['key'], 
						'unselectable' => $area['unselectable'],
						'selected' =>  $area_selceted);
					if(count($area['children'])>0){
						foreach ($area['children'] as $soldto) {
							$soldto_selected  = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'],$selected_customers) || $area_selceted;
							$data[] =  array('activity_id' => $activity_id,
								'parent_id' => $area['key'],
								'title' => $soldto['title'], 
								'isfolder' => isset($soldto['isfolder']) ? $soldto['isfolder'] : null, 
								'key' => $soldto['key'], 
								'unselectable' => $soldto['unselectable'],
								'selected' => $soldto_selected);
							if(count($soldto['children'])>0){
								foreach ($soldto['children'] as $shipto) {
									$shipto_selected = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'].".".$shipto['key'],$selected_customers) || $soldto_selected;
									$data[] = array('activity_id' => $activity_id,
										'parent_id' => $soldto['key'],
										'title' => $shipto['title'], 
										'isfolder' => isset($shipto['isfolder']) ? $shipto['isfolder'] : null, 
										'key' => $shipto['key'], 
										'unselectable' => $shipto['unselectable'],
										'selected' => $shipto_selected );
									if(isset($shipto['children'])){
										if(count($shipto['children']	)>0){
											foreach ($shipto['children'] as $account) {
												$account_selected = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'].".".$shipto['key'].".".$account['key'],$selected_customers) ||  $shipto_selected;
												$data[] = array('activity_id' => $activity_id,
													'parent_id' => $shipto['key'],
													'title' => $account['title'], 
													'isfolder' => isset($account['isfolder']) ? $account['isfolder'] : null, 
													'key' => $account['key'], 
													'unselectable' => $account['unselectable'],
													'selected' => $account_selected);

											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		self::insert($data);
	}

	public static function getSelectedAreas($id){
		$selected = ActivityCustomer::customers($id);
		$areas = array();
		foreach ($selected as $row) {
			$_selected_customer = explode(".", $row);

			if(!empty($_selected_customer[1])){
				$sel_area = self::where('key',$_selected_customer[1])->first();
				$areas[$sel_area->key] = $sel_area->title;
			}else{
				//get all areas
				$sel_areas = self::where('parent_id',$_selected_customer[0])->get();
				foreach ($sel_areas as $sel_area) {
					// echo $sel_area->area_name;
					$areas[$sel_area->key] = $sel_area->title;
				}

			}
		}
		return $areas;

	}
}