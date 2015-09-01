<?php

class Sku extends \Eloquent {
	protected $fillable = ['sku_code', 'sku_desc', 'division_code', 'division_desc',
		'category_code', 'category_desc', 'brand_code', 'brand_desc', 'cpg_code',
		'cpg_desc', 'packsize_code', 'packsize_desc'];
	public $timestamps = false;


	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->division_code)){
				$attributes = array(
					'sku_code' => $row->sku_code,
					'sku_desc' => $row->sku_desc,
					'division_code' => $row->division_code,
					'division_desc' => $row->division_desc,
					'category_code' => $row->category_code,
					'category_desc' => $row->category_desc,
					'brand_code' => $row->brand_code,
					'brand_desc' => $row->brand_desc,
					'cpg_code' => $row->cpg_code,
					'cpg_desc' => $row->cpg_desc,
					'packsize_code' => $row->packsize_code,
					'packsize_desc' => $row->packsize_desc);
				self::updateOrCreate($attributes, $attributes);
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
					$sku = self::where('sku_code',$row->sku_code)->first();
					if(empty($sku)){
						$sku = new Sku;
						$sku->sku_code = $row->sku_code;
						$sku->sku_desc = $row->sku_desc;
						$sku->division_code = $row->division_code;
						$sku->division_desc = $row->division_desc;
						$sku->category_code = $row->category_code;
						$sku->category_desc = $row->category_desc;
						$sku->brand_code = $row->brand_code;
						$sku->brand_desc = $row->brand_desc;
						$sku->cpg_code = $row->cpg_code;
						$sku->cpg_desc = $row->cpg_desc;
						$sku->packsize_code = $row->packsize_code;
						$sku->packsize_desc = $row->packsize_desc;
						$sku->active = 1;
						$sku->launch = 1;
						$sku->save();	
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

	public static function getDivisionLists(){
		return self::select('division_code', 'division_desc')
				->groupBy('division_code')
				->orderBy('division_desc')
				->lists('division_desc', 'division_code');
	}

	public static function category($code){
		return self::select('category_code', 'category_desc')
			->where('category_code', $code)
			->groupBy('category_code')
			->first();
	}

	public static function categories($division_code){
		return self::select('category_code', 'category_desc')
			->where('division_code',$division_code)
			->groupBy('category_code')
			->orderBy('category_desc')
			->lists('category_desc', 'category_code');
	}

	public static function brand($code){
		return self::select('cpg_code', 'brand_desc','cpg_desc')
			->where('cpg_code', $code)
			->groupBy('cpg_code')
			->first();
	}

	public static function brands($categories){
		return self::select('brand_code', 'brand_desc')
			->whereIn('category_code',$categories)
			->groupBy('brand_code')
			->orderBy('brand_desc')
			->lists('brand_desc', 'brand_code');
	}

	public static function items($division_code,$categories,$brands){
		return self::select('sku_code', DB::raw('CONCAT(UPPER(sku_desc), " - ", sku_code) AS full_desc'))
			->whereIn('division_code',$division_code)
			->whereIn('category_code',$categories)
			->whereIn('brand_desc',$brands)
			->orderBy('sku_desc')
			->lists('full_desc', 'sku_code');
	}



	public static function divisions(){
		return self::select('division_code', 'division_desc')
			->groupBy('division_code')
			->orderBy('division_desc')->lists('division_desc', 'division_code');
	}

	public static function getSku($sku_code){
		return self::where('sku_code',$sku_code)->first();
	}

	public static function getLaunchSku($sku_code){
		return self::where('sku_code',$sku_code)
			->where('launch',1)
			->where('active',1)
			->first();
	}

	
}