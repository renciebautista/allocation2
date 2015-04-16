<?php

class ActivityApprover extends \Eloquent {
	protected $fillable = [];
	// public $timestamps = false;

	public static function getList($id){
		$list = array();
		$data = self::where('activity_id',$id)->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->user_id;
			}
		}
		return $list;
	}

	public static function getApprover($id,$user_id){
		return self::where('activity_id',$id)
			->where('user_id',$user_id)->first();
	}

	public static function getApproverByRole($activity_id,$role_name){
		return self::select('activity_approvers.user_id')
			->join('users', 'activity_approvers.user_id', '=', 'users.id')
			->join('assigned_roles', 'activity_approvers.user_id', '=', 'assigned_roles.user_id')
			->join('roles', 'assigned_roles.role_id', '=', 'roles.id')
			->where('activity_id',$activity_id)
			->where('roles.name',$role_name)
			->where('activity_approvers.status_id',0)
			->get();
	}
	public static function getAllApproverByRole($activity_id,$role_name){
		return self::select('activity_approvers.user_id')
			->join('users', 'activity_approvers.user_id', '=', 'users.id')
			->join('assigned_roles', 'activity_approvers.user_id', '=', 'assigned_roles.user_id')
			->join('roles', 'assigned_roles.role_id', '=', 'roles.id')
			->where('activity_id',$activity_id)
			->where('roles.name',$role_name)
			->get();
	}

	public static function allApproverByRole($activity_id,$role_name){
		$data = array();
		$aprovers = self::select('activity_approvers.user_id')
			->join('users', 'activity_approvers.user_id', '=', 'users.id')
			->join('assigned_roles', 'activity_approvers.user_id', '=', 'assigned_roles.user_id')
			->join('roles', 'assigned_roles.role_id', '=', 'roles.id')
			->where('activity_id',$activity_id)
			->where('roles.name',$role_name)
			->get();

		foreach ($aprovers as $aprover) {
			$data[] = $aprover->user_id;
		}

		return $data;
	}

	public static function updateActivity($activity_id,$role_name,$status_id){
		$approver_ids = self::allApproverByRole($activity_id,$role_name);

		$aprovers = self::where('activity_id',$activity_id)
			->whereIn('user_id',$approver_ids)
			->get();
		$valid = true;

		foreach ($aprovers as $approver) {
			if($approver->status_id == 0){
				$valid = false;
				break;
			}
		}
		// echo $valid;
		if($valid){
			$activity = Activity::find($activity_id);
			$activity->status_id = $status_id;
			$activity->update();
		}

		// //fields approved
		$all_approvers = self::where('activity_id',$activity_id)->get();
		$approved = true;
		foreach ($all_approvers as $approver) {
			if($approver->status_id == 0){
				$approved = false;
				break;
			}
		}

		if($approved){
			$activity2 = Activity::find($activity_id);
			$activity2->status_id = $status_id;
			$activity2->update();
		}
	}

	public static function resetApprover($id,$role_id){
		$approvers = self::select('activity_approvers.id')
			->where('activity_id',$id)
			->where('assigned_roles.role_id','>',$role_id)
			->join('users', 'activity_approvers.user_id', '=', 'users.id')
			->join('assigned_roles', 'activity_approvers.user_id', '=', 'assigned_roles.user_id')
			->join('roles', 'assigned_roles.role_id', '=', 'roles.id')->get();

		// Helper::print_array($approvers);
		foreach ($approvers as $approver) {
			self::where('id',$approver->id)->update(array('status_id' => 0));
		}
	}

	public static function resetAll($activity_id){
		self::where('activity_id',$activity_id)->update(array('status_id' => 0));
	}
}