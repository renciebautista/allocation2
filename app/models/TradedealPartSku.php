<?php

class TradedealPartSku extends \Eloquent {
	protected $fillable = [];


	public function hostDesc(){
		if(!empty($this->attributes['host_desc'])){
			return $this->attributes['host_desc'] . ' - ' .$this->attributes['host_code'];
		}
	}

	public function preDesc(){
		if(!empty($this->attributes['pre_desc'])){
			return $this->attributes['pre_desc'] . ' - ' .$this->attributes['pre_code'];
		}
	}

	public static function getPartSkus($activity){
		return self::where('activity_id', $activity->id)->get();
	}


	public static function nonUlpHostExist($activity, $host_code, $variant,  $part_sku = null){
		$record = self::where('activity_id', $activity->id)
			->where('host_code', $host_code)
			->where('variant', $variant)
			->where(function($query) use ($part_sku){
				if(!is_null($part_sku)){
					$query->where('id','!=',$part_sku->id);
				}
			})
			->first();

		if(!empty($record)){
			return true;
		}else{
			return false;
		}
	}

	public static function ulpHostExist($activity, $host_code, $variant, $pre_code, $pre_variant, $part_sku = null){
		$record = self::where('activity_id', $activity->id)
			->where('host_code', $host_code)
			->where('variant', $variant)
			->where('pre_code', $pre_code)
			->where(function($query) use ($part_sku){
				if(!is_null($part_sku)){
					$query->where('id','!=',$part_sku->id);
				}
			})
			->first();

		if(!empty($record)){
			return true;
		}else{
			return false;
		}
	}

	public static function getParticipatingSku($activity){
		return self::select(array('id',
			DB::raw('CONCAT(host_desc," - ",host_code) as host_sku'), 'host_cost', 'host_pcs_case', 'variant', 
			DB::raw('CONCAT(ref_desc," - ",ref_code) as ref_sku'),
			DB::raw('CONCAT(pre_desc," - ",pre_code) as pre_sku'), 'pre_cost', 'pre_pcs_case', 'pre_variant'))
			->where('activity_id', $activity->id)
			->orderBy('id')
			->get();
	}



}