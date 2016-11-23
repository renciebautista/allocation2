<?php

class Activity extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		// 'scope' => 'required|integer|min:1',
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

    public function timelines()
    {
        return $this->hasMany('ActivityTimeline');
    }

   	public function pmog()
    {
        return $this->belongsToMany('User', 'activity_planners', 'activity_id', 'user_id');
    }


    // static function
	public static function validForDownload($activity,$required_array){
		$return = array();
		$required = array();

		if(in_array("budget", $required_array)){
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
		}
		

		if(in_array("approver", $required_array)){
			$approver = ActivityApprover::getList($activity->id);
			if(count($approver)  == 0){
				$required[] = 'Activity approver is required.';
				$return['status'] = 0;
			}
		}

		if(in_array("cycle", $required_array)){
			if($activity->cycle_id == 0){
				$required[] = 'Activity cycle is required.';
				$return['status'] = 0;
			}
		}

		if(in_array("division", $required_array)){
			$division = ActivityDivision::getList($activity->id);
			if(count($division)  == 0){
				$required[] = 'Activity division is required.';
				$return['status'] = 0;
			}
		}

		if(in_array("category", $required_array)){
			$category = ActivityCategory::selected_category($activity->id);
			if(count($category)  == 0){
				$required[] = 'Activity category is required.';
				$return['status'] = 0;
			}
		}

		if(in_array("brand", $required_array)){
			$brand = ActivityBrand::selected_brand($activity->id);
			if(count($brand) == 0){
				$required[] = 'Activity brand is required.';
				$return['status'] = 0;
			}
		}

		
		if(in_array("objective", $required_array)){
			$objective = ActivityObjective::getList($activity->id);
			if(count($objective) == 0){
				$required[] = 'Activity objective is required.';
				$return['status'] = 0;
			}
		}
		
		if(in_array("background", $required_array)){
			if($activity->background ==''){
				$required[] = 'Activity background is required.';
				$return['status'] = 0;
			}
		}
		
		if(in_array("customer", $required_array)){
			$customer = ActivityCustomer::customers($activity->id);
			if(count($customer) == 0){
				$required[] = 'Activity customer is required.';
				$return['status'] = 0;
			}
		}
		
		if(in_array("scheme", $required_array)){
			$scheme = Scheme::getList($activity->id);
			if(count($scheme) == 0){
				$required[] = 'Activity scheme is required.';
				$return['status'] = 0;
			}
		}

		if(in_array("sob", $required_array)){
			$_required = false;
			$_skip_sob = false;
			$scheme = Scheme::getList($activity->id);
			foreach ($scheme as $row) {
				if($row->compute == 2){
					$_skip_sob = true;
					break;
				}
				
			}


			if(!$_skip_sob){
				foreach ($scheme as $row) {
					$sob = AllocationSob::where('scheme_id', $row->id)->get();
					if(count($sob) < 1){
						$_required = true;
					}
				}

				if($_required){
					$required[] = 'SOB Allocation is required.';
					$return['status'] = 0;
				}
			}

			
		}
		
		if(in_array("fdapermit", $required_array)){
			$fdaPermit = ActivityFdapermit::getList($activity->id);
			if(count($fdaPermit) == 0){
				$required[] = 'FDA Permit No. is required.';
				$return['status'] = 0;
			}
		}
		
		if(in_array("artwork", $required_array)){
			$artworks = ActivityArtwork::getList($activity->id);
			if(count($artworks) == 0){
				$required[] = 'Artwork Packshots is required.';
				$return['status'] = 0;
			}
		}

		if(in_array("submission_deadline", $required_array)){
			$_skip_sob = false;
			$scheme = Scheme::getList($activity->id);
			foreach ($scheme as $row) {
				if($row->compute == 2){
					$_skip_sob = true;
					break;
				}
				
			}

			if(!$_skip_sob){
				if(strtotime($activity->cycle->submission_deadline) < strtotime(date('Y-m-d'))){
					$required[] = 'Cycle Submission Deadline is already expired.';
					$return['status'] = 0;
				}
			}
			
		}

		if(in_array("material_source", $required_array)){
			$source = ActivityMaterial::getList($activity->id);
			if(count($source) == 0){
				$required[] = 'Material Sourcing is required.';
				$return['status'] = 0;
			}
		}

		if(in_array("sob_start_week", $required_array)){
			$_failed = false;
			$scheme = Scheme::getList($activity->id);
			$schemes = [];

			$_skip_sob = false;
			$scheme = Scheme::getList($activity->id);
			foreach ($scheme as $row) {
				if($row->compute == 2){
					$_skip_sob = true;
					break;
				}
				
			}
			if(!$_skip_sob){
				foreach ($scheme as $row) {
					if(strtotime($row['sob_start_date']) < strtotime(date('Y-m-d'))){
						$schemes[] = $row->name;
						$_failed = true;
					}
				}

				if($_failed){
					$required[] = implode(',', $schemes).' SOB start date is less than today.';
					$return['status'] = 0;
				}
			}
			
		}
		


		$return['message'] = $required;
		return $return;;
		
	}

	public static function search($user_id = 0,$status,$cycle,$scope,$type,$pmog,$title){
		$national = self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','pdf','word',
			'scope_types.scope_name','activity_types.activity_type','created_by',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'),
			'activities.status_id','activities.disable')
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
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();

		


		$customized = self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type','created_by',
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_members', 'activities.id','=','activity_members.activity_id')
			->join('users', 'activity_members.user_id','=','users.id')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activity_members.user_id',$user_id)
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
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();

		$newColection = $customized->reject(function($element) {
		    return !ActivityMember::allowToSubmit($element);
		});

		return $national->merge($newColection);
	}

	public static function search2($user_id = 0,$status,$cycle,$scope,$type,$pmog,$title){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','pdf','word',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'),
			'activities.status_id','activities.disable')
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id', 'left')
			->join('users', 'activity_planners.user_id','=','users.id', 'left')	
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where(function($query) use ($user_id){
				if(count($user_id) > 0){
					$query->whereIn('activities.created_by', $user_id);
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
			->where('activities.disable', 0)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function searchDownloaded($user_id,$proponent_id,$status,$cycle,$scope,$type,$title){
		$national = self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
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
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();

		$customized = self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_members', 'activities.id','=','activity_members.activity_id')
			->join('users', 'activity_members.user_id','=','users.id')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activity_members.user_id',$user_id)
			->where('activity_members.activity_member_status_id', '>', 1)
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
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();

		return $national->merge($customized);
	}

	public static function PmogForApproval($user_id){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
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
			->where('activities.status_id',4)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function searchSubmitted($proponent_id,$status,$cycle,$scope,$type,$title){
		$activities = ActivityApprover::getActivities(Auth::id());
		
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','activities.end_date',
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
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function searchField($cycle,$type,$scope,$title){
		return self::select('activities.id','activities.circular_name','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type','activities.eimplementation_date','activities.end_date')
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
			->where(function($query) use ($scope){
				if($scope > 0){
					$query->whereIn('activities.scope_type_id', $scope);
				}
			})
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function summary($status,$type, $filters){
		$division = $filters['division'];
		// dd($division);
		return self::select('activities.id','activities.circular_name','cycles.cycle_name',
			'activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))	
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id', 'left')
			->join('users', 'activity_planners.user_id','=','users.id', 'left')	
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.status_id',$status)
			->where(function($query) use ($type){
				if($type == 'ongoing'){
					$query->where('activities.eimplementation_date','>=',Carbon::now()->startOfMonth())
						->where('activities.eimplementation_date','<',Carbon::now()->startOfMonth()->addMonths(1));
				}
				if($type == 'nextmonth'){
					$query->where('activities.eimplementation_date','>=',Carbon::now()->startOfMonth()->addMonths(1))
						->where('activities.eimplementation_date','<',Carbon::now()->startOfMonth()->addMonths(2));
				}
				if($type == 'lastmonth'){
					$query->where('activities.eimplementation_date','>=',Carbon::now()->startOfMonth()->subMonths(1))
						->where('activities.eimplementation_date','<',Carbon::now()->startOfMonth());
				}
				
			})
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
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
			->orderBy('activity_types.id')
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

	public static function ApproverForApproval($user_id,$status_id){
		
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type','activities.status_id',
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.status_id',$status_id)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}
	
	public static function ProponentActivitiesForApproval($user_id,$cycles){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','activities.end_date',
			'scope_types.scope_name','activity_types.activity_type','activities.status_id',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.created_by',$user_id)
			->where('activities.status_id', '<',9)
			->whereIn('activities.cycle_id',$cycles)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function PmogActivitiesForApproval($user_id,$cycles){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','activities.end_date',
			'scope_types.scope_name','activity_types.activity_type', 'activities.status_id',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activity_planners.user_id',$user_id)
			// ->where('activities.status_id', '>',3)
			->where('activities.status_id', '<',9)
			->whereIn('activities.cycle_id',$cycles)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function ApproverActivitiesForApproval($user_id,$cycles){
		$activities = ActivityApprover::getActivitiesForApproval($user_id);
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','activities.end_date',
			'scope_types.scope_name','activity_types.activity_type', 'activities.status_id',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.status_id', '>',3)
			->where('activities.status_id', '<',9)
			->whereIn('activities.cycle_id',$cycles)
			->whereIn('activities.id',$activities)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function ApproverActivities($user_id,$cycles){
		$activities = ActivityApprover::getActivities($user_id);
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','activities.end_date',
			'scope_types.scope_name','activity_types.activity_type',  'activities.status_id',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.status_id', '>',3)
			->where('activities.status_id', '<',9)
			->whereIn('activities.cycle_id',$cycles)
			->whereIn('activities.id',$activities)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function Released($cycles){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','activities.end_date',
			'scope_types.scope_name','activity_types.activity_type',  'activities.status_id',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.status_id', 9)
			->whereIn('activities.cycle_id',$cycles)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function ReleasedCyles($cycles){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name','activities.end_date',
			'scope_types.scope_name','activity_types.activity_type',  'activities.status_id',
			'cycles.id',
			DB::raw('CONCAT(users.first_name, " ", users.last_name) AS planner'),
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_planners', 'activities.id','=','activity_planners.activity_id','left')
			->join('users', 'activity_planners.user_id','=','users.id','left')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activities.status_id', 9)
			->whereIn('activities.cycle_id',$cycles)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->groupBy('cycles.cycle_name')
			->get();
	}

	public static function forRelease($cycles){
		return self::where('activities.status_id', 8)
			->where('activities.pdf', 1)
			->whereIn('activities.cycle_id',$cycles)
			->get();
	}


	public static function withActivities($user_id){
		$records = self::where('created_by',$user_id)->count();
		if($records > 0){
			return true;
		}else{
			return false;
		}
	}


	public static function getScopes(){
		return self::select('scope_desc','scope_type_id')
				->groupBy('scope_type_id')
				->lists('scope_desc', 'scope_type_id');
	}

	public static function getProponents(){
		return self::select('proponent_name','created_by')
				->groupBy('created_by')
				->lists('proponent_name', 'created_by');
	}

	public static function getPlanners(){
		return self::select('planner_desc','user_id')
				->join('activity_planners', 'activities.id', '=', 'activity_planners.activity_id')
				->groupBy('user_id')
				->orderBy('planner_desc')
				->lists('planner_desc', 'user_id');
	}

	public static function getApprovers(){
		return self::select('approver_desc','user_id')
				->join('activity_approvers', 'activities.id', '=', 'activity_approvers.activity_id')
				->groupBy('user_id')
				->orderBy('approver_desc')
				->lists('approver_desc', 'user_id');
	}

	public static function getActivityType(){
		return self::select('activitytype_desc','activity_type_id')
				->groupBy('activity_type_id')
				->lists('activitytype_desc', 'activity_type_id');
	}

	public static function getDivision(){
		return self::select('division_desc','activity_divisions.division_code')
				->join('activity_divisions', 'activities.id', '=', 'activity_divisions.activity_id')
				->groupBy('activity_divisions.division_code')
				->orderBy('division_desc')
				->lists('division_desc', 'division_code');
	}

	public static function getCategory(){
		return self::select('category_desc','activity_categories.category_code')
				->join('activity_categories', 'activities.id', '=', 'activity_categories.activity_id')
				->groupBy('activity_categories.category_code')
				->orderBy('category_desc')
				->lists('category_desc', 'category_code');
	}

	public static function getBrand(){
		return self::select('b_desc')
				->join('activity_brands', 'activities.id', '=', 'activity_brands.activity_id')
				->groupBy('activity_brands.b_desc')
				->orderBy('b_desc')
				->lists('b_desc', 'b_desc');
	}

	public static function cycleUsed($id){
		$count =  self::where('cycle_id',$id)->count();
		if($count>0){
			return true;
		}
		return false;
	}

	public static function getNextForApproval($status){
		$activities = ActivityApprover::getActivities(Auth::id());

		return self::select('activities.id as id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->where('activities.status_id','>',1)
			->where(function($query) use ($status){
				if($status > 0){
					$query->whereIn('activities.status_id', $status);
				}
			})
			->whereIn('activities.id',$activities)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->first();
	}


	public static function getIdList($status){
		$activities = ActivityApprover::getActivities(Auth::id());

		$data = array();
		$records = self::select('activities.id as id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->where('activities.status_id','>',1)
			->where(function($query) use ($status){
				if($status > 0){
					$query->whereIn('activities.status_id', $status);
				}
			})
			->whereIn('activities.id',$activities)
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
		if(!empty($records)){
			foreach ($records as $row) {
				$data[] = $row->id;
			}
		}

		return $data;
	}

	public static function checkIfTypeExist($id){
		$record = self::where('activity_type_id',$id)->get();
		if($record->count()> 0){
			return true;
		}else{
			return false;
		}
	}

	public static function getCustomProponent($user_id){
		return self::select('proponent_name','created_by')
				->join('activity_members', 'activities.id', '=', 'activity_members.activity_id')
				->where('scope_type_id', 2)
				->where('activity_members.activity_member_status_id', 1)
				->where('activity_members.user_id',$user_id)
				->groupBy('created_by')
				->orderBy('proponent_name')
				->lists('proponent_name', 'created_by');
	}

	public static function searchCustomForApproval($user_id, $proponent_id, $title, $cycle, $type){
		return self::select('activities.id','activities.circular_name','activities.edownload_date',
			'activities.eimplementation_date','activities.end_date','activities.billing_date',
			'activity_statuses.status','cycles.cycle_name',
			'scope_types.scope_name','activity_types.activity_type',
			DB::raw('CONCAT(propo.first_name, " ", propo.last_name) AS proponent'))
			->join('activity_statuses', 'activities.status_id','=','activity_statuses.id')
			->join('cycles', 'activities.cycle_id','=','cycles.id')
			->join('scope_types', 'activities.scope_type_id','=','scope_types.id')
			->join('activity_types', 'activities.activity_type_id','=','activity_types.id')
			->join('activity_members', 'activities.id','=','activity_members.activity_id')
			->join('users', 'activity_members.user_id','=','users.id')
			->join('users as propo', 'activities.created_by','=','propo.id')
			->where('activity_members.user_id',$user_id)
			->where('scope_type_id', 2)
			->where('activity_members.activity_member_status_id', 1)
			->where(function($query) use ($proponent_id){
				if($proponent_id > 0){
					$query->whereIn('activities.created_by', $proponent_id);
				}
			})
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
			->orderBy('activity_types.activity_type')
			->orderBy('activities.circular_name')
			->orderBy('activities.id')
			->get();
	}

	public static function getCustomIdList(){
		return ActivityMember::getActivities(Auth::user()->id);
	}
}