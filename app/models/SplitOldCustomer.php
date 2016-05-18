<?php

class SplitOldCustomer extends \Eloquent {
	protected $fillable = ['inactive_customer_code', 'active_customer_code', 'split'];
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
			'c2.customer_code as to_customer_code', 'c2.customer_name as to_customer_name', 
			'c2.active',
			'split_old_customers.split')
			->join('customers as c1', 'c1.customer_code' , '=', 'split_old_customers.inactive_customer_code')
			->join('customers as c2', 'c2.customer_code' , '=', 'split_old_customers.active_customer_code')
			// ->where('c2.active',1)
			->get();
	}

	public static function import($records){
		DB::beginTransaction();
		try {
				DB::table('split_old_customers')->truncate();
				$records->each(function($row)  {
				if(!is_null($row->inactive_customer_code)){
					$split = new SplitOldCustomer;
					$split->inactive_customer_code = $row->inactive_customer_code;
					$split->active_customer_code = $row->active_customer_code;
					$split->split = $row->split;
					$split->save();
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			// dd($e);
			DB::rollback();
		}
	}
}