<?php

class Scheme extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'scheme_name' => 'required',
		// 'pr' => 'required',
		// 'srp_p' => 'required',
		'total_alloc' => 'required',
		// 'deals' => 'required',
		'skus' => 'required',
	);

	public function activity()
    {
        return $this->belongsTo('Activity','activity_id','id');
    }


    public static function getList($activity_id){
		return self::where('activity_id', $activity_id)
				->orderBy('created_at', 'asc')
				->get();
	}


	public static function getSummary($schemes){
		$obj = new stdClass();
		$obj->pr = 0;
		$obj->ulp = 0;
		$obj->cost_sale = 0;
		$obj->final_total_deals = 0;
		$obj->final_total_cases = 0;
		$obj->final_tts_r = 0;
		$obj->final_pe_r = 0;
		$obj->final_total_cost = 0;
		foreach ($schemes as $scheme) {
			$obj->pr += $scheme->pr; 
			$obj->ulp += $scheme->ulp; 
			$obj->cost_sale += $scheme->cost_sale; 
			$obj->final_total_deals += $scheme->final_total_deals; 
			$obj->final_total_cases += $scheme->final_total_cases; 
			$obj->final_tts_r += $scheme->final_tts_r; 
			$obj->final_pe_r += $scheme->final_pe_r; 
			$obj->final_total_cost += $scheme->final_total_cost; 
		}
		return $obj;
	}
}