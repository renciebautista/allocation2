<?php

class ActivityNobudget extends \Eloquent {
	protected $fillable = [];

	public function budgettype()
    {
        return $this->belongsTo('BudgetType','budget_type_id','id');
    }

    // static
    public static function hasType($activity_id, $type_id){
    	$data = self::where('activity_id', $activity_id)
    		->where('budget_type_id', $type_id)
    		->count();

    	if($data > 0){
    		return 1;
    	}else{
    		return 0;
    	}
    }
}