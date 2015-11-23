<?php

class Customer extends \Eloquent {
	protected $fillable = ['area_code', 'area_code_two','customer_code', 'customer_name', 'active', 'multiplier','from_dt'];
	public $timestamps = false;

	public static function getAll(){
		return self::select('customers.id', 'customers.area_code',
			'areas.area_name',
			'customers.area_code_two', 'customers.customer_code', 'customers.sob_customer_code','customers.customer_name',
			'customers.multiplier', 'customers.from_dt', 'customers.active')
			->join('areas', 'areas.area_code' , '=', 'customers.area_code')
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
			$records->each(function($row)  {
				if(!is_null($row->customer_name)){
					$customer = self::where('customer_name',$row->customer_name)
						->where('area_code',$row->area_code)
						->first();
					if(empty($customer)){
						$customer = new Customer;
						$customer->area_code = $row->area_code;
						$customer->area_code_two = $row->area_code_two;
						$customer->customer_code = $row->customer_code;
						$customer->sob_customer_code = $row->sob_customer_code;
						$customer->customer_name = $row->customer_name;
						$customer->active = $row->active;
						$customer->multiplier = $row->multiplier;
						$customer->from_dt = $row->from_dt;
						$customer->save();
					}else{
						$customer->area_code = $row->area_code;
						$customer->area_code_two = $row->area_code_two;
						$customer->customer_code = $row->customer_code;
						$customer->sob_customer_code = $row->sob_customer_code;
						$customer->customer_name = $row->customer_name;
						$customer->active = $row->active;
						$customer->multiplier = $row->multiplier;
						$customer->from_dt = $row->from_dt;
						$customer->update();
					}
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			// dd($e);
			DB::rollback();
		}
	}
}