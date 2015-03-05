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

    public function activitytype()
    {
        return $this->belongsTo('ActivityType','activity_type_id');
    }

    public function cycle()
    {
        return $this->belongsTo('Cycle','cycle_id');
    }

    public function createdby()
    {
        return $this->belongsTo('User','created_by');
    }


    // static function
	public static function validForDownload($activity){
		$return = array();
		$required_budget_type = ActivityTypeBudgetRequired::required($activity->activity_type_id);
		$valid_types = array();
		$required = array();
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
			
			$_valid = 1;
			foreach ($valid_types as $key => $value) {
				if($value == 0){
					$budget_type = BudgetType::find($key);
					$required[] = 'Budget Type '.$budget_type->budget_type. ' is required.';
				}
				$_valid &= $value;
			}
			$return['status'] = $_valid;
		}else{
			$return['status'] = 1;
		}

		$approver = ActivityApprover::getList($activity->id);
		if(count($approver)  == 0){
			$required[] = 'Activty approver is required.';
			$return['status'] = 0;
		}

		if($activity->cycle_id == 0){
			$required[] = 'Activty cycle is required.';
			$return['status'] = 0;
		}

		if($activity->division_code == 0){
			$required[] = 'Activty division is required.';
			$return['status'] = 0;
		}

		$category = ActivityCategory::selected_category($activity->id);
		if(count($category)  == 0){
			$required[] = 'Activty category is required.';
			$return['status'] = 0;
		}

		$brand = ActivityBrand::selected_brand($activity->id);
		if(count($brand) == 0){
			$required[] = 'Activty brand is required.';
			$return['status'] = 0;
		}

		$skus = ActivitySku::getList($activity->id);
		if(count($skus) == 0){
			$required[] = 'Activty skus involved is required.';
			$return['status'] = 0;
		}

		$objective = ActivityObjective::getList($activity->id);
		if(count($objective) == 0){
			$required[] = 'Activty objective is required.';
			$return['status'] = 0;
		}

		if($activity->background ==''){
			$required[] = 'Activty background is required.';
			$return['status'] = 0;
		}

		$customer = ActivityCustomer::customers($activity->id);
		if(count($customer) == 0){
			$required[] = 'Activty customer is required.';
			$return['status'] = 0;
		}

		$scheme = Scheme::getList($activity->id);
		if(count($scheme) == 0){
			$required[] = 'Activty scheme is required.';
			$return['status'] = 0;
		}

		$return['message'] = $required;
		return $return;;
		
	}

}