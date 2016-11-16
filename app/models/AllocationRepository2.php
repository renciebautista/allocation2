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
		$additionalsales = DB::table('split_old_customers')->get();

		$customers_list =  DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name',
				'customer_name','customer_code','customers.area_code as area_code',
				'customers.area_code_two as area_code_two','multiplier','active','from_dt','sob_customer_code', 'trade_deal')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->join('groups', 'areas.group_code', '=', 'groups.group_code')
			->orderBy('groups.id')
			->orderBy('areas.id')
			->orderBy('customers.id')
			->get();

		$customers =  DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name',
				'customer_name','customer_code','customers.area_code as area_code',
				'customers.area_code_two as area_code_two','multiplier','active', 'from_dt', 'sob_customer_code', 'trade_deal')
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
		

		$channels = array();
		if(!empty($selected_channels)){
			foreach ($selected_channels as $channel_node) {
				$_selected_node = explode(".", $channel_node);
				$channels[] = $_selected_node[0];
			}
		}

		$acc_nodes = [];
		$ship_nodes = [];
		$cust_nodes = [];

		if(!empty($selected_customers)){
			foreach ($selected_customers as $selected_customer) {
				$n = explode('.', $selected_customer);
				if(count($n) == 1){
					$n_nodes = CustomerTree::where('channel_code', $n[0])->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->account_id != ''){
							$acc = Account::find($n_node->account_id);
							$acc_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][$n_node->plant_code][] = $acc->account_name;
						}
						if($n_node->plant_code != ''){
							$ship_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][] = $n_node->plant_code;
						}

						$cust_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][] = $n_node->customer_code;

					}
					
				}

				if(count($n) == 2){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->account_id != ''){
							$acc = Account::find($n_node->account_id);
							$acc_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][$n_node->plant_code][] = $acc->account_name;
						}
						if($n_node->plant_code != ''){
							$ship_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][] = $n_node->plant_code;
						}

						$cust_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 3){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->account_id != ''){
							$acc = Account::find($n_node->account_id);
							$acc_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][$n_node->plant_code][] = $acc->account_name;
						}
						if($n_node->plant_code != ''){
							$ship_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][] = $n_node->plant_code;
						}

						$cust_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][] = $n_node->customer_code;
					}
					
				}

				if(count($n) == 4){
					$n_nodes = CustomerTree::where('channel_code', $n[0])
						->where('group_code', $n[1])
						->where('area_code', $n[2])
						->where('customer_code', $n[3])
						->get();
					foreach ($n_nodes as $n_node) {
						if($n_node->account_id != ''){
							$acc = Account::find($n_node->account_id);
							$acc_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][$n_node->plant_code][] = $acc->account_name;
						}
						if($n_node->plant_code != ''){
							$ship_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][] = $n_node->plant_code;
						}

						$cust_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][] = $n_node->customer_code;
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
						if($n_node->account_id != ''){
							$acc = Account::find($n_node->account_id);
							$acc_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][$n_node->plant_code][] = $acc->account_name;
						}
						if($n_node->plant_code != ''){
							$ship_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][] = $n_node->plant_code;
						}

						$cust_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][] = $n_node->customer_code;
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
						if($n_node->account_id != ''){
							$acc = Account::find($n_node->account_id);
							$acc_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][$n_node->plant_code][] = $acc->account_name;
						}
						if($n_node->plant_code != ''){
							$ship_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][$n_node->customer_code][] = $n_node->plant_code;
						}

						$cust_nodes[$n_node->channel_code][$n_node->group_code][$n_node->area_code][] = $n_node->customer_code;
					}
					
				}
			}
		}

		$this->account_sales = DB::table('mt_dt_sales')
			->select(DB::raw('area_code, customer_code, plant_code, account_name, channel_code, sum(gsv) as gsv'))
			->join('sub_channels', function($join)
			{
				$join->on('sub_channels.coc_03_code', '=', 'mt_dt_sales.coc_03_code');
				$join->on('sub_channels.l4_code','=','mt_dt_sales.coc_04_code');
				$join->on('sub_channels.l5_code','=','mt_dt_sales.coc_05_code');
			})
			->whereIn('child_sku_code', $child_skus)
			->whereIn('channel_code', $channels)
			->groupBy('area_code', 'customer_code', 'plant_code', 'account_name', 'channel_code')
			->get();



		$_base_sales = $this->account_sales;

		foreach ($customers as $customer) {
			if($customer->active == 1){
				$ado_total = 0;
				$forced_ado_total = 0;
				$customer_gsv = 0;
				foreach ($_shiptos as $_shipto) {
					if($customer->customer_code == $_shipto->customer_code){
						if(!is_null($_shipto->ship_to_code)){
							unset($_shipto->accounts);
							$total_account_gsv = 0;
							foreach ($_accounts as $_account) {
								if(($_account->area_code == $customer->area_code) && ($_account->ship_to_code == $_shipto->ship_to_code)){
									$_account->gsv = 0;
									$_account->added_gsv = 0;
									if(isset($acc_nodes[$_account->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code][$_shipto->plant_code])){
										if(in_array($_account->account_name, $acc_nodes[$_account->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code][$_shipto->plant_code])){
											foreach ($this->account_sales as $account_sale) {
												if(($account_sale->area_code == $_account->area_code) && ($account_sale->customer_code == $_shipto->customer_code) && ($account_sale->plant_code == $_shipto->plant_code) && ($account_sale->account_name == $_account->account_name)){
													$_account->gsv = $account_sale->gsv;
												}
											}
											$added_gsv = self::getAdditionalAccountGsv($additionalsales, $_base_sales, $customer->customer_code, $_account->channel_code, $_shipto->plant_code, $_account->account_name);
											$_account->added_gsv += $added_gsv;
											$_account->gsv +=  $added_gsv;
										}
									}

									$_account->gsv = ($_account->gsv * $customer->multiplier ) / 100;

									$total_account_gsv += $_account->gsv;
									$_shipto->accounts[] = (array) $_account;
								}
							}
						}
						$_shipto->area_code = $customer->area_code;

						// start ship to sales
						$_shipto->gsv = 0;
						$_shipto->addgsv = 0;
						foreach ($this->account_sales as $account_sale) {
							if(isset($ship_nodes[$account_sale->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code])){
								if(in_array($_shipto->plant_code, $ship_nodes[$account_sale->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code])){
									if(($customer->customer_code == $account_sale->customer_code) && ($_shipto->plant_code == $account_sale->plant_code)){
										$_shipto->gsv += $account_sale->gsv;
									}
								}
							}
						}

						$add_gsv = self::getAdditionalShipGsv($additionalsales, $_base_sales, $channels, $ship_nodes, $customer->customer_code, $_shipto->plant_code);
						$_shipto->gsv += $add_gsv;
						$_shipto->addgsv = $add_gsv;
						

						$_shipto->gsv = ($_shipto->gsv * $customer->multiplier ) / 100;
						// end ship to sales

						$customer_gsv += $_shipto->gsv;
						$ado_total += $_shipto->gsv;
						$customer->shiptos[] = (array)	$_shipto;

						if(array_key_exists($customer->area_code, $forced_areas)){
							$forced_ado_total += $_shipto->gsv * $forced_areas[$customer->area_code];
						}
						
						$customer->forced_ado_total = $forced_ado_total;

					}

				}

				$customer->adde_gsv = 0; 
				$no_shipto = ShipTo::where('customer_code', $customer->customer_code)->get();
				if(count($no_shipto) == 0){
					foreach ($channels as $ch) {
						if(isset($cust_nodes[$ch][$customer->group_code][$customer->area_code])){
							if(in_array($customer->customer_code, $cust_nodes[$ch][$customer->group_code][$customer->area_code])){
								foreach ($this->account_sales as $account_sale) {
									if(($customer->customer_code == $account_sale->customer_code) && ($account_sale->channel_code == $ch)){
										$customer_gsv += $account_sale->gsv;
									}							
								}
							}
						}	
					}

					$customer_gsv += self::getAdditionalCustomerGsv($additionalsales, $_base_sales, $channels, $cust_nodes, $customer->customer_code);

				}


				$customer->ado_total = $customer_gsv;

				$customer->gsv = ($customer_gsv * $customer->multiplier ) / 100;

				if(array_key_exists($customer->area_code, $forced_areas)){
					$this->force_total_gsv += $customer->gsv * $forced_areas[$customer->area_code];
				}

				$this->total_gsv += $customer->gsv;
			}
		}
		return $customers;
	}

	private function getAdditionalCustomerGsv($additionalsales, $_base_sales, $channels, $cust_nodes, $to_customer){
		$gsv = 0;
		foreach ($additionalsales as $row) {
			if(($row->to_customer == $to_customer) && ($row->to_plant == '')){

				$customer = DB::table('customers')
					->select('areas.group_code as group_code','group_name','area_name',
						'customer_name','customer_code','customers.area_code as area_code',
						'customers.area_code_two as area_code_two','multiplier','active','from_dt','sob_customer_code')
					->join('areas', 'customers.area_code', '=', 'areas.area_code')
					->join('groups', 'areas.group_code', '=', 'groups.group_code')
					->where('customer_code',  $to_customer)
					->first();

				foreach ($this->account_sales as $account_sale) {
					if(isset($cust_nodes[$account_sale->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code])){
						if(in_array($to_plant, $cust_nodes[$account_sale->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code])){
							if(($account_sale->customer_code == $row->from_customer)){
								$gsv += $account_sale->gsv;
							}
						}
					}
				}
				$gsv = ($gsv * $row->split) / 100;
			}
			$gsv = ($gsv * $row->split) / 100;
		}
		return $gsv;
	}

	private function getAdditionalShipGsv($additionalsales, $_base_sales, $channels, $ship_nodes, $to_customer, $to_plant){
		$gsv = 0;
		foreach ($additionalsales as $row) {
			if(($row->to_customer == $to_customer) && ($row->to_plant == $to_plant)){
				
				$customer = DB::table('customers')
					->select('areas.group_code as group_code','group_name','area_name',
						'customer_name','customer_code','customers.area_code as area_code',
						'customers.area_code_two as area_code_two','multiplier','active','from_dt','sob_customer_code')
					->join('areas', 'customers.area_code', '=', 'areas.area_code')
					->join('groups', 'areas.group_code', '=', 'groups.group_code')
					->where('customer_code',  $to_customer)
					->first();

				foreach ($this->account_sales as $account_sale) {
					if(isset($ship_nodes[$account_sale->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code])){
						if(in_array($to_plant, $ship_nodes[$account_sale->channel_code][$customer->group_code][$customer->area_code][$customer->customer_code])){
							if(($account_sale->customer_code == $row->from_customer) && 
								($account_sale->plant_code == $row->from_plant)){
								$gsv += $account_sale->gsv;
							}
						}
					}
				}
				$gsv = ($gsv * $row->split) / 100;
			}
		}
		return $gsv;
	}

	private function getAdditionalAccountGsv($split, $source, $to_customer, $channel_code, $to_plant, $account_name){
		$gsv = 0;
		foreach ($split as $row) {
			if(($row->to_customer == $to_customer) && ($row->to_plant == $to_plant)){
				foreach ($source as $sale) {
					if(!is_null($account_name)){
						if(($sale->customer_code == $row->from_customer) && 
							($sale->plant_code == $row->from_plant) && 
							($sale->account_name == $account_name) && 
							($sale->channel_code == $channel_code)){
							$gsv += $sale->gsv;
						}
					}
				}
			}
			
			$gsv = ($gsv * $row->split) / 100;
		}
		return $gsv;
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