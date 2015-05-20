<?php

class UserDivisionFilter extends \Eloquent {
	protected $fillable = [];

	public static function getList($id){
		$list = array();
		$data = self::where('user_id',$id)->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->division_code;
			}
		}
		return $list;
	}
}