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

}