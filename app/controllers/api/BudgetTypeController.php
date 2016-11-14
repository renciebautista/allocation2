<?php
namespace Api;
class BudgetTypeController extends \BaseController {

	public function gettype(){
		if(\Request::ajax()){
			$data = \BudgetType::lists('budget_type', 'id');
			return \Response::json($data,200);
		}
	}

}