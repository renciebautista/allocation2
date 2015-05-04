<?php

class Holiday extends \Eloquent {
	protected $fillable = [];
	public static $rules = array(
        'desc' => 'required',
        'date' => 'required'
    );

    public static function allHoliday(){
    	$holidays = self::where('date', '>=', date("Y-m-d"))
    		->orderBy('date')
    		->get();
    	$data = array();
    	foreach ($holidays as $holiday) {
    		$data[] = $holiday->date;
    	}
    	return $data;
    }

}