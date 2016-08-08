<?php

class TradedealSchemeSku extends \Eloquent {
	protected $fillable = ['tradedeal_scheme_id', 'tradedeal_part_sku_id'];

	public function host(){
		return $this->belongsTo('TradedealPartSku', 'tradedeal_part_sku_id', 'id');
	}

	public static function getSelected($scheme){
		$records = self::select('tradedeal_part_skus.id')
			->join('tradedeal_part_skus', 'tradedeal_part_skus.id' , '=', 'tradedeal_scheme_skus.tradedeal_part_sku_id')
			->where('tradedeal_scheme_id', $scheme->id)->get();
		$data = [];
		foreach ($records as $row) {
			$data[] = $row->id;
		}

		return $data;
	}
}