<?php

class AllocReportPerGroup extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getAvailableFields($id){
		return self::select('allocation_report_scheme_fields.id','allocation_report_scheme_fields.desc_name')
			->join('allocation_report_scheme_fields', 'alloc_report_per_groups.filter_id','=','allocation_report_scheme_fields.id')
			->where('alloc_report_per_groups.role_id',$id)
			->orderBy('allocation_report_scheme_fields.id')
			->get();
	}

}