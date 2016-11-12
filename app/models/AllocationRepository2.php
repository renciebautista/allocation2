<?php

class AllocationRepository2  {
	private $mt_total_sales = 0;
	private $dt_total_sales = 0;
	private $total_gsv = 0;
	private $force_total_gsv = 0;
	private $_mt_primary_sales = array();
	private $_dt_secondary_sales = array();

	private $_mt_dt_sales = [];

	private $_customers = array();
	private $_customers_list = array();
	private $area_sales = array();
	
	public function __construct()  {
      	
    }

    private function getSkus($skus){
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

		return array_unique($child_skus);
	}


	public function customers($skus, $selected_channels, $selected_customers,$forced_areas){

		$this->_customers = $selected_customers;
		$salescources = DB::table('split_old_customers')->get();

		$customers_list =  DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name',
				'customer_name','customer_code','customers.area_code as area_code',
				'customers.area_code_two as area_code_two','multiplier','active','from_dt','sob_customer_code')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->join('groups', 'areas.group_code', '=', 'groups.group_code')
			->orderBy('groups.id')
			->orderBy('areas.id')
			->orderBy('customers.id')
			->get();

		$customers =  DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name',
				'customer_name','customer_code','customers.area_code as area_code',
				'customers.area_code_two as area_code_two','multiplier','active', 'from_dt', 'sob_customer_code')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->join('groups', 'areas.group_code', '=', 'groups.group_code')
			->where('customers.active', 1)
			->orderBy('groups.id')
			->orderBy('areas.id')
			->orderBy('customers.id')
			->get();

		// get all ship to
		$_shiptos = DB::table('ship_tos')
			->select('customer_code','sold_to_code', 'ship_to_code', 'plant_code', 'ship_to_name','split')
			->where('ship_tos.active', 1)
			->get();

		$_shiptos_list = DB::table('ship_tos')
			->select('customer_code','sold_to_code','ship_to_code', 'plant_code', 'ship_to_name','split')
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
			->get();

		// get all outlet
		$_outlets = DB::table('outlets')->get();	

		// get all child skus
		$child_skus = self::getSkus($skus);
		// Helper::debug($child_skus);

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


		$_mt_dt_sales = DB::table('mt_dt_sales')
			->select(DB::raw('area_code, customer_code, sum(gsv) as gsv '))
			->join('sub_channels', function($join)
			{
				$join->on('sub_channels.coc_03_code', '=', 'mt_dt_sales.coc_03_code');
				$join->on('sub_channels.l4_code','=','mt_dt_sales.coc_04_code');
				$join->on('sub_channels.l5_code','=','mt_dt_sales.coc_05_code');
			})
			->whereIn('child_sku_code', $child_skus)
			->whereIn('channel_code', $channels)
			->groupBy('area_code', 'customer_code')
			->get();

		// get Ship To Sales
		$_ship_to_sales = DB::table('mt_dt_sales')
			->select(DB::raw('area_code, customer_code, plant_code, sum(gsv) as gsv '))
			->join('sub_channels', function($join)
			{
				$join->on('sub_channels.coc_03_code', '=', 'mt_dt_sales.coc_03_code');
				$join->on('sub_channels.l4_code','=','mt_dt_sales.coc_04_code');
				$join->on('sub_channels.l5_code','=','mt_dt_sales.coc_05_code');
			})
			->whereIn('child_sku_code', $child_skus)
			->whereIn('channel_code', $channels)
			->groupBy('area_code', 'customer_code', 'plant_code')
			->get();	
	

		$this->account_sales = DB::table('mt_dt_sales')
			->select(DB::raw('area_code, customer_code, plant_code, account_name, sum(gsv) as gsv '))
			->join('sub_channels', function($join)
			{
				$join->on('sub_channels.coc_03_code', '=', 'mt_dt_sales.coc_03_code');
				$join->on('sub_channels.l4_code','=','mt_dt_sales.coc_04_code');
				$join->on('sub_channels.l5_code','=','mt_dt_sales.coc_05_code');
			})
			->whereIn('child_sku_code', $child_skus)
			->whereIn('channel_code', $channels)
			->groupBy('area_code', 'customer_code', 'plant_code', 'account_name')
			->get();

		$data = array();
		foreach ($customers as $customer) {
			if($customer->active == 1){
				$ado_total = 0;
				$forced_ado_total = 0;
				foreach ($_shiptos as $_shipto) {
					if($customer->customer_code == $_shipto->customer_code){
						if(!is_null($_shipto->ship_to_code)){
							unset($_shipto->accounts);
							$total_account_gsv = 0;
							foreach ($_accounts as $_account) {
								if(($_account->area_code == $customer->area_code) && ($_account->ship_to_code == $_shipto->ship_to_code)){
									$_account->gsv = 0;

									$abort_= false;
									foreach ($this->account_sales as $account_sale) {
										if(($account_sale->area_code == $_account->area_code) &&
											($account_sale->customer_code == $_shipto->customer_code) &&
											($account_sale->plant_code == $_shipto->plant_code) &&
											($account_sale->account_name == $_account->account_name)){

											$_account->gsv = $account_sale->gsv;
											$abort_ = true;
										}
										if ($abort_ === true) break;
									}
									$total_account_gsv += $_account->gsv;
									$_shipto->accounts[] = (array) $_account;
								}
							}
						}
						$_shipto->area_code = $customer->area_code;

						// start ship to sales
						$abort_shipto = false;
						$_shipto->gsv = 0;
						foreach ($_ship_to_sales as $_ship_to_sale) {
							if($_shipto->plant_code == $_ship_to_sale->plant_code){
								$_shipto->gsv = $_ship_to_sale->gsv;
								
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

				foreach ($_mt_dt_sales as $_mt_dt_sale) {
					if(($customer->area_code == $_mt_dt_sale->area_code) && 
						($customer->customer_code == $_mt_dt_sale->customer_code)){

						$customer->gsv = $_mt_dt_sale->gsv;
					}
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