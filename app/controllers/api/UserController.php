<?php

namespace Api;
use User;
use DB;
use Activity;
use Input;
use Auth;
class UserController extends \BaseController {


	public function getnewmembers()
	{
		if(\Request::ajax()){
			$activity = Activity::find(Input::get('id'));
			if(!empty($activity)){
				if($activity->created_by == Auth::id()){
					if(!$activity->channel_approved){
						$departments = [2];
						$data['user'] = User::getUsers($activity,$departments);
					}else{
						$data['user'] = User::getUsers($activity);
					}
				}else{
					$data['user'] = User::getUsers($activity);
				}
				
				return \Response::json($data,200);

			}else{
				return \Response::json('activity not found',404);
			}
			
		}
	}

}