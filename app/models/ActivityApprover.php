<?php

class ActivityApprover extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getList($id){
		$list = array();
		$data = self::where('activity_id',$id)->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->user_id;
			}
		}
		return $list;
	}
}