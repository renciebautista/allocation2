<?php

class Task extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
        'task' => 'required|between:4,128|unique:tasks'
    );

	public static function search($filter){
		return self::where('task', 'LIKE' ,"%$filter%")
			->orderBy('task')
			->get();
	}

	public static function getLists(){
		return self::orderBy('task')->lists('task', 'id');
	}
}