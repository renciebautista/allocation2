<?php

class Customer extends \Eloquent {
	protected $fillable = ['area_code', 'customer_code', 'customer_name', 'active', 'multiplier','trade_deal'];
	public $timestamps = false;

	public static function getForTradedeal(){
		return self::join('areas', 'areas.area_code' , '=', 'customers.area_code')
			->where('active',1)
			->where('trade_deal',1)
			->get();
	}

	public static function getAll(){
		return self::select('customers.id', 'customers.area_code',
			'areas.area_name', 'customers.customer_code', 'customers.sob_customer_code','customers.customer_name',
			'customers.multiplier', 'customers.trade_deal', 'customers.active')
			->join('areas', 'areas.area_code' , '=', 'customers.area_code', 'left')
			->get();
	}

	public static function search($inputs){
		$filter ='';
		if(isset($inputs['s'])){
			$filter = $inputs['s'];
		}

		$status = 1;
		if(isset($inputs['status'])){
			$status = $inputs['status'];
		}
		return self::select('customers.id', 'customers.area_code',
			'areas.area_name', 'customers.customer_code', 'customers.sob_customer_code','customers.customer_name',
			'customers.multiplier', 'customers.trade_deal', 'customers.active')
			->join('areas', 'areas.area_code' , '=', 'customers.area_code', 'left')
			->where('customer_name', 'LIKE' ,"%$filter%")
			->where(function($query) use ($status){
				if($status < 2){
					$query->where('active',$status);
				}
			})
			->get();
	}

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->area_code)){
				$attributes = array(
					'area_code' => $row->area_code,
					'area_code_two' => $row->area_code_two,
					'customer_code' => $row->customer_code,
					'customer_name' => $row->customer_name,
					'active' => ($row->active == 'Y') ? 1 : 0,
					'multiplier' => $row->multiplier,
					'from_dt' => ($row->from_dt == 'Y') ? 1 : 0,);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getCustomerList(){
		$unselectable = true;
		$groups = DB::table('groups')->get();
		$data = array();
		foreach ($groups as $group) {
			$areas = DB::table('customers')
			->select('areas.group_code as group_code','customers.area_code as area_code','area_name')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->where('areas.group_code',$group->group_code)
			->where('customers.active', 1)
			->groupBy('customers.area_code')
			->orderBy('areas.id')
			->get();
			$group_children = array();
			foreach ($areas as $area) {
				$customers = DB::table('customers')
					->where('area_code',$area->area_code)
					->where('customers.active', 1)
					->get();
				$area_children = array();
				foreach ($customers as $customer) {
					$ship_tos =  DB::table('ship_tos')
						->where('customer_code',$customer->customer_code)
						->where('ship_tos.active', 1)
						->get();
					$customer_children = array();
					foreach ($ship_tos as $ship_to) {
						
						if($ship_to->ship_to_code != ''){
							$ship_to_children = array();

							$accounts = DB::table('accounts')
								->where('ship_to_code',$ship_to->ship_to_code )
								->where('area_code',$area->area_code)
								->get();
								
							if(count($accounts)>0){
								$ship_to_children = array();
								
								foreach ($accounts as $account) {
									$ship_to_children[] = array(
									'title' => $account->account_name,
									'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code.".".$account->id,
									'unselectable' => $unselectable
									);
								}

							}

							if(count($ship_to_children) > 0){
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								'unselectable' => $unselectable,
								'children' => $ship_to_children,
									
								);
							}else{
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								'unselectable' => $unselectable,
								);
							}
						}
					}
					$area_children[] = array(
					'title' => $customer->customer_name,
					'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code,
					'unselectable' => $unselectable,
					'children' => $customer_children,
					);
				}
				$group_children[] = array(
					'select' => true,
					'title' => $area->area_name,
					'isfolder' => true,
					'key' => $group->group_code.".".$area->area_code,
					'unselectable' => $unselectable,
					'children' => $area_children,
					);
			}
			$data[] = array(
				'title' => $group->group_name,
				'isfolder' => true,
				'key' => $group->group_code,
				'unselectable' => $unselectable,
				'children' => $group_children,
				);
		}
		return $data;
	}


	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('customers')->truncate();
				$records->each(function($row)  {
					if(!is_null($row->customer_code)){
					$customer = new Customer;
					$customer->area_code = $row->area_code;
					$customer->customer_code = $row->customer_code;
					$customer->customer_name = $row->customer_name;
					$customer->trade_deal = $row->trade_deal;
					$customer->multiplier = $row->multiplier;
					$customer->active = $row->active;
					$customer->save();
					}
				
				});
			DB::commit();
		} catch (\Exception $e) {
			Helper::debug($e);
			DB::rollback();
		}
	}
}