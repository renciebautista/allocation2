<?php

class TradedealSchemeSku extends \Eloquent {
	protected $fillable = ['qty', 'tradedeal_scheme_id', 'tradedeal_part_sku_id', 'pur_req', 'cost_to_sale', 'free_cost'];

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
			'tradedeal_part_skus.pre_desc','pre_variant', 'pre_brand_shortcut', 'pre_sku_format', 'host_sku_format',
			'tradedeal_part_skus.pre_cost', 'tradedeal_part_skus.host_code', 'tradedeal_part_skus.host_pcs_case', 'variant',
			'tradedeal_part_skus.id as host_id',
			'tradedeal_part_skus.pre_code', 'tradedeal_part_skus.pre_pcs_case', 'pur_req', 'cost_to_sale', 'free_cost')
			->join('tradedeal_part_skus', 'tradedeal_part_skus.id' , '=', 'tradedeal_scheme_skus.tradedeal_part_sku_id')
			->where('tradedeal_scheme_id', $scheme->id)->get();
	}


	public static function addHostSku($host_skus, $scheme){
		foreach ($host_skus as $value) {
			$host = TradedealPartSku::find($value);
			if($scheme->tradedeal_type_id == 1){
				
				if($scheme->tradedeal_uom_id == 1){
					$pur_req = $scheme->buy * $host->host_cost;
					$pre_cost = $scheme->free * $host->pre_cost;
				}else if($scheme->tradedeal_uom_id == 2){
					$pur_req = $scheme->buy * $host->host_cost * 12;
					$pre_cost = $scheme->free * $host->pre_cost * 12;
				}else{
					$pur_req = $scheme->buy * $host->host_cost * $host->host_pcs_case;
					$pre_cost = $scheme->free * $host->pre_cost * $host->pre_pcs_case;
				}
				TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
				'tradedeal_part_sku_id' => $value,
				'qty' => 1,
				'pur_req' => $pur_req,
				'free_cost' => $pre_cost,
				'cost_to_sale' =>  ($pre_cost/$pur_req) * 100]);
			}else if($scheme->tradedeal_type_id == 2){
				TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
				'tradedeal_part_sku_id' => $value,
				'qty' => $host_skus[$value]]);
			}else{
				TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
				'tradedeal_part_sku_id' => $value,
				'qty' => 1]);

				$lowest_cost = 0;
				$skus = [];
				foreach ($host_skus as $sku){
					if($lowest_cost == 0){
			        	$lowest_cost = $host->host_cost;
			        	$skus[0] = $host;
			        }else{
			        	if($lowest_cost > $host->host_cost){
				        	$lowest_cost = $host->host_cost;
				        	$skus[0] = $host;
				        }
			        }
				}
					
				$pur_req = 0;
				$pre_cost = 0;


				if($scheme->tradedeal_uom_id == 1){
					$pur_req = $scheme->buy * $host->host_cost;
					$pre_cost = $scheme->free * $host->pre_cost;
				}else if($scheme->tradedeal_uom_id == 2){
					$pur_req = $scheme->buy * $host->host_cost * 12;
					$pre_cost = $scheme->free * $host->pre_cost * 12;
				}else{
					$pur_req = $scheme->buy * $host->host_cost * $host->host_pcs_case;
					$pre_cost = $scheme->free * $host->pre_cost * $host->pre_pcs_case;
				}
				
				$scheme->pur_req = $pur_req;
				$scheme->free_cost = $pre_cost;
				$scheme->cost_to_sale = ($pre_cost/$pur_req) * 100;
				$scheme->save();
			}	
		}
	}
	
}