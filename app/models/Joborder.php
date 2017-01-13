<?php

class Joborder extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
		'task' => 'required|integer|min:1',
		'sub_task' => 'required|integer|min:1',
		'target_date' => 'required',
		'details' => 'required'
	);

	public function activity(){
		return $this->belongsTo('Activity');
	}

	public function assignedto(){
		return $this->belongsTo('User', 'assigned_to');
	}

	public function createdBy(){
		return $this->belongsTo('User', 'created_by');
	}

	public function department(){
		return $this->belongsTo('Department');
	}

	public function status(){
		return $this->belongsTo('JoborderStatus', 'joborder_status_id');
	}

	public function comments(){
		return $this->hasMany('JoborderComment', 'joborder_id');
	}

	public static function getActivityJo($activity){
		return self::where('activity_id', $activity->id)->get();
	}

	public static function departmentJoborder($user, $status, $task, $sub_task, $department, $assignedto){
		return self::where('department_id', $user->department_id)
			->where(function($query) use ($status){
				if($status > 0){
					$query->whereIn('joborder_status_id', $status);
				}
			})
			->where(function($query) use ($task){
				if($task > 0){
					$query->whereIn('task_id', $task);
				}
			})
			->where(function($query) use ($sub_task){
				if($sub_task > 0){
					$query->whereIn('sub_task_id', $sub_task);
				}
			})
			->where(function($query) use ($department){
				if($department > 0){
					$query->whereIn('department_id', $department);
				}
			})
			->where(function($query) use ($assignedto){
				if($assignedto > 0){
					$query->whereIn('assigned_to', $assignedto);
				}
			})
			->get();
	}

	public static function getDeptJoTask($user){
		return self::select('task', 'task_id')
			->where('department_id', $user->department_id)
			->groupBy('task_id')
			->lists('task', 'task_id');
	}

	public static function getDeptJoSubTask($user){
		return self::select('sub_task', 'sub_task_id')
			->where('department_id', $user->department_id)
			->groupBy('sub_task_id')
			->lists('sub_task', 'sub_task_id');
	}

	public static function getJoDept($user){
		return self::select('department', 'department_id')
			->join('departments', 'joborders.department_id', '=', 'departments.id')
			->where('department_id', $user->department_id)
			->groupBy('department_id')
			->lists('department', 'department_id');
	}

	public static function getAssinged($user){
		return self::select(DB::raw('CONCAT(first_name," " , last_name) as full_name'), 'assigned_to')
			->join('users', 'joborders.assigned_to', '=', 'users.id')
			->where('joborders.department_id', $user->department_id)
			->groupBy('assigned_to')
			->lists('full_name', 'assigned_to');
	}

	public static function myJoborder($user, $status, $task, $sub_task){
		return self::where('assigned_to', $user->id)
			->where(function($query) use ($status){
				if($status > 0){
					$query->whereIn('joborder_status_id', $status);
				}
			})
			->where(function($query) use ($task){
				if($task > 0){
					$query->whereIn('task_id', $task);
				}
			})
			->where(function($query) use ($sub_task){
				if($sub_task > 0){
					$query->whereIn('sub_task_id', $sub_task);
				}
			})
			->get();
	}

	public static function getMyJoTask($user){
		return self::select('task', 'task_id')
			->where('assigned_to', $user->id)
			->groupBy('task_id')
			->lists('task', 'task_id');
	}

	public static function getMyJoSubTask($user){
		return self::select('sub_task', 'sub_task_id')
			->where('assigned_to', $user->id)
			->groupBy('sub_task_id')
			->lists('sub_task', 'sub_task_id');
	}
}