<?php

class AllocationRepository  {
	private $mt_total_sales = 0;
	private $dt_total_sales = 0;
	private $_mt_primary_sales = array();
	private $_dt_secondary_sales = array();

	public function __construct()  {
      	
    }


	public function customers($skus, $channels, $selected_customers){
		$salescources = DB::table('split_old_customers')->get();

		$customers = DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name','customer_name','customer_code','customers.area_code as area_code')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->join('groups', 'areas.group_code', '=', 'groups.group_code')
			->where('customers.active', 1)
			->orderBy('customers.id')
			->get();

		// get all ship to
		$_shiptos = DB::table('ship_tos')
			->select('customer_code','ship_to_code','ship_to_name','split')
			->where('ship_tos.active', 1)
			->get();

		// get all account
		$_accounts = DB::table('accounts')
			->select('accounts.id','ship_to_code','area_code', 'account_name', 'channel_name','accounts.account_group_code')
			->join('channels', 'accounts.channel_code', '=', 'channels.channel_code')
			->join('account_groups', 'accounts.account_group_code', '=', 'account_groups.account_group_code')
			->get();

		// get all outlet
		$_outlets = DB::table('outlets')->get();	

		// get all child skus
		$child_sku = DB::table('mother_child_skus')
			->select('child_sku')
			->whereIn('mother_sku',$skus)->get();

		// merge main and child skus
		$data = array();
		if(count($child_sku)>0){
			foreach ($child_sku as $value) {
				$data[] = $value->child_sku;
			}
		}
		$child_skus = array_merge($data, $skus);

		$_grps = array();
		$_areas = array();
		$_cust = array();
		$_shp = array();
		$_otlts = array();
		if(!empty($selected_customers)){
			foreach ($selected_customers as $selected_customer) {
				$_selected_customer = explode(".", $selected_customer);
				$_grps[] = $_selected_customer[0];
				if(!empty($_selected_customer[1])){
					$_areas[$_selected_customer[0]][] = $_selected_customer[1];
				}

				if(!empty($_selected_customer[2])){
					$_cust[$_selected_customer[1]][] = $_selected_customer[2];
				}

				if(!empty($_selected_customer[3])){
					$_shp[$_selected_customer[2]][] = $_selected_customer[3];
				}

				if(!empty($_selected_customer[4])){
					$_otlts[$_selected_customer[3]][] = $_selected_customer[4];
				}
			}
		}
		
		// print_r($areas);
		// get all MT Primary Sales
		if(in_array("E1398", $_grps)){
			$this->_mt_primary_sales = DB::table('mt_primary_sales')
					->select(DB::raw("mt_primary_sales.area_code,mt_primary_sales.customer_code, SUM(gsv) as gsv"))
					->join('customers', 'mt_primary_sales.customer_code', '=', 'customers.customer_code')
					->whereIn('child_sku_code', $child_skus)
					->where(function($query) use ($_areas) {
						if(!empty($_areas)){
							$query->whereIn('mt_primary_sales.area_code', $_areas['E1398']);
						}
						
					})
					->groupBy(array('mt_primary_sales.area_code','mt_primary_sales.customer_code'))
					->get();
		}
		
		
		
		if(in_array("E1397", $_grps)){	
		// get all DT Secondary Sales
		$this->_dt_secondary_sales =DB::table('dt_secondary_sales')
					->select(DB::raw("dt_secondary_sales.area_code,dt_secondary_sales.customer_code, SUM(gsv) as gsv"))
					->join('sub_channels', 'dt_secondary_sales.coc_03_code', '=', 'sub_channels.coc_03_code')
					->join('customers', 'dt_secondary_sales.customer_code', '=', 'customers.customer_code')
					->whereIn('child_sku_code', $child_skus)
					->whereIn('channel_code', $channels)
					->where(function($query) use ($_areas) {
						if(!empty($_areas)){
							$query->whereIn('dt_secondary_sales.area_code', $_areas['E1397']);
						}
						
					})

					->groupBy(array('dt_secondary_sales.area_code','dt_secondary_sales.customer_code'))
					->get();
		}

		// get Ship To Sales
		$_ship_to_sales = DB::table('ship_to_sales')
					->select(DB::raw("ship_to_code, SUM(gsv) as gsv"))
					->whereIn('child_sku_code', $child_skus)
					->groupBy('ship_to_code')
					->get();	

		// get Outlet Sales
		$_outlet_sales = DB::table('outlet_sales')
					->select('area_code','customer_code','account_name','outlet_code','gsv')
					->join('sub_channels', 'outlet_sales.coc_03_code', '=', 'sub_channels.coc_03_code')
					->whereIn('child_sku_code', $child_skus)
					->whereIn('channel_code', $channels)
					->get();	

		$data = array();
		foreach ($customers as $customer) {
			$ado_total = 0;
			foreach ($_shiptos as $_shipto) {
				if($customer->customer_code == $_shipto->customer_code){
					if(!is_null($_shipto->ship_to_code)){
						unset($_shipto->accounts);
						foreach ($_accounts as $_account) {
							if(($_account->area_code == $customer->area_code) && ($_account->ship_to_code == $_shipto->ship_to_code)){
								$outlets = array();
								$gsv = 0;
								foreach ($_outlets as $_outlet) {
									if(($_outlet->area_code == $_account->area_code) &&
										($_outlet->ship_to_code == $_account->ship_to_code) &&
										($_outlet->account_name == $_account->account_name)){

										foreach ($_outlet_sales as $key => $_outlet_sale) {
											if(($_outlet_sale->outlet_code == $_outlet->outlet_code) &&
												($_outlet_sale->area_code == $_outlet->area_code) &&
												($_outlet_sale->account_name == $_outlet->account_name) &&
												($_outlet_sale->customer_code == $_outlet->customer_code)){

												if(in_array($customer->group_code, $_grps)){
													if(!empty($_areas[$customer->group_code])){
														if(in_array($customer->area_code, $_areas[$customer->group_code])){
															if(!empty($_cust[$customer->area_code])){
																if(in_array($customer->customer_code, $_cust[$customer->area_code])){
																	if(!empty($_shp[$customer->customer_code])){
																		if(in_array($_shipto->ship_to_code, $_shp[$customer->customer_code])){
																			if(!empty($_otlts[$_shipto->ship_to_code])){
																				if(in_array($_account->id, $_otlts[$_shipto->ship_to_code])){
																					$gsv +=  $_outlet_sale->gsv;
																				}
																			}else{
																				$gsv +=  $_outlet_sale->gsv;
																			}
																		}
																	}else{
																		$gsv +=  $_outlet_sale->gsv;
																	}
																}
															}else{
																$gsv +=  $_outlet_sale->gsv;
															}
														}
													}else{
														$gsv +=  $_outlet_sale->gsv;
													}
												}

												
											}
										}	
									}
								}
								$_account->gsv = $gsv;
								// print_r($_account);
								$_shipto->accounts[] = (array) $_account;
							}
						}
					}
					$_shipto->area_code = $customer->area_code;

					// start ship to sales
					$abort_shipto = false;
					$_shipto->gsv = '';
					foreach ($_ship_to_sales as $_ship_to_sale) {
						if($_shipto->ship_to_code == $_ship_to_sale->ship_to_code){
							// $_shipto->gsv = $_ship_to_sale->gsv;
							if(in_array($customer->group_code, $_grps)){
								if(!empty($_areas[$customer->group_code])){
									if(in_array($customer->area_code, $_areas[$customer->group_code])){
										if(!empty($_cust[$customer->area_code])){
											if(in_array($customer->customer_code, $_cust[$customer->area_code])){
												if(!empty($_shp[$customer->customer_code])){
													if(in_array($_shipto->ship_to_code, $_shp[$customer->customer_code])){
														$_shipto->gsv = $_ship_to_sale->gsv;
													}
												}else{
													$_shipto->gsv = $_ship_to_sale->gsv;
												}
											}
										}else{
											$_shipto->gsv = $_ship_to_sale->gsv;
										}
									}
								}else{
									$_shipto->gsv = $_ship_to_sale->gsv;
								}
							}
							
							// $ado_total += $_ship_to_sale->gsv;
							$ado_total += $_shipto->gsv;
							$abort_shipto = true;
						}
						
						if ($abort_shipto === true) break;
					}
					// end ship to sales
				
					$customer->shiptos[] = (array)	$_shipto;

					$customer->ado_total = $ado_total;
				}else{

				}

			}

			if($customer->group_code == 'E1397'){
				$abort = false;
				$customer->gsv = 0;
				foreach ($this->_dt_secondary_sales as $_dt_secondary_sale) {
					// check if selected
					if(!empty($_cust[$customer->area_code])){
						if(in_array($customer->customer_code, $_cust[$customer->area_code])){
							$_c_gsv = self::customer_gsv($abort, $customer, $_dt_secondary_sale, $salescources, $this->_dt_secondary_sales);
							$customer->gsv = $_c_gsv['customer_gsv'];
							if ($_c_gsv['abort'] === true) 
							{
								if($customer->gsv > 0){
									$this->dt_total_sales += $customer->gsv;
								}
								break;
							}
						}
						
					}else{
						$_c_gsv = self::customer_gsv($abort, $customer, $_dt_secondary_sale, $salescources, $this->_dt_secondary_sales);
						$customer->gsv = $_c_gsv['customer_gsv'];
						if ($_c_gsv['abort'] === true) 
						{
							if($customer->gsv > 0){
								$this->dt_total_sales += $customer->gsv;
							}
							break;
						}
						// if(($customer->customer_code == $_dt_secondary_sale->customer_code) && ($customer->area_code == $_dt_secondary_sale->area_code)){
						// 	$customer->gsv = $_dt_secondary_sale->gsv;
						// 	$abort = true;
						// }else{
						// 	$customer->gsv = 0;
						// }

						// $customer->gsv += self::additonal_sales($salescources,$customer->customer_code,$this->_dt_secondary_sales);

						// if ($abort === true) 
						// {
						// 	if($customer->gsv > 0){
						// 		$this->dt_total_sales += $customer->gsv;
						// 	}
						// 	break;
						// }
					}
					
				}

			}else{
				$abort = false;
				$customer->gsv = 0;
				foreach ($this->_mt_primary_sales as $_mt_primary_sale) {
					// check if selected
					if(!empty($_cust[$customer->area_code])){
						if(in_array($customer->customer_code, $_cust[$customer->area_code])){
							$_c_gsv = self::customer_gsv($abort, $customer, $_mt_primary_sale, $salescources, $this->_mt_primary_sales);
						
							$customer->gsv = $_c_gsv['customer_gsv'];
							if ($_c_gsv['abort'] === true) 
							{
								if($customer->gsv > 0){
									$this->mt_total_sales += $customer->gsv;
								}
								break;
							}
							// if(($customer->customer_code == $_mt_primary_sale->customer_code) && ($customer->area_code == $_mt_primary_sale->area_code)){
							// 	$customer->gsv = $_mt_primary_sale->gsv;
							// 	$abort = true;
							// }else{
							// 	$customer->gsv = 0;
							// }

							// $customer->gsv += self::additonal_sales($salescources,$customer->customer_code,$this->_mt_primary_sales);

							// if ($abort === true) 
							// {
							// 	if($customer->gsv > 0){
							// 		$this->mt_total_sales += $customer->gsv;
							// 	}
							// 	break;
							// }
						}
						
					}else{
						$_c_gsv = self::customer_gsv($abort, $customer, $_mt_primary_sale, $salescources, $this->_mt_primary_sales);

						$customer->gsv = $_c_gsv['customer_gsv'];
						if ($_c_gsv['abort'] === true) 
						{
							if($customer->gsv > 0){
								$this->mt_total_sales += $customer->gsv;
							}
							break;
						}
					}
					// end check if selected
				}
			}
			$data[] = (array)$customer;
		}

		// $_grps = array();
		// $_areas = array();
		// $_cust = array();
		// $_shp = array();

		// echo '<pre>';
		// print_r($_grps);
		// print_r($_areas);
		// print_r($_cust);
		// print_r($_shp);
		// print_r($_otlts);
		// echo '</pre>';

		return $customers;
	}

