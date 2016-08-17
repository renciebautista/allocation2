<?php

class ActivityMember extends \Eloquent {
	protected $fillable = [];
    
	public static function myActivity($activity_id){
    	return self::where('activity_id', $activity_id)
    		->where('user_id',Auth::id())
    		->first();
    }


    public static function getByDepartmentId($departments){
        return self::join('users', 'users.id', '=', 'activity_members.user_id')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->whereIn('users.department_id',$departments)
            ->get();
    }

    public static function allowToSubmit($activity){
        $settings = Setting::find(1);
        $approvers = explode(",", $settings->customized_preapprover);

        $members = self::where('activity_id', $activity->id)
            ->join('users', 'users.id', '=', 'activity_members.user_id')
            ->whereIn('users.department_id',$approvers)
            ->get();
        if(count($members) > 0){
            $allow = false;
            $cnt = 0;
            foreach ($members as $member) {
                if($member->activity_member_status_id == 3){
                    $cnt++;
                }
            }

            if($cnt == count($members)){
                return true;
            }
        }else{
            return false;
        }
        
    }
}