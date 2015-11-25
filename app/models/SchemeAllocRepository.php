<?php

class SchemeAllocRepository
{
	public static function insertAlllocation($skus,$scheme){
		self::save($skus,$scheme);
	}

	public static function updateAllocation($skus,$scheme){
		SchemeAllocation::where('scheme_id',$scheme->id)->delete();
		self::save($skus,$scheme);
	}

	public static function gettemplate($scheme){
		$forced_areas = ForceAllocation::getForcedAreas($scheme->activity_id);
		$customers = ActivityCustomer::customers($scheme->activity_id);
		$_channels = ActivityChannel2::channels($scheme->activity_id);

		$_allocation = new AllocationRepository;
		$allocations = $_allocation->customers(array(), $_channels, $customers,$forced_areas);
		$list = array();
		$id = 1;	
		foreach ($allocations as $customer) {
			$scheme_alloc = new stdClass();
			$scheme_alloc->id = $id;
			$scheme_alloc->scheme_id = $scheme->id;
			$scheme_alloc->customer_id = '';
			$scheme_alloc->shipto_id = '';

			$scheme_alloc->group_code = $customer->group_code;
			$scheme_alloc->group = $customer->group_name;

			$scheme_alloc->area_code = $customer->area_code;
			$scheme_alloc->area = $customer->area_name;

			$scheme_alloc->sold_to_code = $customer->customer_code;
			$scheme_alloc->sob_customer_code = $customer->sob_customer_code;
			$scheme_alloc->sold_to = $customer->customer_name;

			$scheme_alloc->ship_to_code = '';
			$scheme_alloc->ship_to = $customer->customer_name . ' TOTAL';


			$scheme_alloc->channel_code = '';
			$scheme_alloc->channel = '';	

			$scheme_alloc->account_group_code = '';
			$scheme_alloc->account_group_name = '';				
							
			$scheme_alloc->outlet = '';

			$scheme_alloc->show = 0;

			if(empty($customer->shiptos)){
				$scheme_alloc->show = 1;
			}
			$scheme_alloc->allocation = 0;
			$list[] = $scheme_alloc;

			// var_dump($scheme_alloc);
			$id++;
			if(!empty($customer->shiptos)){
				foreach($customer->shiptos as $shipto){
					$shipto_alloc = new stdClass();
					$shipto_alloc->id = $id;
					$shipto_alloc->scheme_id = $scheme->id;
					$shipto_alloc->customer_id = $scheme_alloc->id;
					$shipto_alloc->shipto_id = '';

					$shipto_alloc->group_code = $customer->group_code;
					$shipto_alloc->group = $customer->group_name;

					$shipto_alloc->area_code = $customer->area_code;
					$shipto_alloc->area = $customer->area_name;

					$shipto_alloc->sold_to_code = $customer->customer_code;
					$shipto_alloc->sob_customer_code = $customer->sob_customer_code;
					$shipto_alloc->sold_to = $customer->customer_name;

					$shipto_alloc->ship_to_code = $shipto['ship_to_code'];
					$shipto_alloc->ship_to = $shipto['ship_to_name'];

					$shipto_alloc->channel_code = '';
					$shipto_alloc->channel = '';		

					$shipto_alloc->account_group_code = '';
					$shipto_alloc->account_group_name = '';			
							
					$shipto_alloc->outlet = '';

					$shipto_alloc->show = 0;
				   	if(empty($shipto['accounts'])){
				   		$shipto_alloc->show = 1;
				   	}
				   	$shipto_alloc->allocation = 0;
				   	$list[] = $shipto_alloc;
				   	$id++;
					if(!empty($shipto['accounts'])){
						foreach($shipto['accounts'] as $account){
							$account_alloc = new stdClass();
							$account_alloc->id = $id;
							$account_alloc->scheme_id = $scheme->id;
							$account_alloc->customer_id = $scheme_alloc->id;
							$account_alloc->shipto_id = $shipto_alloc->id;

							$account_alloc->group_code = $customer->group_code;
							$account_alloc->group = $customer->group_name;

							$account_alloc->area_code = $customer->area_code;
							$account_alloc->area = $customer->area_name;

							$account_alloc->sold_to_code = $customer->customer_code;
							$account_alloc->sob_customer_code = $customer->sob_customer_code;
							$account_alloc->sold_to = $customer->customer_name;

							$account_alloc->ship_to_code = $shipto['ship_to_code'];
							$account_alloc->ship_to = $shipto['ship_to_name'];

							$account_alloc->channel_code = $account['channel_code'];
							$account_alloc->channel = $account['channel_name'];

							$account_alloc->account_group_code = $account['account_group_code'];
							$account_alloc->account_group_name = $account['account_group_name'];

							$account_alloc->outlet = $account['account_name'];
 
							$account_alloc->show = 1;
							$account_alloc->allocation = 0;
							$list[] = $account_alloc;
							$id++;
						}

						if(empty($customer->area_code_two)){
							$others_alloc = new stdClass();
							$others_alloc->id = $id;
							$others_alloc->scheme_id = $scheme->id;
							$others_alloc->customer_id = $scheme_alloc->id;
							$others_alloc->shipto_id = $shipto_alloc->id;

							$others_alloc->group_code = $customer->group_code;
							$others_alloc->group = $customer->group_name;

							$others_alloc->area_code = $customer->area_code;
							$others_alloc->area = $customer->area_name;

							$others_alloc->sold_to_code = $customer->customer_code;
							$others_alloc->sob_customer_code = $customer->sob_customer_code;
							$others_alloc->sold_to = $customer->customer_name;

							$others_alloc->ship_to_code = $shipto['ship_to_code'];
							$others_alloc->ship_to = $shipto['ship_to_name'];	

							$others_alloc->channel_code = 'OTHERS';
							$others_alloc->channel = 'OTHERS';	

							$others_alloc->account_group_code = '';
							$others_alloc->account_group_name = '';			
							
							$others_alloc->outlet = 'OTHERS';

							$others_alloc->show = 1;

							$others_alloc->allocation = 0;
							$list[] = $others_alloc;
							$id++;
						}
						
					}
				}
			}
			

		}
		return $list;
	}

