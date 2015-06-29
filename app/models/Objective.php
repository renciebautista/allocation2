<?php

class Objective extends \Eloquent {
	protected $fillable = ['objective'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->objective)){
				$attributes = array(
					'objective' => strtoupper($row->objective),
					);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public function activities()
    {
        return $this->belongsToMany('Activity');
    }

    public static function getLists(){
    	return self::orderBy('id')->lists('objective', 'id');;
    }
}