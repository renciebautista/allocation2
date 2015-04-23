<?php

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	protected $fillable = ['name'];
	public static $rules = array(
        'name' => 'required|between:4,128|unique:roles'
    );

    public static function search($filter){
		return self::where('name', 'LIKE' ,"%$filter%")
			->orderBy('name')
			->get();
	}

	public static function getLists(){
		return self::orderBy('name')->lists('name', 'id');
	}

	public static function withUsers($id){
		$users = DB::table('assigned_roles')
			->where('role_id',$id)
			->get();
		if(count($users) > 0){
			return true;
		}
		return false;
	}

}