<?php

class SchemeRepository extends \Eloquent {
	protected $fillable = [];

	public static function addSchemeSku($scheme){
		SchemeSku::where('scheme_id',$scheme->id)->delete();
		$skus = array();
		foreach (Input::get('skus') as $sku){
			$scheme_sku = Sku::getSku($sku);
			$skus[] = array('scheme_id' => $scheme->id, 
				'sku' => $sku,
				'sku_desc' => $scheme_sku->sku_desc,
				'division_code' => $scheme_sku->division_code,
				'division_desc' => $scheme_sku->division_desc,
				'category_code' => $scheme_sku->category_code,
				'category_desc' => $scheme_sku->category_desc,
				'brand_code' => $scheme_sku->brand_code,
				'brand_desc' => $scheme_sku->brand_desc,
				'cpg_code' => $scheme_sku->cpg_code,
				'cpg_desc' => $scheme_sku->cpg_desc,
				'packsize_code' => $scheme_sku->packsize_code,
				'packsize_desc' => $scheme_sku->packsize_desc
				);
		}
		if(count($skus)>0){
			SchemeSku::insert($skus);
		}
	}

	public static function addSchemeSkuInvole($scheme){
		SchemeHostSku::where('scheme_id',$scheme->id)->delete();
		if (Input::has('involve')){
			$hosts = array();
			foreach (Input::get('involve') as $sap_code){
				$pricelist = Pricelist::getSku($sap_code);
				$hosts[] = array('scheme_id' => $scheme->id, 
					'sap_code' => $sap_code,
					'sap_desc' => $pricelist->sap_desc,
					'pack_size' => $pricelist->pack_size,
					'barcode' => $pricelist->barcode,
					'case_code' => $pricelist->case_code,
					'price_case' => $pricelist->price_case,
					'price_case_tax' => $pricelist->price_case_tax,
					'price' => $pricelist->price,
					'srp' => $pricelist->srp);
			}
			if(count($hosts)>0){
				SchemeHostSku::insert($hosts);
			}
		}
	}

	public static function addSchemePremiumSku($scheme){
		SchemePremuimSku::where('scheme_id',$scheme->id)->delete();
		if (Input::has('premuim')){
			$premuim = array();
			foreach (Input::get('premuim') as $sap_code){
				$pricelist = Pricelist::getSku($sap_code);
				$premuim[] = array('scheme_id' => $scheme->id, 
					'sap_code' => $sap_code,
					'sap_desc' => $pricelist->sap_desc,
					'pack_size' => $pricelist->pack_size,
					'barcode' => $pricelist->barcode,
					'case_code' => $pricelist->case_code,
					'price_case' => $pricelist->price_case,
					'price_case_tax' => $pricelist->price_case_tax,
					'price' => $pricelist->price,
					'srp' => $pricelist->srp);
			}
			SchemePremuimSku::insert($premuim);
		}
	}

