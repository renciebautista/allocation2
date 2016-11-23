<?php

class Pricelist extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getAll(){
		return self::orderBy('category_desc')
			->orderBy('brand_desc')
			->orderBy('sap_desc')
			->get();
	}

	public static function search($search){
		return self::where('launch',1)
			->where('active',1)
			->where(function($query) use ($search){
				$query->where('sap_code', 'LIKE' ,"%$search%")
					->orwhere('sap_desc', 'LIKE' ,"%$search%")
					->orwhere('division_desc', 'LIKE' ,"%$search%")
					->orwhere('category_desc', 'LIKE' ,"%$search%")
					->orwhere('brand_desc', 'LIKE' ,"%$search%")
					->orwhere('cpg_desc', 'LIKE' ,"%$search%");
			})
			->get();	
	}

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

	public static function getBrands(){
		return self::select('brand_code','division_desc', 'category_desc', 'brand_desc', 'brand_shortcut')
			->groupBy('brand_desc', 'category_desc',  'division_desc')
			->orderBy('division_desc')
			->orderBy('category_desc')
			->orderBy('brand_desc')
			->get();
	}

	public static function getFullBrand(){
		return self::select(DB::raw("CONCAT(division_desc,' - ',category_desc,' - ',brand_desc) as brand_desc"),'brand_code')
			->groupBy('brand_desc', 'category_desc',  'division_desc')
			->orderBy('division_desc')
			->orderBy('category_desc')
			->orderBy('brand_desc')
			->get();
	}


	public static function getBrand($brand_desc){
		return self::select('brand_code', 'brand_desc', 'brand_shortcut')
			->where('brand_desc', $brand_desc)
			->first();
	}

	public static function getBrandByCode($brand_code){
		return self::select('brand_code', 'brand_desc', 'brand_shortcut')
			->where('brand_code', $brand_code)
			->first();
	}

	public static function getBrandLists(){
		return self::select('brand_desc', 'brand_desc')
			->groupBy('brand_desc')
			->orderBy('brand_desc')
			->lists('brand_desc', 'brand_desc');
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
				if(!is_null($row->sap_code)){
					$cnt++;
					$sku = self::where('sap_code',$row->sap_code)->first();
					if(empty($sku)){
						// Helper::debug($row);
						$pricelist = new Pricelist;
						$pricelist->cpg_code = $row->cpg_code;
						$pricelist->cpg_desc = $row->cpg_desc;
						$pricelist->sap_code = $row->sap_code;
						$pricelist->sap_desc = $row->sap_desc;

						$pricelist->division_code = $row->division_code;
						$pricelist->division_desc = $row->division_desc;
						$pricelist->category_code = $row->category_code;
						$pricelist->category_desc = $row->category_desc;
						$pricelist->brand_code = $row->brand_code;
						$pricelist->brand_desc = $row->brand_desc;
						$pricelist->brand_shortcut = $row->brand_shortcut;

						$pricelist->pack_size = $row->pack_size;
						$pricelist->barcode = $row->barcode;
						$pricelist->case_code = $row->case_code;
						$pricelist->price_case = $row->price_case;
						$pricelist->price_case_tax = $row->price_case_tax;
						if($row->price == ''){
							$pricelist->price = 0;
						}else{
							$pricelist->price = $row->price;
						}
						
						$pricelist->srp = $row->srp;
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

	public static function updatePriceList($records){
		$cnt = 0;
		DB::beginTransaction();
			try {
			$records->each(function($row) use ($cnt) {
				if(!is_null($row->sap_code)){
					$cnt++;
					$sku = self::where('sap_code',$row->sap_code)->first();
					if(empty($sku)){
						$pricelist = new Pricelist;
						$pricelist->cpg_code = $row->cpg_code;
						$pricelist->cpg_desc = $row->cpg_desc;
						$pricelist->sap_code = $row->sap_code;
						$pricelist->sap_desc = $row->sap_desc;

						$pricelist->division_code = $row->division_code;
						$pricelist->division_desc = $row->division_desc;
						$pricelist->category_code = $row->category_code;
						$pricelist->category_desc = $row->category_desc;
						$pricelist->brand_code = $row->brand_code;
						$pricelist->brand_desc = $row->brand_desc;

						$pricelist->pack_size = $row->pack_size;
						$pricelist->barcode = $row->barcode;
						$pricelist->case_code = $row->case_code;
						$pricelist->price_case = $row->price_case;
						$pricelist->price_case_tax = $row->price_case_tax;
						$pricelist->price = $row->price;
						$pricelist->srp = $row->srp;
						$pricelist->active = 1;
						$pricelist->launch = 0;
						$pricelist->save();
					}else{
						$sku->cpg_code = $row->cpg_code;
						$sku->cpg_desc = $row->cpg_desc;
						$sku->sap_code = $row->sap_code;
						$sku->sap_desc = $row->sap_desc;

						$sku->division_code = $row->division_code;
						$sku->division_desc = $row->division_desc;
						$sku->category_code = $row->category_code;
						$sku->category_desc = $row->category_desc;
						$sku->brand_code = $row->brand_code;
						$sku->brand_desc = $row->brand_desc;

						$sku->pack_size = $row->pack_size;
						$sku->barcode = $row->barcode;
						$sku->case_code = $row->case_code;
						$sku->price_case = $row->price_case;
						$sku->price_case_tax = $row->price_case_tax;
						$sku->price = $row->price;
						$sku->srp = $row->srp;
						$sku->active = 1;
						$sku->launch = 0;
						$sku->update();
					}
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
		}

		return $cnt;
	}

	public static function division($code){
		return self::select('division_code', 'division_desc')
			->where('division_code', $code)
			->groupBy('division_code')
			->first();
	}

	public static function divisions(){
		return self::select('division_code', 'division_desc')
			->groupBy('division_code')
			->orderBy('division_desc')->lists('division_desc', 'division_code');
	}

	public static function category($code){
		return self::select('category_code', 'category_desc')
			->where('category_code', $code)
			->groupBy('category_code')
			->first();
	}

	public static function brand($code){
		return self::select('cpg_code', 'brand_desc','cpg_desc')
			->where('brand_desc', $code)
			->groupBy('brand_desc')
			->first();
	}

	public static function involves($filter,$activity = null){
		$data = self::select('sap_code', DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
			->where('active',1)
			->where('launch',0)
			->whereIn('brand_desc',$filter)
			->orderBy('full_desc')->get();

			if(Auth::user()->inRoles(['PROPONENT'])){
				$user_id = Auth::id();
			}else{
				$user_id = $activity->created_by;
			}

		if(!is_null($activity)){
			$data2 = LaunchSkuAccess::select('sap_code', DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
			->join('pricelists','pricelists.sap_code','=','launch_sku_access.sku_code','left')
			->where('launch_sku_access.user_id',$user_id)
			->where('active',1)
			->where('launch',1)
			->whereIn('brand_desc',$filter)
			->orderBy('full_desc')->get();

			foreach($data2 as $row) {
			    $data->add($row);
			}
		}else{
			$data2 = LaunchSkuAccess::select('sap_code', DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
			->join('pricelists','pricelists.sap_code','=','launch_sku_access.sku_code','left')
			->where('launch_sku_access.user_id', Auth::user()->id)
			->where('active',1)
			->where('launch',1)
			->whereIn('brand_desc',$filter)
			->orderBy('full_desc')->get();

			foreach($data2 as $row) {
			    $data->add($row);
			}
		}
		
		return $data->lists('full_desc', 'sap_code');
	}

	public static function updateBrand($records){
		$records->each(function($row){
			if(!is_null($row->brand_desc)){
				self::where('brand_desc', $row->brand_desc)
					->where('category_desc', $row->category_desc)
					->where('division_desc', $row->division_desc)
					->update(['brand_shortcut' => $row->brand_shortcut]);
			}
			
		});
	}

	public static function import($records){
		DB::beginTransaction();
			try {
			$records->each(function($row)  {
				if(!is_null($row->sap_code)){
					$item = self::where('sap_code',$row->sap_code)->first();
					if(empty($item)){
						// change item to pricelist
						$item = new Pricelist;
						$item->cpg_code = $row->cpg_code;
						$item->cpg_desc = $row->cpg_desc;
						$item->sap_code = $row->sap_code;
						$item->sap_desc = $row->sap_desc;
						$item->division_code = $row->division_code;
						$item->division_desc = $row->division_desc;
						$item->category_code = $row->category_code;
						$item->category_desc = $row->category_desc;
						$item->brand_code = $row->brand_code;
						$item->brand_desc = $row->brand_desc;
						$item->brand_shortcut = $row->brand_shortcut;
						$item->pack_size = $row->pack_size;
						$item->barcode = $row->barcode;
						$item->case_code = $row->case_code;
						$item->price_case = $row->price_case;
						$item->price_case_tax = $row->price_case_tax;
						$item->price = $row->price;
						if($row->srp == null){
							$item->srp = 0.00;
						}else{
							$item->srp = $row->srp;
						}
						$item->sku_format = $row->sku_format;
						$item->active = $row->active;
						$item->launch = $row->launch;
						$item->save();
					}else{
						$item->cpg_code = $row->cpg_code;
						$item->cpg_desc = $row->cpg_desc;
						$item->sap_code = $row->sap_code;
						$item->sap_desc = $row->sap_desc;
						$item->division_code = $row->division_code;
						$item->division_desc = $row->division_desc;
						$item->category_code = $row->category_code;
						$item->category_desc = $row->category_desc;
						$item->brand_code = $row->brand_code;
						$item->brand_desc = $row->brand_desc;
						$item->brand_shortcut = $row->brand_shortcut;
						$item->pack_size = $row->pack_size;
						$item->barcode = $row->barcode;
						$item->case_code = $row->case_code;
						$item->price_case = $row->price_case;
						$item->price_case_tax = $row->price_case_tax;
						$item->price = $row->price;
						$item->srp = $row->srp;
						$item->sku_format = $row->sku_format;
						$item->active = $row->active;
						$item->launch = $row->launch;
						$item->update();
					}
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}

}