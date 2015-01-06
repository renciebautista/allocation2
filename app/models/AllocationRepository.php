<?php

class AllocationRepository  {
	private $mt_total_sales = 0;
	private $dt_total_sales = 0;
	private $_mt_primary_sales;
	private $_dt_secondary_sales;

	public function __construct()  {
      	
    }


	public function customers($skus,$channels){
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
			->select('ship_to_code','area_code', 'account_name')
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

		
		// get all MT Primary Sales
		$this->_mt_primary_sales = DB::table('mt_primary_sales')
					->select(DB::raw("mt_primary_sales.area_code,mt_primary_sales.customer_code, SUM(gsv) as gsv"))
					->join('customers', 'mt_primary_sales.customer_code', '=', 'customers.customer_code')
					->whereIn('child_sku_code', $child_skus)
					->where('customers.active', 1)
					->groupBy(array('mt_primary_sales.area_code','mt_primary_sales.customer_code'))
					->get();
		

		// get all DT Secondary Sales
		$this->_dt_secondary_sales =DB::table('dt_secondary_sales')
					->select(DB::raw("dt_secondary_sales.area_code,dt_secondary_sales.customer_code, SUM(gsv) as gsv"))
					->join('sub_channels', 'dt_secondary_sales.coc_03_code', '=', 'sub_channels.coc_03_code')
					->join('customers', 'dt_secondary_sales.customer_code', '=', 'customers.customer_code')
					->whereIn('child_sku_code', $child_skus)
					->whereIn('channel_code', $channels)
					->where('customers.active', 1)
					->groupBy(array('dt_secondary_sales.area_code','dt_secondary_sales.customer_code'))
					->get();

		// get Ship To Sales
		$_ship_to_sales = DB::table('ship_to_sales')
					->select(DB::raw("ship_to_code, SUM(gsv) as gsv"))
					->whereIn('child_sku_code', $child_skus)
					->groupBy('ship_to_code')
					->get();	

		// get Outlet Sales
		$_outlet_sales = DB::table('outlet_sales')
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
												($_outlet_sale->outlet_code == $_outlet->outlet_code)){
												// $_outlet->sales[] = (array) $_outlet_sale;
												$gsv +=  $_outlet_sale->gsv;
											}
										}	

										// $outlets[] = (array) $_outlet;
									}
								}

								// $_account->outlets = (array) $outlets;
								$_account->gsv = $gsv;
								$_shipto->accounts[] = (array) $_account;
							}
						}
					}
					$_shipto->area_code = $customer->area_code;

					// start ship to sales
					$abort_shipto = false;
					foreach ($_ship_to_sales as $_ship_to_sale) {
						if($_shipto->ship_to_code == $_ship_to_sale->ship_to_code){
							$_shipto->gsv = $_ship_to_sale->gsv;
							$ado_total += $_ship_to_sale->gsv;
							$abort_shipto = true;
						}else{
							$_shipto->gsv = '';
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
				foreach ($this->_dt_secondary_sales as $_dt_secondary_sale) {
					if(($customer->customer_code == $_dt_secondary_sale->customer_code) && ($customer->area_code == $_dt_secondary_sale->area_code)){
						$customer->gsv = $_dt_secondary_sale->gsv;
						$abort = true;
					}else{
						$customer->gsv = 0;
					}

					if ($abort === true) break;
				}
			}else{
				$abort = false;
				foreach ($this->_mt_primary_sales as $_mt_primary_sale) {
					if(($customer->customer_code == $_mt_primary_sale->customer_code) && ($customer->area_code == $_mt_primary_sale->area_code)){
						$customer->gsv = $_mt_primary_sale->gsv;
						$abort = true;
					}else{
						$customer->gsv = 0;
					}

					if ($abort === true) break;
				}
			}
			$data[] = (array)$customer;
		}
		// echo '<pre>';
		// print_r($customers);
		// echo '</pre>';
		return $customers;
	}


	public function total_sales(){

		foreach ($this->_mt_primary_sales  as $row) {
			if($row->gsv > 0){
				$this->mt_total_sales += $row->gsv;
			}
		}

		foreach ($this->_dt_secondary_sales  as $row) {
			if($row->gsv > 0){
				$this->dt_total_sales += $row->gsv;
			}
		}

		return $this->dt_total_sales + $this->mt_total_sales;
	}

}