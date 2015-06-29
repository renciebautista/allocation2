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

	public static function search($status,$type,$search){
		return self::select('users.id', 'users.first_name', 'users.last_name','users.email','users.active')
			->join('assigned_roles', 'users.id', '=', 'assigned_roles.user_id')
			->where(function($query) use ($search){
				$query->where('first_name', 'LIKE' ,"%$search%")
					->orwhere('last_name', 'LIKE' ,"%$search%")
					->orwhere('middle_initial', 'LIKE' ,"%$search%");
			})
			->where(function($query) use ($status){
				if($status == 1){
					$query->where('active', 1);
				}
				if($status == 2){
					$query->where('active', 0);
				}
				
					
			})
			->where(function($query) use ($type){
				if($type > 0){
					$query->where('role_id', $type);
				}
					
			})
			->get();
	}

	public function getFullname()
	{
	    return $this->attributes['first_name'] .' '.$this->attributes['last_name'];
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
			->orderBy('roles.id')
			->orderBy('users.first_name')
			->get();
		$data = array();
		foreach ($users as $user) {
			$data[$user->id] = $user->first_name .' '.$user->middle_initial.' '.$user->last_name .' ('. $user->name.')';
		}

		return $data;
	}

	public static function GetPlanners($roles){
		return self::join('assigned_roles', 'users.id', '=', 'assigned_roles.user_id')
		->join('roles', 'assigned_roles.role_id', '=', 'roles.id')
		->where('users.active',1)
		->whereIn('roles.name',$roles)
		->get();
	}

	public static function batchInsert($records){
		if(!empty($records)){
			foreach ($records as $row) {
				
				if(!is_null($row['user_name'])){
					$active = 0;
					if($row['active'] == "Y"){
						 $active = 1;
					}
					$user = User::where(['username' => str_replace(" ", "",$row['user_name'])])->first();
					$role = Role::where('name',$row['groups'])->first();
					if(!empty($user)){
					    $user->username = str_replace(" ", "",$row['user_name']);
					    $user->first_name = strtoupper($row['first_name']);
					    $user->last_name = strtoupper($row['last_name']);
					    $user->email = $row['email_address'];
					    $user->confirmed = 1;
					    $user->active = $active;
					    $user->update();

					    // // echo $user->id;
						$for_delete = DB::table('assigned_roles')
						->where('user_id', $user->id)
						->get();
						if(count($for_delete) > 0){
							DB::table('assigned_roles')
							->where('user_id', $user->id)
							->delete();
						}

						$user->roles()->attach($role->id); // id only

					}else{
						if($row['email_address'] != ""){
							$new_user = new User;
						    $new_user->username = str_replace(" ", "",$row['user_name']);
						    $new_user->first_name = strtoupper($row['first_name']);
						    $new_user->last_name = strtoupper($row['last_name']);
						    $new_user->email = str_replace(" ", "",$row['email_address']);
						    $new_user->password = 'password';
						    $new_user->password_confirmation = 'password';
						    $new_user->confirmation_code = md5(uniqid(mt_rand(), true));
						    $new_user->confirmed = 1;
						    $new_user->active = $active;
						    $new_user->save();

						    $new_user->roles()->attach($role->id); // id only
						}
						
					    
					}
				}
			}
		}
		// $records->each(function($row) {
		// 	if(!is_null($row->user_name)){
		// 		$active = 0;
		// 		if($row->active == "Y"){
		// 			 $active = 1;
		// 		}

		// 		$user = User::where(['username' => $row->user_name])->first();
		// 		$role = Role::where('name',$row->groups)->first();
		// 		if(!empty($user)){
		// 		    $user->username = $row->user_name;
		// 		    $user->first_name = strtoupper($row->first_name);
		// 		    $user->last_name = strtoupper($row->last_name);
		// 		    $user->email = $row->email_address;
		// 		    $user->password = 'password';
		// 		    $user->password_confirmation = 'password';
		// 		    $user->confirmation_code = md5(uniqid(mt_rand(), true));
		// 		    $user->confirmed = 1;
		// 		    $user->active = $active;
		// 		    $user->update();

		// 		    // // echo $user->id;
		// 			$for_delete = DB::table('assigned_roles')
		// 			->where('user_id', $user->id)
		// 			->get();
		// 			if(count($for_delete) > 0){
		// 				DB::table('assigned_roles')
		// 				->where('user_id', $user->id)
		// 				->delete();
		// 			}

		// 			$user->roles()->attach($role->id); // id only

		// 		}else{
		// 			$new_user = new User;
		// 		    $new_user->username = $row->user_name;
		// 		    $new_user->first_name = strtoupper($row->first_name);
		// 		    $new_user->last_name = strtoupper($row->last_name);
		// 		    $new_user->email = $row->email_address;
		// 		    $new_user->password = 'password';
		// 		    $new_user->password_confirmation = 'password';
		// 		    $new_user->confirmation_code = md5(uniqid(mt_rand(), true));
		// 		    $new_user->confirmed = 1;
		// 		    $new_user->active = $active;
		// 		    $new_user->save();

		// 		    $new_user->roles()->attach($role->id); // id only
				    
		// 		}
				
				

				
				

				
		// 	}
			
		// });
	}
}
