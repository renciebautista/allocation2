<?php

class ActivityMember extends \Eloquent {
	protected $fillable = [];
    
	public static function myActivity($activity_id){
    	return self::where('activity_id', $activity_id)
    		->where('user_id',Auth::user()->id)
    		->first();
    }

    public static function myApproval($activity_id){
        return self::where('activity_id', $activity_id)
            ->where('user_id',Auth::user()->id)
            ->where('pre_approve', 1)
            ->where('activity_member_status_id', 1)
            ->first();
    }


    public static function getByDepartmentId($departments){
        return self::join('users', 'users.id', '=', 'activity_members.user_id')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->whereIn('users.department_id',$departments)
            ->get();
    }



    public static function allowToSubmit($activity){
        $members = self::where('activity_id', $activity->id)
            ->where('pre_approve',1)
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

    public static function getActivities($user_id){
        $list = array();
        $data = self::where('user_id',$user_id)
        ->where('activity_member_status_id',1)
        ->get();
        if(!empty($data)){
            foreach ($data as $row) {
                $list[] = $row->activity_id;
            }
        }
        return $list;
    }

    public static function memberList($activity){
        return self::select(array('activity_member_statuses.id as status_id','user_desc', 'department', 'activity_member_statuses.mem_status',
         'activity_members.activity_member_status_id', 'activity_members.id', 'activity_members.pre_approve'))
            ->join('activity_member_statuses', 'activity_member_statuses.id', '=', 'activity_members.activity_member_status_id')
            ->where('activity_id', $activity->id)
            ->get();
    }
}