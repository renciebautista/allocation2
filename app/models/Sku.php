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
}