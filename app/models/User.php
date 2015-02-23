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

}
