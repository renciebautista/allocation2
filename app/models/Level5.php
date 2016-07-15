<?php

class Level5 extends \Eloquent {
	protected $table = 'level5';
	protected $fillable = [];
	public $timestamps = false;

	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('level5')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->l4_code)){
					$channel = new Level5;
					$channel->l4_code = $row->l4_code;
					$channel->l5_code = $row->l5_code;
					$channel->l5_desc = $row->l5_desc;
					$channel->trade_deal = ($row->trade_deal == 1) ? 1 : 0 ;
					$channel->rtm_tag = $row->rtm_tag;
					$channel->save();
				}				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}
}