<?php

class AllocationReportFilter extends \Eloquent {
	protected $fillable = [];


	public static function getList($template_id,$type_id){
		$list = array();
		$data = self::where('template_id',$template_id)
			->where('filter_type_id',$type_id)
			->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->filter_id;
			}
		}
		return $list;
	}
}