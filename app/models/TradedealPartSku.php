<?php

class TradedealPartSku extends \Eloquent {
	protected $fillable = [];

	public function preDesc(){
		if(!empty($this->attributes['pre_desc'])){
			return $this->attributes['pre_desc'] . ' - ' .$this->attributes['pre_code'];
		}
	}
}