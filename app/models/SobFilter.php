<?php

class SobFilter extends \Eloquent {
	protected $fillable = [];

	public $timestamps = false;

	public function sobGroup(){
		return $this->belongsTo('SobGroup','sob_group_id','id');
	}

	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('sob_filters')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->group_code)){
					$filter = new SobFilter;
					$filter->group_code = $row->group_code;
					$filter->area_code = $row->area_code;
					$filter->customer_code = $row->customer_code;
					$filter->sob_group_id = $row->sob_group_id;
					$filter->save();
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
		}
	}

	public function getGroup(){
		if($this->group_code == '0'){
			return 'ALL';
		}else{
			$group = Group::where('group_code',$this->group_code)->first();
			return $group->group_name;
		}
	}

	public function getArea(){
		if($this->area_code == '0'){
			return 'ALL';
		}else{
			$area = Area::where('area_code',$this->area_code)->first();
			return $area->area_name;
		}
	}
	public function getCustomer(){
		if($this->customer_code == '0'){
			return 'ALL';
		}else{
			$customer = Customer::where('customer_code',$this->customer_code)->first();
			return $customer->customer_name;
		}
	}

	public static function getFilter($shipto){
		$filter = self::where('customer_code',$shipto->sold_to_code)->first(); 
        if(!empty($filter)){ 
            return $filter; // 001
        }


        $filter = self::where('area_code',$shipto->area_code)
        	->where('customer_code', 0)
        	->first(); 
        if(!empty($filter)){ 
            return $filter; // 001
        }

        $filter = self::where('group_code',$shipto->group_code)
        	->where('area_code', 0)
        	->where('customer_code', 0)
        	->first(); 
        if(!empty($filter)){ 
            return $filter; // 001
        }

	}
}