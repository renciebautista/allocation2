<?php

class AccountGroup extends \Eloquent {
	protected $fillable = ['account_group_code', 'account_group_name', 'show_in_summary'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->account_group_code)){
				$attributes = array(
					'account_group_code' => $row->account_group_code,
					'account_group_name' => $row->account_group_name,
					'show_in_summary' => ($row->show_in_summary == 'Y') ? 1 : 0);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function import($records){
		DB::beginTransaction();
			try {
				DB::table('account_groups')->truncate();

			$records->each(function($row)  {
				if(!is_null($row->account_group_name)){
					$accountgroup = self::where('account_group_code',$row->account_group_code)
						->where('account_group_name',$row->account_group_name)
						->where('show_in_summary',$row->show_in_summary)
						->first();

					if(empty($accountgroup)){

						$accountgroup = new AccountGroup;
						$accountgroup->account_group_code = $row->account_group_code;
						$accountgroup->account_group_name = $row->account_group_name;
						$accountgroup->show_in_summary = $row->show_in_summary;
	
						$accountgroup->save();
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