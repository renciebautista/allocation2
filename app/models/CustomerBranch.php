<?php

class CustomerBranch extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function importbranch($records){
		DB::beginTransaction();
			try {
				DB::table('customer_branches')->truncate();
				$records->each(function($row)  {
					// Helper::debug($row);
					if(!is_null($row->distributor_code)){
					$branch = new CustomerBranch;
					$branch->customer_code = $row->customer_code;
					$branch->plant_code = $row->plant_code;
					$branch->distributor_code = $row->distributor_code;
					$branch->branch_name = $row->branch_name;
					$branch->save();
					}
				
				});
			DB::commit();
		} catch (\Exception $e) {
			Helper::debug($e);
			DB::rollback();
		}
	}

	public static function search($inputs){
		$filter ='';
		if(isset($inputs['s'])){
			$filter = $inputs['s'];
		}
		return self::join('customers', 'customers.customer_code' , '=', 'customer_branches.customer_code', 'left')
			->join('areas', 'areas.area_code' , '=', 'customers.area_code', 'left')
			->where(function($query) use ($filter){
				$query->where('area_name', 'LIKE' ,"%$filter%")
					->orWhere('customer_name', 'LIKE' ,"%$filter%")
					->orWhere('branch_name', 'LIKE' ,"%$filter%")
					->orWhere('distributor_code', 'LIKE' ,"%$filter%");
			})
			->orderby('area_name')
			->orderby('customer_name')
			->orderby('branch_name')
			->get();
	}

	public static function getExport(){
		return self::all();
		// return self::select('id', 'area_code',  'area_name', 'customer_code', 'customer_name', 'branch_name','distributor_code', 'plant_code')
		// 	->join('customers', 'customers.customer_code' , '=', 'customer_branches.customer_code', 'left')
		// 	->join('areas', 'areas.area_code' , '=', 'customers.area_code', 'left')
		// 	->orderby('area_name')
		// 	->orderby('customer_name')
		// 	->orderby('branch_name')
		// 	->get();
	}
}