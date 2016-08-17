<?php

namespace Api;
use User;
use DB;
use Activity;
use Input;
use Auth;
use Setting;
class UserController extends \BaseController {


	public function getnewmembers()
	{
		if(\Request::ajax()){
			$activity = Activity::find(Input::get('id'));
			if(!empty($activity)){
				if($activity->created_by == Auth::id()){
					$settings = Setting::find(1);
					$approvers = explode(",", $settings->customized_preapprover);
					if(!$activity->channel_approved){
						$departments = $approvers;
					}else{
						$departments = [];
					}
					$data['user'] = User::getUsers($activity,$departments);
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