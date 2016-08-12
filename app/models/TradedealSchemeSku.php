<?php

class TradedealSchemeSku extends \Eloquent {
	protected $fillable = ['qty', 'tradedeal_scheme_id', 'tradedeal_part_sku_id'];

	public function host(){
		return $this->belongsTo('TradedealPartSku', 'tradedeal_part_sku_id', 'id');
	}

	public static function getSelected($scheme){
		// $records = self::select('tradedeal_part_skus.id')
		// 	// ->join('tradedeal_part_skus', 'tradedeal_part_skus.id' , '=', 'tradedeal_scheme_skus.tradedeal_part_sku_id')
		// 	->where('tradedeal_scheme_id', $scheme->id)->get();
		$records = self::where('tradedeal_scheme_id', $scheme->id)->get();
		$data['selection'] = [];
		$data['values'] = [];
		foreach ($records as $row) {
			$data['selection'][] = $row->tradedeal_part_sku_id;
			$data['values'][$row->tradedeal_part_sku_id] = $row->qty;
		}

		return $data;
	}

	
}