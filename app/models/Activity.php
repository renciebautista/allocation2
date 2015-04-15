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

    public function objectives()
    {
        return $this->belongsToMany('Objective','activity_objectives');
    }

   	public function pmog()
    {
        return $this->belongsToMany('User', 'activity_planners', 'activity_id', 'user_id');
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

		// $skus = ActivitySku::getList($activity->id);
		// if(count($skus) == 0){
		// 	$required[] = 'Activty skus involved is required.';
		// 	$return['status'] = 0;
		// }

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

	// public static function search($user_id){
	// 	return DB::select( DB::raw("SELECT *,activity_statuses.status,cycles.cycle_name,
	// 		scope_types.scope_name,activity_types.activity_type,concat(first_name, ' ', last_name) as planner
	// 	 	FROM activities
	// 		join activity_statuses on activities.status_id = activity_statuses.id
	// 		join cycles on activities.cycle_id = cycles.id
	// 		join scope_types on activities.scope_type_id = scope_types.id
	// 		join activity_types on activities.activity_type_id = activity_types.id
	// 		left join activity_planners on activities.id = activity_planners.activity_id
	// 		left join users on activity_planners.user_id = users.id
	// 		WHERE created_by = :user_id
	// 		ORDER BY activities.created_at desc"), 
	// 		array('user_id' => $user_id,
	// 	 ));
	// 	// return self::where('created_by', '=', $user_id)
	// 	// 	->orderBy('activities.created_at','desc')
	// 	// 	->get();
	// }

	public static function search($user_id = 0,$status,$cycle,$scope,$type,$pmog,$title){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id')
			->join('users', 'activity_planners.user_id','=','users.id')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where(function($query) use ($user_id){
				if($user_id > 0){
					$query->where('activities.created_by', $user_id);
				}
			})
			->where(function($query) use ($title){
				$query->where('activities.circular_name', 'LIKE' ,"%$title%");
			})
			->where(function($query) use ($status){
				if($status > 0){
					$query->where('activities.status_id', $status);
				}
			})
			->where(function($query) use ($cycle){
				if($cycle > 0){
					$query->where('activities.cycle_id', $cycle);
				}
			})
			->where(function($query) use ($scope){
				if($scope > 0){
					$query->where('activities.scope_type_id', $scope);
				}
			})
			->where(function($query) use ($type){
				if($type > 0){
					$query->where('activities.activity_type_id', $type);
				}
			})
			->where(function($query) use ($pmog){
				if($pmog > 0){
					$query->where('activity_planners.user_id', $pmog);
				}
			})
			->get();
	}

	public static function availableCycles($user_id = null){
		return self::select('cycles.cycle_name','cycles.id')
			->where(function($query) use ($user_id){
				if(!is_null($user_id)){
					$query->where('activities.created_by', $user_id);
				}
			})
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->groupBy('cycles.cycle_name')
			->orderBy('cycles.cycle_name')
			->get()
			->lists('cycle_name','id');
	}

	public static function availableScopes($user_id = null){
		return self::select('scope_types.scope_name','scope_types.id')
			->where(function($query) use ($user_id){
				if(!is_null($user_id)){
					$query->where('activities.created_by', $user_id);
				}
			})
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->groupBy('scope_types.scope_name')
			->orderBy('scope_types.scope_name')
			->get()
			->lists('scope_name','id');
	}

	public static function availableTypes($user_id  = null){
		return self::select('activity_types.activity_type','activity_types.id')
			->where(function($query) use ($user_id){
				if(!is_null($user_id)){
					$query->where('activities.created_by', $user_id);
				}
			})
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->groupBy('activity_types.activity_type')
			->orderBy('activity_types.activity_type')
			->get()
			->lists('activity_type','id');
	}

	public static function availablePlanners($user_id  = null){
		return self::select('users.id',DB::raw('CONCAT(first_name, " ", last_name) AS planner'))
			->where(function($query) use ($user_id){
				if(!is_null($user_id)){
					$query->where('activities.created_by', $user_id);
				}
			})
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id')
			->join('users', 'activity_planners.user_id','=','users.id')
			->groupBy('activity_planners.user_id')
			->orderBy('planner')
			->get()
			->lists('planner','id');
	}

}