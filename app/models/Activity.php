<?php

class Activity extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'scope' => 'required|integer|min:1',
		// 'planner' => 'required|integer|min:1',
		// 'approver' => 'required|integer|min:1',
		'activity_type' => 'required|integer|min:1',
		// 'download_date' => 'required',
		// 'implementation_date' => 'required',
		'activity_title' => 'required',
		
		// 'cycle' => 'required|integer|min:1',
		// 'division' => 'required|integer|min:1',
		// 'background' => 'required'
	);

	public function status()
    {
        return $this->belongsTo('ActivityStatus','status_id','id');
    }

    public function scope()
    {
        return $this->belongsTo('ScopeType','scope_type_id','id');
    }


    // static function
	public static function validForDownload($activity){
		$return = array();
		$required_budget_type = ActivityTypeBudgetRequired::required($activity->activity_type_id);
		$valid_types = array();

		if(!empty($required_budget_type)){
			foreach ($required_budget_type as $value) {
				$status = 0;
				if(ActivityBudget::hasType($activity->id,$value) || ActivityNobudget::hasType($activity->id,$value)){
					$status = 1;
				}
				$valid_types[$value] = $status;
			}
		}

		if(!empty($valid_types)){
			$required = array();
			$_valid = 1;
			foreach ($valid_types as $key => $value) {
				if($value == 0){
					$budget_type = BudgetType::find($key);
					$required[] = 'Budget Type '.$budget_type->budget_type. ' is required.';
				}
				$_valid &= $value;
			}
			$return['status'] = $_valid;
			$return['message'] = $required;
			return $return;
		}else{
			$return['status'] = 1;
			return $return;
		}
	}

}