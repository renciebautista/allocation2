<?php

class AllocSchemeField extends \Eloquent {
	protected $fillable = [];
	protected $table = 'alloc_scheme_template_fields';

	public static function getFields($id){
		return self::join('allocation_report_scheme_fields', 'alloc_scheme_template_fields.field_id','=','allocation_report_scheme_fields.id')
			->where('template_id',$id)
			->orderBy('field_id')
			->get();
	}

	public static function getFieldList($id){
		$fields = self::select('allocation_report_scheme_fields.id as id')
			->join('allocation_report_scheme_fields', 'alloc_scheme_template_fields.field_id','=','allocation_report_scheme_fields.id')
			->where('template_id',$id)
			->orderBy('field_id')
			->get();

		$data = array();
		if(!empty($fields)){
			foreach ($fields as $field) {
				$data[] = $field->id;
			}
		}
		return $data;
	}
}