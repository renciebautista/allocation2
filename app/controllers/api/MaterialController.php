<?php
namespace Api;
class MaterialController extends \BaseController {

	public function getsource(){
		if(\Request::ajax()){

			$data = \MaterialSource::lists('source', 'id');

			return \Response::json($data,200);
		}
	}

}