<?php

class TradedealSchemeSku extends \Eloquent {
	protected $fillable = ['tradedeal_scheme_id', 'tradedeal_part_sku_id'];

	public function host(){
		return $this->belongsTo('TradedealPartSku', 'tradedeal_part_sku_id', 'id');
	}
}