	public static function duplicate($id){
		DB::beginTransaction();

		try {
			$scheme = Scheme::find($id);
			$new_scheme = new Scheme;
			$new_scheme->activity_id = $scheme->activity_id;
			$new_scheme->name = $scheme->name;
			$new_scheme->item_code = $scheme->item_code;
			$new_scheme->item_desc = $scheme->item_desc;
			$new_scheme->item_barcode = $scheme->item_barcode;
			$new_scheme->item_casecode = $scheme->item_casecode;
			$new_scheme->pr = $scheme->pr;
			$new_scheme->srp_p = $scheme->srp_p;
			$new_scheme->other_cost = $scheme->other_cost;
			$new_scheme->ulp = $scheme->ulp;
			$new_scheme->cost_sale = $scheme->cost_sale;
			$new_scheme->quantity = $scheme->quantity;
			$new_scheme->deals = $scheme->deals;
			$new_scheme->total_deals = $scheme->total_deals;
			$new_scheme->total_cases = $scheme->total_cases;
			$new_scheme->tts_r = $scheme->tts_r;
			$new_scheme->pe_r = $scheme->pe_r;
			$new_scheme->lpat = $scheme->lpat;
			$new_scheme->total_cost = $scheme->total_cost;
			$new_scheme->user_id = Auth::id();
			$new_scheme->final_alloc = $scheme->final_alloc;
			$new_scheme->final_total_deals = $scheme->final_total_deals;
			$new_scheme->final_total_cases = $scheme->final_total_cases;
			$new_scheme->final_tts_r = $scheme->final_tts_r;
			$new_scheme->final_pe_r = $scheme->final_pe_r;
			$new_scheme->final_total_cost = $scheme->final_total_cost;
			$new_scheme->ulp_premium = $scheme->ulp_premium;
			$new_scheme->compute = $scheme->compute;
			$new_scheme->with_upload = $scheme->with_upload;
			$new_scheme->save();

			// add skus
			$scheme_skus = SchemeSku::where('scheme_id',$scheme->id)->get();
			if(!empty($scheme_skus)){
				foreach ($scheme_skus as $sku) {
					SchemeSku::insert(array('scheme_id' => $new_scheme->id, 
						'sku' => $sku->sku,
						'sku_desc' => $sku->sku_desc,
						'division_code' => $sku->division_code,
						'division_desc' => $sku->division_desc,
						'category_code' => $sku->category_code,
						'category_desc' => $sku->category_desc,
						'brand_code' => $sku->brand_code,
						'brand_desc' => $sku->brand_desc,
						'cpg_code' => $sku->cpg_code,
						'cpg_desc' => $sku->cpg_desc,
						'packsize_code' => $sku->packsize_code,
						'packsize_desc' => $sku->packsize_desc));
				}
			}
			// add host sku
			$host_skus = SchemeHostSku::where('scheme_id',$scheme->id)->get();
			if(!empty($host_skus)){
				foreach ($host_skus as $sku) {
					SchemeHostSku::insert(array('scheme_id' => $new_scheme->id, 
						'sap_code' => $sku->sap_code,
						'sap_desc' => $sku->sap_desc,
						'pack_size' => $sku->pack_size,
						'barcode' => $sku->barcode,
						'case_code' => $sku->case_code,
						'price_case' => $sku->price_case,
						'price_case_tax' => $sku->price_case_tax,
						'price' => $sku->price,
						'srp' => $sku->srp));
				}
			}

			// add premuim sku
			$premuim_skus = SchemePremuimSku::where('scheme_id',$scheme->id)->get();
			if(!empty($premuim_skus)){
				foreach ($premuim_skus as $sku) {
					SchemePremuimSku::insert(array('scheme_id' => $new_scheme->id, 
						'sap_code' => $sku->sap_code,
						'sap_desc' => $sku->sap_desc,
						'pack_size' => $sku->pack_size,
						'barcode' => $sku->barcode,
						'case_code' => $sku->case_code,
						'price_case' => $sku->price_case,
						'price_case_tax' => $sku->price_case_tax,
						'price' => $sku->price,
						'srp' => $sku->srp));
				}
			}

			$allocations = Allocation::schemeAllocations($scheme->id);
			$last_area_id = 0;
			$last_shipto_id = 0;
			foreach ($allocations as $allocation) {
				$scheme_alloc = new SchemeAllocation;

				if((!empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
					$scheme_alloc->customer_id = $last_area_id;
				}

				if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
					$scheme_alloc->customer_id = $last_area_id;
					$scheme_alloc->shipto_id = $last_shipto_id;
				}	
				
				$scheme_alloc->scheme_id = $new_scheme->id;
				$scheme_alloc->group_code = $allocation->group_code;
				$scheme_alloc->group = $allocation->group;
				$scheme_alloc->area_code = $allocation->area_code;
				$scheme_alloc->area = $allocation->area;
				$scheme_alloc->sold_to_code = $allocation->sold_to_code;
				$scheme_alloc->sob_customer_code = $allocation->sob_customer_code;
				$scheme_alloc->sold_to = $allocation->sold_to;
				$scheme_alloc->ship_to_code = $allocation->ship_to_code;
				$scheme_alloc->ship_to = $allocation->ship_to;
				$scheme_alloc->channel_code = $allocation->channel_code;
				$scheme_alloc->channel = $allocation->channel;
				$scheme_alloc->account_group_code = $allocation->account_group_code;
				$scheme_alloc->account_group_name = $allocation->account_group_name;
				$scheme_alloc->outlet = $allocation->outlet;
				$scheme_alloc->sold_to_gsv = $allocation->sold_to_gsv;
				$scheme_alloc->forced_sold_to_gsv = $allocation->forced_sold_to_gsv;
				$scheme_alloc->sold_to_gsv_p = $allocation->sold_to_gsv_p;
				$scheme_alloc->forced_sold_to_gsv_p = $allocation->forced_sold_to_gsv_p;
				$scheme_alloc->sold_to_alloc = $allocation->sold_to_alloc;
				$scheme_alloc->forced_sold_to_alloc = $allocation->forced_sold_to_alloc;
				$scheme_alloc->ship_to_gsv = $allocation->ship_to_gsv;
				$scheme_alloc->forced_ship_to_gsv = $allocation->forced_ship_to_gsv;
				$scheme_alloc->ship_to_gsv_p = $allocation->ship_to_gsv_p;
				$scheme_alloc->forced_ship_to_gsv_p = $allocation->forced_ship_to_gsv_p;
				$scheme_alloc->ship_to_alloc = $allocation->ship_to_alloc;
				$scheme_alloc->forced_ship_to_alloc = $allocation->forced_ship_to_alloc;
				$scheme_alloc->outlet_to_gsv = $allocation->outlet_to_gsv;
				$scheme_alloc->forced_outlet_to_gsv = $allocation->forced_outlet_to_gsv;
				$scheme_alloc->outlet_to_gsv_p = $allocation->outlet_to_gsv_p;
				$scheme_alloc->forced_outlet_to_gsv_p = $allocation->forced_outlet_to_gsv_p;
				$scheme_alloc->outlet_to_alloc = $allocation->outlet_to_alloc;
				$scheme_alloc->forced_outlet_to_alloc = $allocation->forced_outlet_to_alloc;
				$scheme_alloc->multi = $allocation->multi;
				$scheme_alloc->computed_alloc = $allocation->computed_alloc;
				$scheme_alloc->force_alloc = $allocation->force_alloc;
				$scheme_alloc->final_alloc = $allocation->final_alloc;
				$scheme_alloc->in_deals = $allocation->in_deals;
				$scheme_alloc->in_cases = $allocation->in_cases;
				$scheme_alloc->tts_budget = $allocation->tts_budget;
				$scheme_alloc->pe_budget = $allocation->pe_budget;
				$scheme_alloc->show = $allocation->show;
				$scheme_alloc->save();

				if((empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
					$last_area_id = $scheme_alloc->id;
				}

				if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
					$last_shipto_id = $scheme_alloc->id;
				}
			}
			$data['scheme_id'] = $new_scheme->id;
			DB::commit();
			$data['class'] = 'alert-success';
			$data['message'] = 'Scheme  successfully duplicated.';
		} catch (\Exception $e) {
			DB::rollback();
			// echo $e;
		    $data['class'] = 'alert-danger';
			$data['message'] = 'Cannot duplicate activity.';
				// something went wrong
		}

		return $data;
	}

	public static function newSkus($scheme){
		$old_skus = array();
		$records = SchemeSku::select('sku')->where('scheme_id',$scheme->id)->get();
		foreach ($records as $value) {
			$old_skus[] = $value->sku;
		}
		foreach (Input::get('skus') as $sku){
			if(in_array($sku, $old_skus)){
				return false;
			}
		}

		return true;
	}

	public static function newValue($old_value,$new_value){
		if($old_value != $new_value){
			return true;
		}

		return false;
	}
}