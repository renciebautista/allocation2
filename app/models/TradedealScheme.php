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
			->get();
		foreach ($data as $key => $value) {
			$host_skus = TradedealSchemeSku::with('host')
				->where('tradedeal_scheme_id', $value->id)->get();

			$channels = TradedealSchemeChannel::where('tradedeal_scheme_id', $value->id)->get();
			$data[$key]->host_skus = $host_skus;
			$data[$key]->channels = $channels;
		}
		return $data;
	}

	public function premium(){
		return $this->pre_desc .' - '.$this->pre_code;
		// if($this->tradedeal_type_id == 1){
		// 	return 'See participating variants details';
		// }else{
			
		// }
	}
}