<?php

class SchemeAllocation extends \Eloquent {
	protected $table = 'allocations';
	protected $fillable = [];

	public static function getCustomers($id){
		return self::select('group', 'area', 'sold_to', 'ship_to', 'channel', 'outlet')
			->join('schemes','allocations.scheme_id', '=', 'schemes.id')
			->where('schemes.activity_id', $id)
			->groupBy(array('group', 'area', 'sold_to', 'ship_to', 'channel', 'outlet'))
			->orderBy('allocations.id')
			->get();
	}

	public static function getCustomerAllocation($id){
		return self::where('scheme_id', $id)
			->get();
	}

	public static function getAllocation($id){
		$data = array();
		$schemes = Scheme::where('activity_id', $id)
				->orderBy('created_at', 'desc')
				->get();
		if(count($schemes)>0){
			foreach ($schemes as $scheme) {
				$alloc = 
				$data[$scheme->id] = self::getAlloc($scheme->id);
			}
		}
		return $data;
	}

	private static function getAlloc($scheme_id){
		$data = array();
		$allocs = self::where('scheme_id', $scheme_id)
					->orderBy('allocations.id')
					->get();
		if(count($allocs)>0){
			foreach ($allocs as $alloc) {
				$_alloc = 0;
				// if((!empty($alloc->customer_id)) && (empty($alloc->shipto_id))){
				// 	echo $alloc->group.".".$alloc->area.".".$alloc->sold_to.".".$alloc->ship_to.".".$alloc->channel.".".$alloc->outlet;
				// 	echo  $alloc->ship_to_alloc;
				// 	echo '<br>';
				// }
				if((empty($alloc->customer_id)) && (empty($alloc->shipto_id))){
					$_alloc = $alloc->sold_to_alloc;
				}

				if((!empty($alloc->customer_id)) && (empty($alloc->shipto_id))){
					$_alloc = $alloc->ship_to_alloc;
				}

				if((!empty($alloc->customer_id)) && (!empty($alloc->shipto_id))){
					$_alloc = $alloc->outlet_to_alloc;
				}
				
				$data[md5($alloc->group.".".$alloc->area.".".$alloc->sold_to.".".$alloc->ship_to.".".$alloc->channel.".".$alloc->outlet)] = $_alloc;
			}
		}
		return $data;
	}

	public static function recomputeAlloc($alloc){
		
		if(is_null($alloc->customer_id)){
			$customer_id = $alloc->id;
			$customer_alloc = $alloc->final_alloc;
		}else{
			$customer_id = $alloc->customer_id;
			$parent = self::find($alloc->customer_id);
			$parent->final_alloc = round($alloc->final_alloc / $alloc->multi);
			$parent->update();		

			$customer_alloc	= $parent->final_alloc;
		}

		$childs = self::where('customer_id',$customer_id)
			->where('shipto_id',null)
			->get();

		if(!empty($childs)){
			foreach ($childs as $child) {
				$child->final_alloc = $customer_alloc * $child->multi;
				$child->update();

				$outlets = self::where('customer_id',$customer_id)
					->where('shipto_id',$child->id)
					->get();

				$others_alloc = $child->final_alloc;
				if(!empty($outlets)){
					foreach ($outlets as $outlet) {
						$outlet_final_alloc = $child->final_alloc * $outlet->multi;
						if($outlet->outlet == 'OTHERS'){
							$outlet->final_alloc = $others_alloc;
						}else{
							$outlet->final_alloc = $outlet_final_alloc;
							$others_alloc -= $outlet_final_alloc;
						}
						// $outlet->multi = $child->final_alloc/$others_alloc;
						$outlet->update();
					}
				}
			}
		}
	}

	public static function finalallocation($id){
		return self::where('scheme_id',$id)
			->where('customer_id',null)
			->where('shipto_id',null)
			->sum('final_alloc');;
	}
}