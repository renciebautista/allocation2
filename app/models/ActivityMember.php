<?php

class ActivityMember extends \Eloquent {
	protected $fillable = [];
	public static function myActivity($activity_id){
    	$activity = self::where('activity_id', $activity_id)
    		->where('user_id',Auth::id())
    		->first();
    	if(!empty($activity)){
    		return true;
    	}

    	return false;
    }

    public static function getByDepartmentId($departments){
        return self::join('users', 'users.id', '=', 'activity_members.user_id')
            ->join('departments', 'departments.id', '=', 'users.department_id')
            ->whereIn('users.department_id',$departments)
            ->get();
    }
}