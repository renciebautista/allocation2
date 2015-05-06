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
		'activity_title' => 'required|max:80',
		
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
			$required[] = 'Activity approver is required.';
			$return['status'] = 0;
		}

		if($activity->cycle_id == 0){
			$required[] = 'Activity cycle is required.';
			$return['status'] = 0;
		}

		if($activity->division_code == 0){
			$required[] = 'Activity division is required.';
			$return['status'] = 0;
		}

		$category = ActivityCategory::selected_category($activity->id);
		if(count($category)  == 0){
			$required[] = 'Activity category is required.';
			$return['status'] = 0;
		}

		$brand = ActivityBrand::selected_brand($activity->id);
		if(count($brand) == 0){
			$required[] = 'Activity brand is required.';
			$return['status'] = 0;
		}

		// $skus = ActivitySku::getList($activity->id);
		// if(count($skus) == 0){
		// 	$required[] = 'Activity skus involved is required.';
		// 	$return['status'] = 0;
		// }

		$objective = ActivityObjective::getList($activity->id);
		if(count($objective) == 0){
			$required[] = 'Activity objective is required.';
			$return['status'] = 0;
		}

		if($activity->background ==''){
			$required[] = 'Activity background is required.';
			$return['status'] = 0;
		}

		$customer = ActivityCustomer::customers($activity->id);
		if(count($customer) == 0){
			$required[] = 'Activity customer is required.';
			$return['status'] = 0;
		}

		$scheme = Scheme::getList($activity->id);
		if(count($scheme) == 0){
			$required[] = 'Activity scheme is required.';
			$return['status'] = 0;
		}

		$return['message'] = $required;
		return $return;;
		
	}

	public static function search($user_id = 0,$status,$cycle,$scope,$type,$pmog,$title){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'),
			'activities.status_id')
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id', 'left')
			->join('users', 'activity_planners.user_id','=','users.id', 'left')	
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
					$query->whereIn('activities.status_id', $status);
				}
			})
			->where(function($query) use ($cycle){
				if($cycle > 0){
					$query->whereIn('activities.cycle_id', $cycle);
				}
			})
			->where(function($query) use ($scope){
				if($scope > 0){
					$query->whereIn('activities.scope_type_id', $scope);
				}
			})
			->where(function($query) use ($type){
				if($type > 0){
					$query->whereIn('activities.activity_type_id', $type);
				}
			})
			->where(function($query) use ($pmog){
				if($pmog > 0){
					$query->whereIn('activity_planners.user_id', $pmog);
				}
			})
			->orderBy('activities.created_at', 'desc')
			->get();
	}

	public static function searchDownloaded($user_id,$proponent_id,$status,$cycle,$scope,$type,$title){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id')
			->join('users', 'activity_planners.user_id','=','users.id')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activity_planners.user_id',$user_id)
			->where('activities.status_id','>',1)
			->where(function($query) use ($proponent_id){
				if($proponent_id > 0){
					$query->whereIn('activities.created_by', $proponent_id);
				}
			})
			->where(function($query) use ($title){
				$query->where('activities.circular_name', 'LIKE' ,"%$title%");
			})
			->where(function($query) use ($status){
				if($status > 0){
					$query->whereIn('activities.status_id', $status);
				}
			})
			->where(function($query) use ($cycle){
				if($cycle > 0){
					$query->whereIn('activities.cycle_id', $cycle);
				}
			})
			->where(function($query) use ($scope){
				if($scope > 0){
					$query->whereIn('activities.scope_type_id', $scope);
				}
			})
			->where(function($query) use ($type){
				if($type > 0){
					$query->whereIn('activities.activity_type_id', $type);
				}
			})
			->orderBy('activities.created_at', 'desc')
			->get();
	}

	public static function searchSubmitted($proponent_id,$status,$cycle,$scope,$type,$title){
		$activities = ActivityApprover::getActivities(Auth::id());
		
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.status_id','>',1)
			->whereIn('activities.id',$activities)
			->where(function($query) use ($proponent_id){
				if($proponent_id > 0){
					$query->whereIn('activities.created_by', $proponent_id);
				}
			})
			->where(function($query) use ($title){
				$query->where('activities.circular_name', 'LIKE' ,"%$title%");
			})
			->where(function($query) use ($status){
				if($status > 0){
					$query->whereIn('activities.status_id', $status);
				}
			})
			->where(function($query) use ($cycle){
				if($cycle > 0){
					$query->whereIn('activities.cycle_id', $cycle);
				}
			})
			->where(function($query) use ($scope){
				if($scope > 0){
					$query->whereIn('activities.scope_type_id', $scope);
				}
			})
			->where(function($query) use ($type){
				if($type > 0){
					$query->whereIn('activities.activity_type_id', $type);
				}
			})
			->orderBy('activities.created_at', 'desc')
			->get();
	}

	public static function searchField($cycle,$type,$title){
		return self::select('activities.id','activities.circular_name','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->where('activities.status_id',8)
			->where(function($query) use ($title){
				$query->where('activities.circular_name', 'LIKE' ,"%$title%");
			})
			->where(function($query) use ($cycle){
				if($cycle > 0){
					$query->whereIn('activities.cycle_id', $cycle);
				}
			})
			->where(function($query) use ($type){
				if($type > 0){
					$query->whereIn('activities.activity_type_id', $type);
				}
			})
			->orderBy('activities.created_at', 'desc')
			->get();
	}

	public static function summary($status,$type){
		return self::select('activities.id','activities.circular_name','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->where('activities.status_id',$status)
			->where(function($query) use ($type){
				if($type == 'ongoing'){
					$query->where('cycles.release_date','>=',Carbon::now()->startOfMonth())
						->where('cycles.release_date','<',Carbon::now()->startOfMonth()->addMonths(1));
				}
				if($type == 'nextmonth'){
					$query->where('cycles.release_date','>=',Carbon::now()->startOfMonth()->addMonths(1))
						->where('cycles.release_date','<',Carbon::now()->startOfMonth()->addMonths(2));
				}
				if($type == 'lastmonth'){
					$query->where('cycles.release_date','>=',Carbon::now()->startOfMonth()->subMonths(1))
						->where('cycles.release_date','<',Carbon::now()->startOfMonth());
					
				}
				
			})
			->orderBy('activities.created_at', 'desc')
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

	public static function availableTypes($user_id = null){
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

	public static function myActivity($activity){
		return ($activity->created_by == Auth::id()) ? true : false;
	}


	

}