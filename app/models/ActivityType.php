<?php

class ActivityType extends \Eloquent {
	protected $fillable = ['id', 'activity_type', 'uom','activity_grouping_id'];
	public static $rules = array(
        'activity_type' => 'required|between:4,128|unique:activity_types',
        'uom' => 'required'
    );
	public $timestamps = false;

	public static function batchInsert($records){
		if(!empty($records)){
			foreach ($records as $row) {
				if(!is_null($row['activity_type_code'])){
					$attributes = array(
						'id' => $row['activity_type_code'],
						'activity_type' => $row['activity_type'],
						'uom' => strtoupper($row['uom']),
						'activity_grouping_id' => $row['activity_grouping_id']);
					self::insert($attributes);

					if($row['pe'] == "Y"){
						ActivityTypeBudgetRequired::insert( array('activity_type_id' => $row['activity_type_code'], 'budget_type_id' => 2));
					}

					if($row['tts'] == "Y"){
						ActivityTypeBudgetRequired::insert( array('activity_type_id' => $row['activity_type_code'], 'budget_type_id' => 1));
					}
				}
			}
		}
	}

	public static function search($filter){
		return self::where('activity_type', 'LIKE' ,"%$filter%")
			->orderBy('activity_type')
			->get();
	}

	public static function getLists(){
		return self::orderBy('activity_type')->lists('activity_type', 'id');
	}

	public static function getWithNetworks(){
		return self::whereIn('id', function($query){
		    $query->select('activitytype_id')
		    ->from('activity_type_networks')
		    ->groupBy('activitytype_id')
		    ->get();
		})
		->orderBy('activity_type')->lists('activity_type', 'id');
	}
}