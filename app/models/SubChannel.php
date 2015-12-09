<?php

class SubChannel extends \Eloquent {
	protected $fillable = ['coc_03_code', 'channel_code'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->coc_03_code)){
				$attributes = array(
					'coc_03_code' => $row->coc_03_code,
					'channel_code' => $row->channel_code);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getAll(){
		return self::join('channels', 'channels.channel_code', '=' , 'sub_channels.channel_code')
			->orderBy('channels.channel_code')
			->get();
	}

	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('sub_channels')->truncate();
			$records->each(function($row)  {
				if(!is_null($row->channel_code)){
					$subchannel = new Subchannel;
					$subchannel->coc_03_code = $row->coc_03_code;
					$subchannel->channel_code = $row->channel_code;
					$subchannel->save();
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			// dd($e);
			DB::rollback();
		}
	}
}