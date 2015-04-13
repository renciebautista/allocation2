<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;
 
class User extends Eloquent implements ConfideUserInterface {
    use HasRole;
    use ConfideUser;

    public static $rules = array(
    	'username' => 'required|unique:users',
    	'password' => 'required|min:6|confirmed',
		'password_confirmation' => 'same:password',
		'email' => 'required|email|unique:users',
		'first_name' => 'required',
		'last_name' => 'required',
		'group' => 'required|integer|min:1'
	);

	public static function search($status,$filter){
		return self::where(function($query) use ($status){
				if($status ==  1){
					$query->where('active',1);
				}elseif($status ==  2){
					$query->where('active',0);
				}else{

				}
			})
			->where(function($query) use ($filter){
				$query->where('first_name', 'LIKE' ,"%$filter%")
					->orwhere('last_name', 'LIKE' ,"%$filter%")
					->orwhere('middle_initial', 'LIKE' ,"%$filter%")
					->orwhere('email', 'LIKE' ,"%$filter%");
			})
			->get();
	}

	public function getFullname()
	{
	    return $this->attributes['first_name'] .' '.$this->attributes['last_name'];
	}


	public function roles()
	{
		return $this->belongsToMany('Role','assigned_roles');
	}

	public function scopeIsRole($query, $role) {
	    return $query->whereHas(
	        'roles', function($query) use ($role){
	            $query->where('name', $role);
	        }
	    );
	}

	public function inRoles($roles){
		foreach ($this->roles as $role) {
            if (in_array($role->name, $roles)) {
                return true;
            }
        }

        return false;
	}

	public static function getApprovers($roles){
		$users = self::select('users.id','users.first_name', 'users.middle_initial', 'users.last_name', 'roles.name')
			->join('assigned_roles', 'users.id', '=', 'assigned_roles.user_id')
			->join('roles', 'assigned_roles.role_id', '=', 'roles.id')
			->where('users.active',1)
			->whereIn('roles.name',$roles)
			->orderBy('first_name')
			->get();
		$data = array();
		foreach ($users as $user) {
			$data[$user->id] = $user->first_name .' '.$user->middle_initial.' '.$user->last_name .' ('. $user->name.')';
		}

		return $data;
	}
}
