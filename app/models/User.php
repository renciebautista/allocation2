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
		'group_id' => 'required|integer|min:1'
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
			->orderBy('first_name')
			->get();
	}

	public static function export($status,$type,$search){
		return self::select('users.username', 'users.email', 'users.contact_no','users.first_name','users.last_name','roles.name as groups', 
			DB::raw("IF(users.active = 1, 'YES', 'No') AS active"))
			->join('assigned_roles', 'users.id', '=', 'assigned_roles.user_id')
			->join('roles', 'assigned_roles.role_id', '=', 'roles.id')
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
			->orderBy('first_name')
			->get();
	}

	public function getFullname()
	{
	    return strtoupper($this->attributes['first_name'] .' '.$this->attributes['last_name']);
	}



	public function isActive(){
		return $this->attributes['active'];
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
		->orderBy('first_name')
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
					    if(!empty($row['contact_no'])){
					    	$user->contact_no = $row['contact_no'];
					    }
					    
					    $user->confirmed = 1;
					    // $user->active = $active;
					    $user->update();

					    // echo $user->id;
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
						    if(!empty($row['contact_no'])){
						    	$new_user->contact_no = $row['contact_no'];
						    }
						    $new_user->confirmed = 1;
						    $new_user->active = $active;
						    $new_user->save();

						    $new_user->roles()->attach($role->id); // id only
						}
						
					    
					}
				}
			}
		}
	}


	private static function getUserByEmail($email){
		return self::where('email',$email)->first();
	}

	private static function generateToken()
    {
        return md5(uniqid(mt_rand(), true));
    }

    private static function sendEmail($user, $token){
    	$data['user'] = $user;
    	$data['token'] = $token;
    	Mail::send('emails.password_reset', $data, function($message) use ($user){
			$message->to($user->email, $user->first_name);
			$message->subject('ETOP Password Reset');
		});
    }

	private static function requestChangePassword($user){
		$token = self::generateToken();
        $values = array(
            'email'=> $user->email,
            'token'=> $token,
            'created_at'=> new \DateTime
        );

        DB::table('password_reminders')->insert($values);

        self::sendEmail($user, $token);

        return $token;
	}
	public static function forgot_password($email){
		$user = self::getUserByEmail($email);
        if (($user) && ($user->active)){
            return self::requestChangePassword($user);
        }

        return false;
	}
	
}
