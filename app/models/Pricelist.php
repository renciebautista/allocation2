<?php

class Pricelist extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function items(){
		return self::select('sap_code', DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
			->orderBy('sap_desc')
			->where('active', 1)
			->where('launch', 0)
			->lists('full_desc', 'sap_code');
	}

	public static function getSku($sap_code){
		return self::where('sap_code',$sap_code)->first();
	}

	public static function updateCpg($records){
		$records->each(function($row){
				if(!is_null($row->id)){
					$sku = self::find($row->id);
					if(!empty($sku)){
						$sku->cpg_code = $row->cpg_code;
						$sku->update();
					}
				}
				
			});
	}

	public static function insertLaunch($records){
		$cnt = 0;
		DB::beginTransaction();
			try {
			$records->each(function($row) use ($cnt) {
				if(!is_null($row->sku_code)){
					$cnt++;
					$sku = self::where('sap_code',$row->sku_code)->first();
					if(empty($sku)){
						$pricelist = new Pricelist;
						$pricelist->cpg_code = $row->cpg_code;
						$pricelist->sap_code = $row->sku_code;
						$pricelist->sap_desc = $row->sku_desc;
						$pricelist->pack_size = $row->pack_size;
						$pricelist->barcode = $row->barcode;
						$pricelist->case_code = $row->case_code;
						$pricelist->price_case = $row->price_case;
						$pricelist->price_case_tax = $row->price_case_tax;
						$pricelist->price = $row->price_pc;
						$pricelist->srp = $row->srp_pc;
						$pricelist->active = 1;
						$pricelist->launch = 1;
						$pricelist->save();
					}

				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
		}

		return $cnt;
	}
}