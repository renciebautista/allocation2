<?php
namespace Api;
class PriceListController extends \BaseController {

	public function involve()
	{
		if(\Request::ajax()){
			$filter = \Input::get('brand');
			$data = \Pricelist::involves($filter);
			return \Response::json($data,200);
		}
	}

	public function skuselected()
	{
		if(\Request::ajax()){
			$filter = \Input::get('brand');
			$id = \Input::get('id');
			$activity = \Activity::find($id);
			$data = array();
			$data['selection']= array();
			if($filter != ''){
				$data['selection'] = \Pricelist::involves($filter,$activity);
			}

			$data['selected'] = \ActivitySku::getSkus($id);

			return \Response::json($data,200);
		}
	}

}