<?php

class SobGroup extends \Eloquent {
	protected $fillable = ['sobgroup'];
	public $timestamps = false;
	public static $rules = array(
        'sobgroup' => 'required|unique:sob_groups'
    );

    public static function search($inputs){
		$filter ='';
		if(isset($inputs['s'])){
			$filter = $inputs['s'];
		}

		return self::where('sobgroup', 'LIKE' ,"%$filter%")
			->get();
	}
}