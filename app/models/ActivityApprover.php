<?php

class ActivityApprover extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function getCurrentApprover($activity_id){
		return self::where('activity_id',$activity_id)
			->where('user_id',Auth::id())
			->where('status_id',0)
			->first();
	}

	public static function updateNextApprover($activity_id,$role){
		$approvers = self::getApproverByRole($activity_id,$role);
		foreach ($approvers as $approver) {
			$approver = self::find($approver->id);
			$approver->show = 1;
			$approver->update();

			$activity =Activity::find($activity_id);

			$user = User::find($approver->user_id);
			$data['fullname'] = $user->first_name . ' ' . $user->last_name;
			$data['user'] = $user;
			$data['to_user'] = $user->first_name;
			$data['line1'] = "<p><b>".$activity->circular_name."</b> has been submitted for your approval.</p>";
			$data['line2']= "<p>You may view this activity through this link >> <a href=".route('submittedactivity.edit',$activity->id)."> ".route('submittedactivity.edit', $activity->id)."</a></p>";
			$data['subject'] = "CUSTOMIZED ACTIVITY - FOR APPROVAL";
			$data['activity'] = Activity::getDetails($activity->id);
			if($_ENV['MAIL_TEST']){
				Mail::send('emails.customized', $data, function($message) use ($data){
					$message->to("rbautista@chasetech.com", $data['fullname']);
					$message->bcc("Grace.Erum@unilever.com");
					$message->subject($data['subject']);
				});
			}else{
				Mail::send('emails.customized', $data, function($message) use ($data){
					$message->to(trim(strtolower($user->email)), $data['fullname'])->subject($data['subject']);
				});
			}
		}
	}

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

	public static function getApproverByRole($activity_id,$role_name){
		return self::select('activity_approvers.id','activity_approvers.user_id')
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
			// $activity2->pro_recall = 0;
			// $activity2->pmog_recall = 0;
			$activity2->status_id = $status_id;
			$activity2->update();
		}
	}

	public static function resetAll($activity_id){
		self::where('activity_id',$activity_id)->update(array('status_id' => 0,'for_approval' => 0));
	}

	public static function getActivities($user_id){
		$list = array();
		$data = self::where('user_id',$user_id)
		->where('show',1)
		->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->activity_id;
			}
		}
		return $list;
	}

	public static function getActivitiesForApproval($user_id){
		$list = array();
		$data = self::where('user_id',$user_id)
		->where('show',1)
		->where('status_id',0)
		->where('for_approval',1)
		->get();
		if(!empty($data)){
			foreach ($data as $row) {
				$list[] = $row->activity_id;
			}
		}
		return $list;
	}

	public static function getApprover($id,$user_id){
		return self::where('activity_id',$id)
			->where('user_id',$user_id)->first();
	}

	public static function myActivity($activity_id){
		$activities = self::getActivities(Auth::id());
		if(in_array($activity_id, $activities)){
			return true;
		}
    	return false;
    }

    public static function ApproverExist($activity_id,$user_id){
    	$approver = self::where('activity_id',$activity_id)
    	->where('user_id',$user_id)
    	->first();

    	if(count($approver) >0 ){
    		return true;
    	}else{
    		return false;
    	}
    }

    public static function getNames($activity_id){
    	return self::where('activity_id',$activity_id)
    	->join('users','users.id','=','activity_approvers.user_id')
    	->get();
    }

    public static function withActivities($user_id){
		$records = self::where('user_id',$user_id)->count();
		if($records > 0){
			return true;
		}else{
			return false;
		}
	}
}