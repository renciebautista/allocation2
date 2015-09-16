<?php

class AllocationRepository  {
	private $mt_total_sales = 0;
	private $dt_total_sales = 0;
	private $total_gsv = 0;
	private $force_total_gsv = 0;
	private $_mt_primary_sales = array();
	private $_dt_secondary_sales = array();

	private $_customers = array();
	private $_customers_list = array();
	private $area_sales = array();
	
	public function __construct()  {
      	
    }


	public function customers($skus, $selected_channels, $selected_customers,$forced_areas){

		$this->_customers = $selected_customers;
		$salescources = DB::table('split_old_customers')->get();

		$customers_list =  DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name',
				'customer_name','customer_code','customers.area_code as area_code',
				'customers.area_code_two as area_code_two','multiplier','active')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->join('groups', 'areas.group_code', '=', 'groups.group_code')
			// ->where('customers.active', 1)
			->orderBy('areas.id')
			->orderBy('customers.id')
			->get();

		$customers =  DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name',
				'customer_name','customer_code','customers.area_code as area_code',
				'customers.area_code_two as area_code_two','multiplier','active', 'from_dt')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->join('groups', 'areas.group_code', '=', 'groups.group_code')
			->where('customers.active', 1)
			->orderBy('areas.id')
			->orderBy('customers.id')
			->get();

		// get all ship to
		$_shiptos = DB::table('ship_tos')
			->select('customer_code','ship_to_code','ship_to_name','split')
			->where('ship_tos.active', 1)
			->get();

		$_shiptos_list = DB::table('ship_tos')
			->select('customer_code','ship_to_code','ship_to_name','split')
			// ->where('ship_tos.active', 1)
			->get();

		// get all account
		$_accounts = DB::table('accounts')
			->select('accounts.id','ship_to_code','area_code', 'account_name', 'channel_name','accounts.account_group_code','channels.channel_code','account_groups.account_group_name')
			->join('channels', 'accounts.channel_code', '=', 'channels.channel_code')
			->join('account_groups', 'accounts.account_group_code', '=', 'account_groups.account_group_code')
			->where('active',1)
			->get();

		$_accounts_list = DB::table('accounts')
			->select('accounts.id','ship_to_code','area_code', 'account_name', 'channel_name','accounts.account_group_code')
			->join('channels', 'accounts.channel_code', '=', 'channels.channel_code')
			->join('account_groups', 'accounts.account_group_code', '=', 'account_groups.account_group_code')
			// ->where('active',1)
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
		if(!empty($skus)){
			$child_skus = array_merge($data, $skus);
		}else{
			$child_skus = $data;
		}
		

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

		$channels = array();
		$_chgrp = array();
		if(!empty($selected_channels)){
			foreach ($selected_channels as $channel_node) {
				$_selected_node = explode(".", $channel_node);
				$channels[] = $_selected_node[0];
				if(!empty($_selected_node[1])){
					$_chgrp[$_selected_node[0]][] = $_selected_node[1];
				}
			}
		}
		// Helper::print_r($_chgrp);
		// get all MT Primary Sales
		if(in_array("E1398", $_grps)){
			$this->_mt_primary_sales = DB::table('mt_primary_sales')
					->select(DB::raw("mt_primary_sales.area_code,mt_primary_sales.customer_code, SUM(gsv) as gsv"))
					->join(DB::raw("(SELECT DISTINCT(customer_code) FROM customers) customers"), 'mt_primary_sales.customer_code', '=', 'customers.customer_code')
					->whereIn('child_sku_code', $child_skus)
					->where(function($query) use ($_areas) {
						if(!empty($_areas['E1398'])){
							$query->whereIn('mt_primary_sales.area_code', $_areas['E1398']);
						}
						
					})
					->groupBy(array('mt_primary_sales.area_code','mt_primary_sales.customer_code'))
					->get();
		}
		
		
		
		if(in_array("E1397", $_grps)){	
		// get all DT Secondary Sales
		// dd($channels);
		$this->_dt_secondary_sales =DB::table('dt_secondary_sales')
					->select(DB::raw("dt_secondary_sales.area_code,dt_secondary_sales.customer_code, SUM(gsv) as gsv"))
					->join('sub_channels', 'dt_secondary_sales.coc_03_code', '=', 'sub_channels.coc_03_code')
					->join(DB::raw("(SELECT DISTINCT(customer_code) FROM customers) customers"), 'dt_secondary_sales.customer_code', '=', 'customers.customer_code')
					->whereIn('child_sku_code', $child_skus)
					// ->whereIn('channel_code', $channels)
					->where(function($query) use ($channels) {
						if(!empty($channels)){
							$query->whereIn('channel_code', $channels);
						}		
					})
					->where(function($query) use ($_areas) {
						if(!empty($_areas['E1397'])){
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
					// ->whereIn('channel_code', $channels)
					->get();	

		$data = array();
		foreach ($customers as $customer) {
			if($customer->active == 1){
				$ado_total = 0;
				$forced_ado_total = 0;
				$total_account_gsv = 0;
				foreach ($_shiptos as $_shipto) {
					if($customer->customer_code == $_shipto->customer_code){

						if(!is_null($_shipto->ship_to_code)){

							unset($_shipto->accounts);
							foreach ($_accounts as $_account) {

								if(($_account->area_code == $customer->area_code) && ($_account->ship_to_code == $_shipto->ship_to_code)){
									$outlets = array();
									$gsv = 0;

									foreach ($_outlets as $_outlet) {
										$account_area_code = $_account->area_code;
										if(!empty($customer->area_code_two)){
											$account_area_code = $customer->area_code_two;		
										}

										if(($_outlet->area_code == $account_area_code) &&
											($_outlet->ship_to_code == $_account->ship_to_code) &&
											($_outlet->account_name == $_account->account_name)){
											
											foreach ($_outlet_sales as $key => $_outlet_sale) {
												if(($_outlet_sale->outlet_code == $_outlet->outlet_code) &&
													($_outlet_sale->area_code == $_outlet->area_code) &&
													($_outlet_sale->account_name == $_outlet->account_name) &&
													($_outlet_sale->customer_code == $_outlet->customer_code)){

													if($customer->from_dt == 1){
														if(!empty($_chgrp)){
															if((isset($_chgrp[$_account->channel_code])) && ($_chgrp[$_account->channel_code][0] == "OTHERS")){
																if(($_account->account_group_code == 'AG1') || ($_account->account_group_code == 'AG6')){

																}else{
																	self::get_gsv($gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$_outlet_sale);
																}
															}else{
																self::get_gsv($gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$_outlet_sale);
															}
														}else{
															self::get_gsv($gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$_outlet_sale);
														}
														
													}else{
														if(in_array($_account->channel_code, $channels)){
															if(!empty($_chgrp)){
																if((isset($_chgrp[$_account->channel_code])) && ($_chgrp[$_account->channel_code][0] == "OTHERS")){
																	if(($_account->account_group_code == 'AG1') || ($_account->account_group_code == 'AG6')){

																	}else{
																		self::get_gsv($gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$_outlet_sale);
																	}
																}else{	
																	self::get_gsv($gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$_outlet_sale);
																}
															}else{
																self::get_gsv($gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$_outlet_sale);
															}
															
														}
														
													}
													
												}
											}
										}
										// $gsv +=  100;
									}
									$_account->gsv = $gsv;
									$additional_gsv = 0;

									if(!empty($_chgrp)){
										if((isset($_chgrp[$_account->channel_code])) && ($_chgrp[$_account->channel_code][0] == "OTHERS")){
											if(($_account->account_group_code == 'AG1') || ($_account->account_group_code == 'AG6')){

											}else{
												self::get_additional_gsv($additional_gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$salescources,$customers_list,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales);
											}
											
										}else{	
											if((isset($_chgrp[$_account->channel_code])) && ($_chgrp[$_account->channel_code][0] == $_account->account_group_code)){
												self::get_additional_gsv($additional_gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$salescources,$customers_list,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales);
											}
										}
									}else{
										self::get_additional_gsv($additional_gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$salescources,$customers_list,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales);
									}

									
									
									// if($customer->customer_code == 'E53617'){
										// $additional_gsv = self::additonal_outlet_sales($salescources,$customers_list,$customer->customer_code,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales,$_account->account_name);
										// echo $customer->customer_code. '<br>';
										// echo $customer->customer_name. ' => '.$_account->account_name .' => '.$_account->gsv. ' + '.$additional_gsv.'<br>';
									// }
									

									$_account->gsv += $additional_gsv;
									$total_account_gsv += $_account->gsv;
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
								
								if(array_key_exists($customer->area_code, $forced_areas)){
									 $forced_ado_total += $_shipto->gsv * $forced_areas[$customer->area_code];
								}
								
								$ado_total += $_shipto->gsv;
								$abort_shipto = true;
							}
							
							if ($abort_shipto === true) break;
						}
						// end ship to sales

						if(!empty($customer->area_code_two)){
							$_shipto->gsv = $total_account_gsv;
						}
					
						$customer->shiptos[] = (array)	$_shipto;

						$customer->ado_total = $ado_total;
						
						$customer->forced_ado_total = $forced_ado_total;

						// if(array_key_exists($customer->area_code, $forced_areas)){
						// 	 $customer->forced_ado_total = $forced_ado_total * $forced_areas[$customer->area_code];
						// }

					}else{

					}

				}

				if($customer->group_code == 'E1397'){
					$abort = false;
					$customer->gsv = 0;
					if(empty($customer->area_code_two)){
						foreach ($this->_dt_secondary_sales as $_dt_secondary_sale) {
							// check if selected
							if(!empty($_cust[$customer->area_code])){
								if(in_array($customer->customer_code, $_cust[$customer->area_code])){
									$_c_gsv = self::customer_gsv($abort, $customer, $_dt_secondary_sale, $salescources, $this->_dt_secondary_sales);
									$customer->gsv =  ($_c_gsv['customer_gsv'] * $customer->multiplier ) / 100;
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
								$customer->gsv =  ($_c_gsv['customer_gsv'] * $customer->multiplier ) / 100;
								if ($_c_gsv['abort'] === true) 
								{
									if($customer->gsv > 0){
										$this->dt_total_sales += $customer->gsv;
									}
									break;
								}
							}
						}
					}else{
						$customer->gsv = $total_account_gsv;
					}

					if(array_key_exists($customer->area_code, $forced_areas)){
						$this->force_total_gsv += $customer->gsv * $forced_areas[$customer->area_code];
					}
					$this->total_gsv += $customer->gsv;

					if(!isset($this->area_sales[$customer->area_code])){
						$this->area_sales[$customer->area_code] = 0;
					}

					$this->area_sales[$customer->area_code] += $customer->gsv;

				}else{
					$abort = false;
					$customer->gsv = 0;
					if(empty($customer->area_code_two)){
						foreach ($this->_mt_primary_sales as $_mt_primary_sale) {
							// check if selected
							if(!empty($_cust[$customer->area_code])){
								if(in_array($customer->customer_code, $_cust[$customer->area_code])){
									$_c_gsv = self::customer_gsv($abort, $customer, $_mt_primary_sale, $salescources, $this->_mt_primary_sales);
								
									$customer->gsv = ($_c_gsv['customer_gsv'] * $customer->multiplier ) / 100;
									if ($_c_gsv['abort'] === true) 
									{
										if($customer->gsv > 0){
											$this->mt_total_sales += $customer->gsv;
										}
										break;
									}
								}
								
							}else{
								$_c_gsv = self::customer_gsv($abort, $customer, $_mt_primary_sale, $salescources, $this->_mt_primary_sales);

								$customer->gsv =  ($_c_gsv['customer_gsv'] * $customer->multiplier ) / 100;
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
						if(!isset($this->area_sales[$customer->area_code])){
							$this->area_sales[$customer->area_code] = 0;
						}
						$this->area_sales[$customer->area_code] += $customer->gsv;
					}else{
						$customer->gsv = $total_account_gsv;

					}

					if(array_key_exists($customer->area_code, $forced_areas)){
						$this->force_total_gsv += $customer->gsv * $forced_areas[$customer->area_code];
					}
					$this->total_gsv += $customer->gsv;
				}
				$data[] = (array)$customer;
			}
			
		}


		return $customers;
	}

	public function get_additional_gsv(&$additional_gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$salescources,$customers_list,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales){
		if(in_array($customer->group_code, $_grps)){
			if(!empty($_areas[$customer->group_code])){
				if(in_array($customer->area_code, $_areas[$customer->group_code])){
					if(!empty($_cust[$customer->area_code])){
						if(in_array($customer->customer_code, $_cust[$customer->area_code])){
							if(!empty($_shp[$customer->customer_code])){
								if(in_array($_shipto->ship_to_code, $_shp[$customer->customer_code])){
									if(!empty($_otlts[$_shipto->ship_to_code])){
										if(in_array($_account->id, $_otlts[$_shipto->ship_to_code])){
											$additional_gsv = self::additonal_outlet_sales($salescources,$customers_list,$customer->customer_code,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales,$_account->account_name);
											
										}
									}else{
										$additional_gsv = self::additonal_outlet_sales($salescources,$customers_list,$customer->customer_code,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales,$_account->account_name);
										
									}
								}
							}else{
								$additional_gsv = self::additonal_outlet_sales($salescources,$customers_list,$customer->customer_code,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales,$_account->account_name);
								
							}
						}
					}else{
						$additional_gsv = self::additonal_outlet_sales($salescources,$customers_list,$customer->customer_code,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales,$_account->account_name);
						
					}
				}
			}else{
				$additional_gsv = self::additonal_outlet_sales($salescources,$customers_list,$customer->customer_code,$_shiptos_list,$_accounts_list,$_outlets,$_outlet_sales,$_account->account_name);
			}
		}
	}

	public function get_gsv(&$gsv,$customer,$_grps,$_areas,$_cust,$_shp,$_shipto,$_otlts,$_account,$_outlet_sale){
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

		// from with dt
		// if(in_array($customer->group_code, $_grps)){
		// 	if(!empty($_areas[$customer->group_code])){
		// 		if(in_array($customer->area_code, $_areas[$customer->group_code])){
		// 			if(!empty($_cust[$customer->area_code])){
		// 				if(in_array($customer->customer_code, $_cust[$customer->area_code])){
		// 					if(!empty($_shp[$customer->customer_code])){
		// 						if(in_array($_shipto->ship_to_code, $_shp[$customer->customer_code])){
		// 							if(!empty($_otlts[$_shipto->ship_to_code])){
		// 								if(in_array($_account->id, $_otlts[$_shipto->ship_to_code])){
		// 									$gsv +=  $_outlet_sale->gsv;
		// 								}
		// 							}else{
		// 								$gsv +=  $_outlet_sale->gsv;
		// 							}
		// 						}
		// 					}else{
		// 						$gsv +=  $_outlet_sale->gsv;
		// 					}
		// 				}
		// 			}else{
		// 				$gsv +=  $_outlet_sale->gsv;
		// 			}
		// 		}
		// 	}else{
		// 		$gsv +=  $_outlet_sale->gsv;
		// 	}
		// }
	}


	public function additonal_outlet_sales($salescources,$customers,$customer_code,$_shiptos,$_accounts,$_outlets,$_outlet_sales,$account_name){
		$additonal_sales = 0;
		foreach ($salescources as $salescource) {
			if($salescource->active_customer_code == $customer_code){
				$old_customer = self::get_old_customer($customers,$salescource->inactive_customer_code);
				// Helper::print_r($old_customer);
				if(!empty($old_customer)){
					foreach ($_shiptos as $_shipto) {
						if($old_customer->customer_code == $_shipto->customer_code){
							
							if(!is_null($_shipto->ship_to_code)){
								unset($_shipto->accounts);
								foreach ($_accounts as $_account) {

									if(($_account->area_code == $old_customer->area_code) 
										&& ($_account->ship_to_code == $_shipto->ship_to_code) &&
										($_account->account_name == $account_name)){
										$outlets = array();
										$gsv = 0;
										// Helper::print_r($_account);
										foreach ($_outlets as $_outlet) {
											$account_area_code = $_account->area_code;
											if(!empty($old_customer->area_code_two)){
												$account_area_code = $old_customer->area_code_two;		
											}

											if(($_outlet->area_code == $account_area_code) &&
												($_outlet->ship_to_code == $_account->ship_to_code) &&
												($_outlet->account_name == $account_name)){
												
												foreach ($_outlet_sales as $key => $_outlet_sale) {
													if(($_outlet_sale->outlet_code == $_outlet->outlet_code) &&
														($_outlet_sale->area_code == $_outlet->area_code) &&
														($_outlet_sale->account_name == $_outlet->account_name) &&
														($_outlet_sale->customer_code == $_outlet->customer_code)){
														$additonal_sales += ($_outlet_sale->gsv * $salescource->split) / 100;
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
		return $additonal_sales;
		
	}

	public function get_old_customer($customers,$customer_code){
		foreach ($customers as $customer) {
			if($customer->customer_code == $customer_code){
				return $customer;
			}
			
		}
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

	public function total_gsv(){
		return $this->total_gsv;
	}

	public function force_total_gsv(){
		return $this->force_total_gsv;
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

	public function account_group($code){
		return \DB::table('accounts')
			->where('account_group_code', $code)
			->groupBy('account_name')
			->orderBy('account_name')
			->get();
	}


	public function groups(){
		$data = array();
		if(!empty($this->_customers)){
			foreach ($this->_customers as $selected_customer) {
				$_selected_customer = explode(".", $selected_customer);
				$data[] = $_selected_customer[0];
			}
		}

		return \DB::table('groups')
			->whereIn('group_code',$data)->get();
	}

	public function areas(){
		$data = array();
		if(!empty($this->_customers)){
			foreach ($this->_customers as $selected_customer) {
				$_selected_customer = explode(".", $selected_customer);
				if(!empty($_selected_customer[1])){
					$data[] = $_selected_customer[1];
				}
			}
		}

		return \DB::table('areas')
			->whereIn('area_code',$data)->get();
	}

	public function soldtos(){
		$data = array();
		if(!empty($this->_customers)){
			foreach ($this->_customers as $selected_customer) {
				$_selected_customer = explode(".", $selected_customer);
				if(!empty($_selected_customer[2])){
					$data[] = $_selected_customer[2];
				}
			}
		}

		return \DB::table('customers')
			->whereIn('customer_code',$data)->get();
	}

	public function area_sales(){
		return $this->area_sales;
	}
	

}