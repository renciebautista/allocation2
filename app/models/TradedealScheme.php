<?php

class TradedealScheme extends \Eloquent {
	protected $fillable = [];

	public function dealType(){
		return $this->belongsTo('TradedealType','tradedeal_type_id','id');
	}

	public function dealUom(){
		return $this->belongsTo('TradedealUom','tradedeal_uom_id','id');
	}

	public static function getScheme($id){
		$data = self::where('tradedeal_id', $id)
			->with('dealType')
			->orderBy('tradedeal_type_id')
			->orderBy('tradedeal_uom_id')
			->get();

		// foreach ($data as $key => $value) {
		// 	$host_skus = TradedealSchemeSku::with('host')
		// 		->where('tradedeal_scheme_id', $value->id)->get();

		// 	$channels = TradedealSchemeChannel::where('tradedeal_scheme_id', $value->id)->get();
		// 	$data[$key]->host_skus = $host_skus;
		// 	$data[$key]->channels = $channels;
		// }

		foreach ($data as $key => $value) {
			$channels = TradedealSchemeChannel::getSelectedDetails($value);
			$tradeal = Tradedeal::find($value->tradedeal_id);
			if($value->tradedeal_type_id == 1){
				$host_skus = TradedealSchemeSku::getHostSku($value);
				foreach ($host_skus as $x => $value) {
					$host_skus[$x]->desc_variant = $value->host_desc. ' '.$value->variant;
					$host_skus[$x]->pre_variant = $value->pre_desc. ' '.$value->pre_variant;
				}
				$data[$key]->host_skus = $host_skus;
			}

			if($value->tradedeal_type_id == 2){
				
			}

			if($value->tradedeal_type_id == 3){
				$host_skus = TradedealSchemeSku::getHostSku($value);
				$_host_sku = [];
				foreach ($host_skus as $host_sku) {
					$_host_sku[] = $host_sku->host_desc.' '.$host_sku->variant;
				}
				$o = new stdClass();
				$o->desc_variant = implode(" / ", $_host_sku);
				if($tradeal->nonUlpPremium()){
					$o->pre_variant = $value->pre_desc .' '.$value->pre_variant;
					$o->pur_req = $value->pur_req;
					$o->cost_to_sale = $value->cost_to_sale;
				}else{
					$part_sku = TradedealPartSku::find($value->pre_id);
					$o->pre_variant = $part_sku->preDesc();
					$o->pur_req = $value->pur_req;
					$o->cost_to_sale = $value->cost_to_sale;
				}
				
				$_host[] = $o;
					$data[$key]->host_skus = $_host;
			}
			$data[$key]->channels = $channels;
		}
		return $data;
	}

	public function premium(){
		return $this->pre_desc .' - '.$this->pre_code;
	}


	public static function total_deals($activity){
		return self::join('');
	}
	
}