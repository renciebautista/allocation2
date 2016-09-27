<?php

class UsersController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * GET /users
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$departments = Department::getLists();
		$status = array('1' => 'ACTIVE','2' => 'IN-ACTIVE');
		$groups = Role::getLists();
		$users = User::search(Input::get('department'), Input::get('status'),Input::get('group'),Input::get('search'));
		return View::make('users.index',compact('users', 'status', 'groups', 'departments'));
	}

	/**
	 * Displays the form for account creation
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function create()
	{
		$groups = Role::getLists();
		$departments = Department::getLists();
		return View::make('users.create',compact('groups', 'departments'));
	}

	/**
	 * Stores new account
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, User::$rules);

		if($validation->passes())
		{
			DB::transaction(function()
			{
				$user = new User;
				$user->first_name = strtoupper(Input::get('first_name'));
				$user->middle_initial = strtoupper(Input::get('middle_name'));
				$user->last_name = strtoupper(Input::get('last_name'));
				$user->username = Input::get('username');
				$user->email = Input::get('email');
				$user->contact_no = Input::get('contact_no');
				$user->password = Input::get('password');
				$user->password_confirmation = Input::get('password_confirmation');
				$user->confirmation_code = md5(uniqid(mt_rand(), true));
				$user->confirmed = 1;
				$user->active = (Input::has('is_active')) ? 1 : 0;
				$user->department_id = Input::get('department_id');
				$user->save();

				// dd($user);

				$role = Role::find(Input::get('group_id'));

				$user->roles()->attach($role->id); // id only
			});
			return Redirect::action('UsersController@index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::action('UsersController@create')
			->withInput(Input::except(array('password','password_confirmation')))
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	public function edit($id){
		Session::forget('_old_input');
		
		$user = User::with('roles')->findOrFail($id);
		$groups = Role::getLists();
		$role = $user->roles[0]->id;
		$departments = Department::getLists();

		// dd($user->department->department);
		return View::make('users.edit',compact('groups','user','role','departments'));
	}

	public function show($id){
		return Redirect::route('users.edit',$id);
	}

	public function update($id){
		$input = Input::all();
		$user = User::findOrFail($id);

		$rules = array(
	    	'username' => 'required|unique:users,username,'.$id,
			'email' => 'required|email|unique:users,email,'.$id,
			'first_name' => 'required',
			'last_name' => 'required',
			'group_id' => 'required|integer|min:1',
			'department_id' => 'required|integer|min:1'
		);

		$validation = Validator::make($input, $rules);

		if($validation->passes())
		{
			DB::beginTransaction();
			try {

				$user->first_name = strtoupper(Input::get('first_name'));
				$user->middle_initial = strtoupper(Input::get('middle_name'));
				$user->last_name = strtoupper(Input::get('last_name'));
				$user->username = Input::get('username');
				$user->email = Input::get('email');
				$user->contact_no = Input::get('contact_no');
				$user->active = (Input::has('is_active')) ? 1 : 0;
				$user->department_id = Input::get('department_id');
				$user->update();

				$user->detachRoles($user->roles);

				$role = Role::find(Input::get('group_id'));

				$user->roles()->attach($role->id); // id only

				DB::commit();

				return Redirect::action('UsersController@index')
					->with('class', 'alert-success')
					->with('message', 'Record successfuly updated.');

			} catch (\Exception $e) {
				DB::rollback();
				return Redirect::action('UsersController@edit',$user->id)
				->withInput(Input::except(array('password','password_confirmation')))
				->withErrors($validation)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
			 //    $class = 'alert-danger';
				// $message = 'Cannot duplicate activity.';

				// return Redirect::to(URL::action('ActivityController@index'))
				// ->with('class', $class )
				// ->with('message', $message);
				// something went wrong
			}		
			
		}

		return Redirect::action('UsersController@edit',$user->id)
			->withInput(Input::except(array('password','password_confirmation')))
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	public function destroy($id){
		Session::flash('url',Request::server('HTTP_REFERER'));  

		$user = User::findOrFail($id);

		$errors = array();
		
		if(Activity::withActivities($id)){
			$errors[] = "There is an existing activity created.";
		}

		if(ActivityPlanner::withActivities($id)){
			$errors[] = "There is an existing activity with approval.";
		}

		if(ActivityApprover::withActivities($id)){
			$errors[] = "There is an existing activity with approval.";
		}

		if(count($errors) > 0){
			return Redirect::to(Session::get('url'))
				->withErrors($errors)
				->with('class', 'alert-danger')
				->with('message', 'Unable to delete user.');
		}else{
			$user->delete();
			return Redirect::to(Session::get('url'))
				->with('class', 'alert-success')
				->with('message', $user->last_name.', '.$user->first_name.' user is successfuly deleted.');
		}

	}

	public function exportuser(){
		$users = User::export(Input::get('st'),Input::get('grp'),Input::get('ser'));
		Excel::create("User List", function($excel) use($users ){
			$excel->sheet('users', function($sheet) use($users ) {
				$sheet->fromArray($users);
			})->download('xls');

		});
	}

	public function forapproval(){
		$users = User::forApproval();
		return View::make('users.forapproval',compact('users', 'status', 'groups'));
	}

	public function deny($id){
		// dd(Input::all());
		$user = User::findOrFail($id);
		// dd($user);
		// $user->status = 3;
		// $user->update();

		// dd($user);	

		DeniedUser::create(['first_name' => $user->first_name, 'middle_initial' => $user->middle_initial,
			'last_name' => $user->last_name, 'email' => $user->email, 'username' => $user->username]);

		$data['email'] = $user->email;
	    $data['first_name'] = $user->first_name;

		// send mail for deny
	    Mail::send('emails.signup_deny', $data, function($message) use ($data){
	      	$message->to($data['email'],$data['first_name'])->subject('Account Application Denied');
	    });

	    $user->delete();

	    Session::flash('message', 'User list successfuly updated.');
	   	Session::flash('class', 'alert alert-success');
	    return Redirect::back();
	}

	public function approve($id){
		$user = User::findOrFail($id);
		$groups = Role::getLists();
		return View::make('users.approve',compact('user','groups'));
	}

	public function setapprove($id){

		$password = str_random(6);
		$user = User::findOrFail($id);
		$user->status = 2;
		$user->active = (Input::has('is_active')) ? 1 : 0;

		$user->password = $password;
		$user->password_confirmation = $password;
		$user->confirmation_code = md5(uniqid(mt_rand(), true));
		$user->confirmed = 1;

		$user->update();
		$user->detachRoles($user->roles);
		$role = Role::find(Input::get('group_id'));

		$user->roles()->attach($role->id); // id only

		$data['email'] = $user->email;
	    $data['first_name'] = $user->first_name;
	    $data['username'] = $user->username;
	    $data['password'] = $password;

		// send confirmation email
		Mail::send('emails.approved', $data, function($message) use ($data){
	      	$message->to($data['email'],$data['first_name'])->subject('Account Application Approved');
	    });

		return Redirect::action('UsersController@forapproval')
					->with('class', 'alert-success')
					->with('message', 'User list successfuly updated.');

	}

	public function updateinfo(){
		return View::make('users.updateinfo');
	}

	public function uploadinfo(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				User::updateinfo($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('UsersController@index')
					->with('class', 'alert-success')
					->with('message', 'Users information successfuly updated');
		}else{

			return Redirect::action('UsersController@updateinfo')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}
}
