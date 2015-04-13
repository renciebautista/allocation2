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

	public static function getList($id){
		return self::where('activity_id',$id)->get();
	}

	public function activity()
    {
        return $this->belongsTo('Activity','activity_id','id');
    }

    public static function sorted($id){
    	return self::where('activity_id',$id)
			->orderBy('created_at', 'desc')
			->get();
    }
}