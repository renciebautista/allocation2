<?php

namespace Api;
use User;
use DB;

class UserController extends \BaseController {


	public function getnewmembers()
	{
		if(\Request::ajax()){
			$data['user'] = User::getUsers();
			return \Response::json($data,200);
		}
	}

}