<?php

class Scheme extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'scheme_name' => 'required',
		'pr' => 'required',
		'srp_p' => 'required',
		'total_alloc' => 'required',
		'deals' => 'required',
		'skus' => 'required',
	);

	public function activity()
    {
        return $this->belongsTo('Activity','activity_id','id');
    }


    public static function getList($activity_id){
		return self::where('activity_id', $activity_id)
				->orderBy('created_at', 'desc')
				->get();
	}

}