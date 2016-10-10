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


	public static function getHostSku($scheme){
		return self::select('tradedeal_scheme_skus.id', 'tradedeal_part_skus.host_desc', 'tradedeal_part_skus.brand_shortcut',
			'tradedeal_part_skus.pre_desc','pre_variant', 'pre_brand_shortcut',
			'tradedeal_part_skus.pre_cost', 'tradedeal_part_skus.host_code', 'tradedeal_part_skus.host_pcs_case', 'variant',
			'tradedeal_part_skus.id as host_id',
			'tradedeal_part_skus.pre_code', 'tradedeal_part_skus.pre_pcs_case')
			->join('tradedeal_part_skus', 'tradedeal_part_skus.id' , '=', 'tradedeal_scheme_skus.tradedeal_part_sku_id')
			->where('tradedeal_scheme_id', $scheme->id)->get();
	}

	
}