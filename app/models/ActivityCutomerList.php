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

	// public static function addCustomer_old($activity_id,$collections){
	// 	self::where('activity_id',$activity_id)->delete();

	// 	$selected_customers = array();
	// 	if(count($collections) > 0){
	// 		foreach ($collections as $collection) {
	// 			$selected_customers[] = $collection['customer_node'];
	// 		}
	// 	}
		
	// 	$groups = Customer::getCustomerList();
	// 	$data = array();
	// 	foreach ($groups as $group) {
	// 		$grp_selected = self::isSelected($group['key'],$selected_customers);
	// 		$data[] = array('activity_id' => $activity_id,
	// 			'parent_id' =>null,
	// 			'title' => $group['title'], 
	// 			'isfolder' => $group['isfolder'], 
	// 			'key' => $group['key'], 
	// 			'unselectable' => $group['unselectable'],
	// 			'selected' => $grp_selected);
	// 		if(count($group['children'])>0){
	// 			foreach ($group['children'] as $area) {
	// 				// $area_selceted = self::isSelected($group['key'].".".$area['key'],$selected_customers) || $grp_selected;
	// 				$area_selceted = self::isSelected($area['key'],$selected_customers) || $grp_selected;
	// 				$data[] = array('activity_id' => $activity_id,
	// 					'parent_id' => $group['key'],
	// 					'title' => $area['title'], 
	// 					'isfolder' => $area['isfolder'], 
	// 					'key' => $area['key'], 
	// 					'unselectable' => $area['unselectable'],
	// 					'selected' =>  $area_selceted);
	// 				if(count($area['children'])>0){
	// 					foreach ($area['children'] as $soldto) {
	// 						// $soldto_selected  = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'],$selected_customers) || $area_selceted;
	// 						$soldto_selected  = self::isSelected($soldto['key'],$selected_customers) || $area_selceted;
	// 						$data[] =  array('activity_id' => $activity_id,
	// 							'parent_id' => $area['key'],
	// 							'title' => $soldto['title'], 
	// 							'isfolder' => isset($soldto['isfolder']) ? $soldto['isfolder'] : null, 
	// 							'key' => $soldto['key'], 
	// 							'unselectable' => $soldto['unselectable'],
	// 							'selected' => $soldto_selected);
	// 						if(count($soldto['children'])>0){
	// 							foreach ($soldto['children'] as $shipto) {
	// 								// $shipto_selected = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'].".".$shipto['key'],$selected_customers) || $soldto_selected;
	// 								$shipto_selected = self::isSelected($shipto['key'],$selected_customers) || $soldto_selected;
	// 								$data[] = array('activity_id' => $activity_id,
	// 									'parent_id' => $soldto['key'],
	// 									'title' => $shipto['title'], 
	// 									'isfolder' => isset($shipto['isfolder']) ? $shipto['isfolder'] : null, 
	// 									'key' => $shipto['key'], 
	// 									'unselectable' => $shipto['unselectable'],
	// 									'selected' => $shipto_selected );
	// 								if(isset($shipto['children'])){
	// 									if(count($shipto['children'])>0){
	// 										foreach ($shipto['children'] as $account) {

	// 											// $account_selected = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'].".".$shipto['key'].".".$account['key'],$selected_customers) ||  $shipto_selected;
	// 											$account_selected = self::isSelected($account['key'],$selected_customers) ||  $shipto_selected;
	// 											$data[] = array('activity_id' => $activity_id,
	// 												'parent_id' => $shipto['key'],
	// 												'title' => $account['title'], 
	// 												'isfolder' => isset($account['isfolder']) ? $account['isfolder'] : null, 
	// 												'key' => $account['key'], 
	// 												'unselectable' => $account['unselectable'],
	// 												'selected' => $account_selected);

	// 										}
	// 									}
	// 								}
	// 							}
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// 	self::insert($data);
	// }

	public static function addCustomer($activity_id,$collections){
		self::where('activity_id',$activity_id)->delete();

		$selected_customers = array();
		if(count($collections) > 0){
			foreach ($collections as $collection) {
				$selected_customers[] = $collection['customer_node'];
			}
		}
		
		$channels = Customer::getChannelCustomerList();
		$data = array();

		foreach ($channels as $channel) {
			$ch_selected = self::isSelected($channel['key'],$selected_customers);
			$data[] = array('activity_id' => $activity_id,
				'parent_id' =>null,
				'title' => $channel['title'], 
				'isfolder' => $channel['isfolder'], 
				'key' => $channel['key'], 
				'unselectable' => true,
				'selected' => $ch_selected);
			if(count($channel['children'])>0){
				foreach ($channel['children'] as $group) {
					// $area_selceted = self::isSelected($group['key'].".".$area['key'],$selected_customers) || $grp_selected;
					$grps_selected = self::isSelected($group['key'],$selected_customers) || $ch_selected;
					$data[] = array('activity_id' => $activity_id,
						'parent_id' => $channel['key'],
						'title' => $group['title'], 
						'isfolder' => $group['isfolder'], 
						'key' => $group['key'], 
						'unselectable' => true,
						'selected' =>  $grps_selected);
					if(count($group['children'])>0){
						foreach ($group['children'] as $area) {
							// $soldto_selected  = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'],$selected_customers) || $grps_selected;
							$area_selected  = self::isSelected($area['key'],$selected_customers) || $grps_selected;
							$data[] =  array('activity_id' => $activity_id,
								'parent_id' => $group['key'],
								'title' => $area['title'], 
								'isfolder' => isset($area['isfolder']) ? $area['isfolder'] : null, 
								'key' => $area['key'], 
								'unselectable' => true,
								'selected' => $area_selected);
							if(count($area['children'])>0){
								foreach ($area['children'] as $customer) {
									// $shipto_selected = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'].".".$shipto['key'],$selected_customers) || $area_selected;
									$customer_selected = self::isSelected($customer['key'],$selected_customers) || $area_selected;
									$data[] = array('activity_id' => $activity_id,
										'parent_id' => $area['key'],
										'title' => $customer['title'], 
										'isfolder' => isset($customer['isfolder']) ? $customer['isfolder'] : null, 
										'key' => $customer['key'], 
										'unselectable' => true,
										'selected' => $customer_selected );
									if(isset($customer['children'])){
										if(count($customer['children'])>0){
											foreach ($customer['children'] as $shipto) {
												// $account_selected = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'].".".$shipto['key'].".".$account['key'],$selected_customers) ||  $shipto_selected;
												$shipto_selected = self::isSelected($shipto['key'],$selected_customers) ||  $customer_selected;
												$data[] = array('activity_id' => $activity_id,
													'parent_id' => $customer['key'],
													'title' => $shipto['title'], 
													'isfolder' => isset($shipto['isfolder']) ? $shipto['isfolder'] : null, 
													'key' => $shipto['key'], 
													'unselectable' => true,
													'selected' => $shipto_selected);
												if(isset($shipto['children'])){
													if(count($shipto['children'])>0){
														foreach ($shipto['children'] as $account) {
															// $account_selected = self::isSelected($group['key'].".".$area['key'].".".$soldto['key'].".".$shipto['key'].".".$account['key'],$selected_customers) ||  $shipto_selected;
															$account_selected = self::isSelected($account['key'],$selected_customers) ||  $shipto_selected;
															$data[] = array('activity_id' => $activity_id,
																'parent_id' => $shipto['key'],
																'title' => $account['title'], 
																'isfolder' => isset($account['isfolder']) ? $account['isfolder'] : null, 
																'key' => $account['key'], 
																'unselectable' => true,
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
				}
			}
		}
		if(count($data) > 0){
			self::insert($data);
		}
		
	}

	public static function getSelectedAreas($id){
		$selected = ActivityCustomer::customers($id);
		$areas = array();
		foreach ($selected as $row) {
			$_selected_customer = explode(".", $row);
			if(substr($_selected_customer[0], 0, 1) == 'C'){
				if(count($_selected_customer) == 1){
					$selected_groups = self::where('parent_id',$_selected_customer[0])
						->where('activity_id',$id)
						->get();
					foreach ($selected_groups as $selected_group) {
						$sel_areas = self::where('parent_id',$selected_group->key)
							->where('activity_id',$id)
							->get();
							foreach ($sel_areas as $sel_area) {
								$areas[] = $sel_area->title;
							}
					}
				}elseif(count($_selected_customer) == 2){
					$sel_areas = self::where('parent_id',$row)
						->where('activity_id',$id)
						->get();
					foreach ($sel_areas as $sel_area) {
						$areas[] = $sel_area->title;
					}
				}elseif(count($_selected_customer)  > 2){
					$sel_area = self::where('key',$_selected_customer[0].'.'.$_selected_customer[1].'.'.$_selected_customer[2])
						->where('activity_id',$id)
						->first();
					$areas[] = $sel_area->title;
				}else{

				}
				
			}else{
				if(!empty($_selected_customer[1])){
					$sel_area = self::where('key',$_selected_customer[0].'.'.$_selected_customer[1])->first();
					$areas[] = $sel_area->title;
				}else{
					//get all areas
					$sel_areas = self::where('parent_id',$_selected_customer[0])
						->where('activity_id',$id)
						->get();
					foreach ($sel_areas as $sel_area) {
						$areas[] = $sel_area->title;
					}
				}
			}
		}

		asort($areas);
		return  array_unique($areas) ;

	}
}