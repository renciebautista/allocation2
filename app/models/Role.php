<?php

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	protected $fillable = ['name'];
	public static $rules = array(
        'name' => 'required|between:4,128|unique:roles'
    );

    public static function search($filter){
		return self::where('name', 'LIKE' ,"%$filter%")
			->orderBy('name')
			->get();
	}


}