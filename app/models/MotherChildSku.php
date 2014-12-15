<?php

class MotherChildSku extends \Eloquent {
	protected $fillable = ['mother_sku', 'child_sku'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->mother_sku)){
				$attributes = array(
					'mother_sku' => $row->mother_sku,
					'child_sku' => $row->child_sku);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}
}