<?php

class SplitOldCustomer extends \Eloquent {
	protected $fillable = ['split', 'from_customer', 'from_plant', 'to_customer', 'to_plant'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->inactive_customer_code)){
				$attributes = array(
					'inactive_customer_code' => $row->inactive_customer_code,
					'active_customer_code' => $row->active_customer_code,
					'split' => $row->split);
				self::insert($attributes, $attributes);
			}
		});
	}

	public static function getAll(){
		return self::select('c1.customer_code as from_customer_code', 'c1.customer_name as from_customer_name',
			'c2.customer_code as to_customer_code', 'c2.customer_name as to_customer_name', 'sh1.ship_to_name as from_ship_to_name',
			'sh2.ship_to_name as to_ship_to_name','split_old_customers.split')
			->join('customers as c1', 'c1.customer_code' , '=', 'split_old_customers.from_customer', 'left')
			->join('ship_tos as sh1', 'sh1.plant_code' , '=', 'split_old_customers.from_plant', 'left')
			->join('customers as c2', 'c2.customer_code' , '=', 'split_old_customers.to_customer', 'left')
			->join('ship_tos as sh2', 'sh2.plant_code' , '=', 'split_old_customers.to_plant', 'left')
			->groupBy(['split_old_customers.from_customer', 'split_old_customers.from_plant', 'split_old_customers.to_plant', 'split_old_customers.to_plant'])
			->get();

		return self::all();
	}

	public static function import($records){
		DB::beginTransaction();
		try {
				DB::table('split_old_customers')->truncate();
				$records->each(function($row)  {
				$split = new SplitOldCustomer;
				$split->split = $row->split;
				$split->from_customer = $row->from_customer;
				if(is_null($row->from_plant)){
					$split->from_plant = '';
				}else{
					$split->from_plant = $row->from_plant;
				}
				
				$split->to_customer = $row->to_customer;
				if(is_null($row->to_plant)){
					$split->to_plant = '';
				}else{
					$split->to_plant = $row->to_plant;
				}
				
				$split->save();
				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}
}