<?php

class Level4 extends \Eloquent {
	// protected $table = 'level4';
	// protected $fillable = ['coc_03_code', 'l4_code', 'l4_desc'];
	// public $timestamps = false;

	// public static function import($records){
	// 	DB::beginTransaction();
	// 		try {
	// 			DB::table('level4')->truncate();
	// 		$records->each(function($row)  {
	// 			if(!is_null($row->coc_03_code)){
	// 				$channel = new Level4;
	// 				$channel->coc_03_code = $row->coc_03_code;
	// 				$channel->l4_code = $row->l4_code;
	// 				$channel->l4_desc = $row->l4_desc;
	// 				$channel->save();
	// 			}				
	// 		});
	// 		DB::commit();
	// 	} catch (\Exception $e) {
	// 		dd($e);
	// 		DB::rollback();
	// 	}
	// }
}