	private static function save($skus,$scheme){
		$activity = Activity::find($scheme->activity_id);
		$forced_areas = ForceAllocation::getForcedAreas($scheme->activity_id);
		$customers = ActivityCustomer::customers($scheme->activity_id);
		$_channels = ActivityChannel2::channels($scheme->activity_id);


		$_allocation = new AllocationRepository;
		$allocations = $_allocation->customers($skus, $_channels, $customers,$forced_areas);
		$_areasales =  $_allocation->area_sales();
	   	// Helper::print_r($allocations);
	   	// dd($allocations);
		$total_sales = $_allocation->total_gsv();
		$force_total_sales = $_allocation->force_total_gsv();
		$force_alloc = $activity->allow_force;
		foreach ($allocations as $customer) {
			$scheme_alloc = new SchemeAllocation;
			$scheme_alloc->scheme_id = $scheme->id;

			$scheme_alloc->group_code = $customer->group_code;
			$scheme_alloc->group = $customer->group_name;

			$scheme_alloc->area_code = $customer->area_code;
			$scheme_alloc->area = $customer->area_name;

			$scheme_alloc->sold_to_code = $customer->customer_code;
			$scheme_alloc->sob_customer_code = $customer->sob_customer_code;
			$scheme_alloc->sold_to = $customer->customer_name;


			$scheme_alloc->ship_to = $customer->customer_name . ' TOTAL';

			// sold to gsv
			$sold_to_gsv_p = 0;
			$fore_sold_to_gsv_p = 0;
			$scheme_alloc->forced_sold_to_gsv = 0;  
			if($customer->gsv > 0){
				if($total_sales > 0){
					$sold_to_gsv_p = round(($customer->gsv/$total_sales) * 100,2);
				}
				$scheme_alloc->sold_to_gsv = $customer->gsv;    
				
				if($force_alloc){
					if(array_key_exists($customer->area_code, $forced_areas)){
						$forced_customer_gsv = $customer->gsv * $forced_areas[$customer->area_code];
						if($force_total_sales > 0){
							$fore_sold_to_gsv_p = round(($forced_customer_gsv/$force_total_sales) * 100,2);
						}
						$scheme_alloc->forced_sold_to_gsv = $forced_customer_gsv;  
					}   
				}

			}else{

				$scheme_alloc->sold_to_gsv = 0;    
			}
			$scheme_alloc->sold_to_gsv_p = $sold_to_gsv_p;
			$scheme_alloc->forced_sold_to_gsv_p = $fore_sold_to_gsv_p;

			$_sold_to_alloc = 0;

			$c_multi = 0;
			if(($customer->gsv > 0) && ($total_sales > 0)){
			   $c_multi = $customer->gsv/$total_sales;
			}

			if($total_sales > 0){
				$_sold_to_alloc = round($c_multi * $scheme->quantity);
			}
			$scheme_alloc->sold_to_alloc = $_sold_to_alloc;
			
			$scheme_alloc->computed_alloc = 0;
			if($_sold_to_alloc > 0){
				$scheme_alloc->computed_alloc = $_sold_to_alloc;
			}
			$scheme_alloc->final_alloc = 0;

			if($force_alloc){
				$forced_sold_to_alloc = 0;
				$forced_c_multi = 0;
				if(($scheme_alloc->forced_sold_to_gsv > 0) && ($force_total_sales > 0)){
				   $forced_c_multi = $scheme_alloc->forced_sold_to_gsv/$force_total_sales;
				}

				if($force_total_sales > 0){
					$forced_sold_to_alloc = round($forced_c_multi * $scheme->quantity);
				}
				$scheme_alloc->forced_sold_to_alloc = $forced_sold_to_alloc;
				$scheme_alloc->force_alloc = $forced_sold_to_alloc;
				$scheme_alloc->final_alloc = $forced_sold_to_alloc;
				$scheme_alloc->multi = $forced_c_multi;
			}else{
				$scheme_alloc->force_alloc = 0;
				$scheme_alloc->final_alloc = $_sold_to_alloc;
				$scheme_alloc->multi = $c_multi;
			}
			
			$in_deals = 0;
			$in_cases = 0;
			if($scheme->activity->activitytype->uom == 'CASES'){
				$in_deals = $scheme_alloc->final_alloc * $scheme->deals;
				$in_cases = $scheme_alloc->final_alloc;
				$tts_budget = $scheme_alloc->final_alloc * $scheme->deals * $scheme->srp_p; 
			}else{
				if($scheme_alloc->final_alloc > 0){
					$in_cases = round($scheme_alloc->final_alloc/$scheme->deals);
					$in_deals =  $scheme_alloc->final_alloc;
				}
				$tts_budget = $scheme_alloc->final_alloc * $scheme->srp_p;
			}

			$scheme_alloc->in_deals = $in_deals;
			$scheme_alloc->in_cases = $in_cases;
			$scheme_alloc->tts_budget = $tts_budget;
			$scheme_alloc->pe_budget = $scheme_alloc->final_alloc *  $scheme->other_cost;

			if(empty($customer->shiptos)){
				$scheme_alloc->show = true;
			}


			$scheme_alloc->save();

			if(!empty($customer->shiptos)){
				foreach($customer->shiptos as $shipto){
					$shipto_alloc = 0;
					$shipto_alloc = new SchemeAllocation;
					$shipto_alloc->scheme_id = $scheme->id;
					$shipto_alloc->customer_id = $scheme_alloc->id;

					$shipto_alloc->group_code = $customer->group_code;
					$shipto_alloc->group = $customer->group_name;

					$shipto_alloc->area_code = $customer->area_code;
					$shipto_alloc->area = $customer->area_name;

					$shipto_alloc->sold_to_code = $customer->customer_code;
					$shipto_alloc->sob_customer_code = $customer->sob_customer_code;
					$shipto_alloc->sold_to = $customer->customer_name;

					$shipto_alloc->ship_to_code = $shipto['ship_to_code'];
					$shipto_alloc->ship_to = $shipto['ship_to_name'];


					if($shipto['gsv'] >0){
						$shipto_alloc->ship_to_gsv = $shipto['gsv'];
					}else{
						$shipto_alloc->ship_to_gsv = 0;
					}

					$shipto_alloc->forced_ship_to_gsv = 0;

					if($force_alloc){
						if($shipto['gsv'] > 0){
							if(array_key_exists($customer->area_code, $forced_areas)){								
								$shipto_alloc->forced_ship_to_gsv = $shipto['gsv'] * $forced_areas[$customer->area_code];
							}   
						}
					}


					
					$_shipto_alloc = 0;
					$s_multi = 0;
					if(!is_null($shipto['split'])){
						if($scheme_alloc->sold_to_alloc > 0){
							$s_multi = $shipto['split'] / 100;
						}
					}else{
						if($shipto['gsv'] >0){
							if(empty($customer->area_code_two)){
								$s_multi = $shipto['gsv'] / $customer->ado_total;
							}else{
								$s_multi = 1;
							}
						}
					}

					$_shipto_alloc = round($s_multi  * $scheme_alloc->sold_to_alloc);

					$shipto_alloc->ship_to_alloc = $_shipto_alloc;
					$shipto_alloc->multi = $s_multi;
					$shipto_alloc->computed_alloc = 0;
					if($_shipto_alloc > 0){
						$shipto_alloc->computed_alloc = $_shipto_alloc;
					}
					
					$fs_multi = 0;
					$shipto_alloc->forced_ship_to_alloc = 0;
					if($force_alloc){
						
						if(!is_null($shipto['split'])){
							if($scheme_alloc->sold_to_alloc > 0){
								$fs_multi = $shipto['split'] / 100;
							}
						}else{
							if( $shipto_alloc->forced_ship_to_gsv >0){
								if(empty($customer->area_code_two)){
									$fs_multi = $shipto_alloc->forced_ship_to_gsv / $customer->forced_ado_total;
								}else{
									$fs_multi = 1;
								}
							}
							// if(($shipto_alloc->forced_ship_to_gsv >0) && ($customer->forced_ado_total > 0)){
							// 	$fs_multi = round($shipto_alloc->forced_ship_to_gsv / $customer->forced_ado_total,2);
							// }
						}

						$f_shipto_alloc = round($fs_multi  * $scheme_alloc->force_alloc);

						$shipto_alloc->forced_ship_to_alloc	= $f_shipto_alloc;

						$shipto_alloc->force_alloc = $f_shipto_alloc;
						$shipto_alloc->final_alloc = $f_shipto_alloc;
					}else{
						$shipto_alloc->force_alloc = 0;
						$shipto_alloc->final_alloc = $_shipto_alloc;
					}
					$shipto_alloc->forced_ship_to_gsv_p = $fs_multi;

					$in_deals = 0;
					$in_cases = 0;
					if($scheme->activity->activitytype->uom == 'CASES'){
						$in_deals = $shipto_alloc->final_alloc * $scheme->deals;
						$in_cases = $shipto_alloc->final_alloc;
						$tts_budget = $shipto_alloc->final_alloc * $scheme->deals * $scheme->srp_p; 
					}else{
						if($shipto_alloc->final_alloc > 0){
							$in_cases = round($shipto_alloc->final_alloc/$scheme->deals);
							$in_deals =  $shipto_alloc->final_alloc;
						}
						$tts_budget = $shipto_alloc->final_alloc * $scheme->srp_p; 
					}
				  
					$shipto_alloc->in_deals = $in_deals;
					$shipto_alloc->in_cases = $in_cases;
					$shipto_alloc->tts_budget = $tts_budget;
					$shipto_alloc->pe_budget = $shipto_alloc->final_alloc *  $scheme->other_cost;
				   	
				   	if($shipto_alloc->multi == 0){
				   		if(($shipto['split'] != null) && ($shipto['split'] > 0)){
				   			$shipto_alloc->multi = $shipto['split']/100;
				   		}
				   		
				   	}

				   	if(empty($shipto['accounts'])){
				   		$shipto_alloc->show = true;
				   	}
				   	
					$shipto_alloc->save();  

					if(!empty($shipto['accounts'])){
						$others = $shipto_alloc->ship_to_alloc;
						$fothers = $shipto_alloc->force_alloc;
						foreach($shipto['accounts'] as $account){
							$account_alloc = new SchemeAllocation;
							$account_alloc->scheme_id = $scheme->id;
							$account_alloc->customer_id = $scheme_alloc->id;
							$account_alloc->shipto_id = $shipto_alloc->id;

							$account_alloc->group_code = $customer->group_code;
							$account_alloc->group = $customer->group_name;

							$account_alloc->area_code = $customer->area_code;
							$account_alloc->area = $customer->area_name;

							$account_alloc->sold_to_code = $customer->customer_code;
							$account_alloc->sob_customer_code = $customer->sob_customer_code;
							$account_alloc->sold_to = $customer->customer_name;

							$account_alloc->ship_to_code = $shipto['ship_to_code'];
							$account_alloc->ship_to = $shipto['ship_to_name'];

							$account_alloc->channel_code = $account['channel_code'];
							$account_alloc->channel = $account['channel_name'];

							$account_alloc->account_group_code = $account['account_group_code'];
							$account_alloc->account_group_name = $account['account_group_name'];

							$account_alloc->outlet = $account['account_name'];


							if($account['gsv'] > 0){
								$account_alloc->outlet_to_gsv = $account['gsv'];
							}else{
								$account_alloc->outlet_to_gsv = 0;
							}

							if($force_alloc){
								if($account['gsv'] > 0){
									if(array_key_exists($customer->area_code, $forced_areas)){								
										$account_alloc->forced_outlet_to_gsv = $account['gsv'] * $forced_areas[$customer->area_code];
									}   
								}
							}


						   
							$p = 0;
							$f_p = 0;
							if($customer->gsv > 0){
								$x = round($account['gsv']/$customer->gsv * 100,5);
								if($x > 0){
									$p = $x;
								}
							}

							if($force_alloc){
								if(($scheme_alloc->forced_sold_to_gsv > 0) && ($account_alloc->forced_outlet_to_gsv > 0)){
									$f_p = round($account_alloc->forced_outlet_to_gsv/$scheme_alloc->forced_sold_to_gsv * 100,5);
								}
							}

							$account_alloc->outlet_to_gsv_p = $p;
							$account_alloc->forced_outlet_to_gsv_p = $f_p;

							$_account_alloc = round(($p * $shipto_alloc->ship_to_alloc)/100);
							$account_alloc->outlet_to_alloc = $_account_alloc;
							if($_account_alloc > 0){
								$others -= $_account_alloc;
							}
							$account_alloc->multi = $p/100;
							$account_alloc->computed_alloc = 0;
							if($_account_alloc > 0){
								$account_alloc->computed_alloc = $_account_alloc;
							}
							
							if($force_alloc){
								$forced_account_alloc = round(($f_p * $shipto_alloc->forced_ship_to_alloc)/100);
								if($forced_account_alloc > 0){
									$fothers -= $forced_account_alloc;
								}
								$account_alloc->forced_outlet_to_alloc = $forced_account_alloc;
								$account_alloc->force_alloc = $forced_account_alloc;
								$account_alloc->final_alloc = $forced_account_alloc;
							}else{
								$account_alloc->forced_outlet_to_alloc = 0;
								$account_alloc->force_alloc = 0;
								$account_alloc->final_alloc = $_account_alloc;
							}

							
							$in_deals = 0;
							$in_cases = 0;
							if($scheme->activity->activitytype->uom == 'CASES'){
								$in_deals = $account_alloc->final_alloc * $scheme->deals;
								$in_cases = $account_alloc->final_alloc;
								$tts_budget = $account_alloc->final_alloc * $scheme->deals * $scheme->srp_p; 
							}else{
								if($account_alloc->final_alloc > 0){
									$in_cases = round($account_alloc->final_alloc/$scheme->deals);
									$in_deals =  $account_alloc->final_alloc;
								}
								$tts_budget = $account_alloc->final_alloc * $scheme->srp_p; 
							}

							$account_alloc->in_deals = $in_deals;
							$account_alloc->in_cases = $in_cases;
							$account_alloc->tts_budget = $tts_budget;
							$account_alloc->pe_budget = $account_alloc->final_alloc *  $scheme->other_cost;   
							$account_alloc->show = true;
							$account_alloc->save();
						}

						if(empty($customer->area_code_two)){
							$_others_alloc = 0;
							$f_others_alloc = 0;
							$others_alloc = new SchemeAllocation;
							$others_alloc->scheme_id = $scheme->id;
							$others_alloc->customer_id = $scheme_alloc->id;
							$others_alloc->shipto_id = $shipto_alloc->id;

							$others_alloc->group_code = $customer->group_code;
							$others_alloc->group = $customer->group_name;

							$others_alloc->area_code = $customer->area_code;
							$others_alloc->area = $customer->area_name;

							$others_alloc->sold_to_code = $customer->customer_code;
							$others_alloc->sob_customer_code = $customer->sob_customer_code;
							$others_alloc->sold_to = $customer->customer_name;

							$others_alloc->ship_to_code = $shipto['ship_to_code'];
							$others_alloc->ship_to = $shipto['ship_to_name'];	

							$others_alloc->channel_code = 'OTHERS';
							$others_alloc->channel = 'OTHERS';				
							
							$others_alloc->outlet = 'OTHERS';
							// $others_alloc->outlet_to_gsv = $account['gsv'];
							$others_alloc->outlet_to_gsv = 0;
							if($others > 0){
								$_others_alloc = $others;
							}
							$others_alloc->outlet_to_alloc = 0;

							if($_others_alloc > 0){
								$others_alloc->outlet_to_alloc = $_others_alloc;
							}
							

							if(($_others_alloc > 0) && ($account_alloc->final_alloc > 0)){
								$others_alloc->multi = $_others_alloc/$account_alloc->final_alloc;
							}else{
								$others_alloc->multi = 0;
							}
							$others_alloc->computed_alloc = $_others_alloc;

							if($force_alloc){
								if($fothers > 0){
									$f_others_alloc = $fothers;
								}
								$others_alloc->force_alloc = $f_others_alloc;
								$others_alloc->final_alloc = $f_others_alloc;

							}else{
								$others_alloc->force_alloc = 0;
								$others_alloc->final_alloc = $_others_alloc;
							}


							$in_deals = 0;
							$in_cases = 0;
							if($scheme->activity->activitytype->uom == 'CASES'){
								$in_deals = $others_alloc->final_alloc * $scheme->deals;
								$in_cases = $others_alloc->final_alloc;
								$tts_budget = $others_alloc->final_alloc * $scheme->deals * $scheme->srp_p; 
							}else{
								if($others_alloc->final_alloc > 0){
									$in_cases = round($others_alloc->final_alloc/$scheme->deals);
									$in_deals =  $others_alloc->final_alloc;
								}
								$tts_budget = $others_alloc->final_alloc * $scheme->srp_p; 
							}
							$others_alloc->in_deals = $in_deals;
							$others_alloc->in_cases = $in_cases;
							$others_alloc->tts_budget = $tts_budget;
							$others_alloc->pe_budget = $others_alloc->final_alloc *  $scheme->other_cost; 
							$others_alloc->show = true;
							$others_alloc->save();
						}
						
					}
				}
			}
		}
	}

	public static function updateCosting($scheme){
		$allocations = SchemeAllocation::where('scheme_id',$scheme->id)->get();
		foreach ($allocations as $allocation) {
			if($scheme->activity->activitytype->uom == 'CASES'){
				$tts_budget = $allocation->final_alloc * $scheme->deals * $scheme->srp_p; 
			}else{
				$tts_budget = $allocation->final_alloc * $scheme->srp_p; 
			}

			$allocation->tts_budget = $tts_budget;
			$allocation->pe_budget = $allocation->final_alloc *  $scheme->other_cost; 

			$allocation->update();
		}
	}
}