	public function customer_gsv($abort, $customer, $sale, $salescources,$fromsales){
		$customer_gsv = 0;
		if(($customer->customer_code == $sale->customer_code) && ($customer->area_code == $sale->area_code)){
			$customer_gsv = $sale->gsv;
			$abort = true;
		}

		$customer_gsv += self::additonal_sales($salescources,$customer->customer_code,$fromsales);

		$data = array('customer_gsv' => $customer_gsv, 'abort' => $abort);
		return $data;
	}

	public function total_sales(){
		return $this->dt_total_sales + $this->mt_total_sales;
	}

	public function additonal_sales($salescources,$customer_code,$sales){
		$additonal_sales = 0;
		foreach ($salescources as $salescource) {
			if($salescource->active_customer_code == $customer_code){
				foreach ($sales as $sale) {
					if($sale->customer_code == $salescource->inactive_customer_code){
						$additonal_sales += ($sale->gsv * $salescource->split) / 100;
					}
				}
			}
		}

		return $additonal_sales;
	}

	public function allocation_summary(){
		$groups = \DB::table('groups')->get();
		$data = array();
		foreach ($groups as $group) {
			$areas = \DB::table('customers')
			->select('areas.group_code as group_code','customers.area_code as area_code','area_name')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->where('areas.group_code',$group->group_code)
			->where('customers.active', 1)
			->groupBy('customers.area_code')
			->orderBy('areas.id')
			->get();
			// foreach ($areas as $area) {
			// 	$customers = \DB::table('customers')
			// 		->where('area_code',$area->area_code)
			// 		->where('customers.active', 1)
			// 		->get();
			// }
			$group->areas = $areas;
		}

		return $groups;
	}

}