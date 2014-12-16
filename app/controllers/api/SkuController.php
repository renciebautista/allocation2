<?php
namespace Api;
class SkuController extends \BaseController {

	public function category()
	{
		if(\Request::ajax()){

			$data = \Sku::select('category_code', 'category_desc')
			->where('division_code',\Input::get('q'))
			->groupBy('category_code')
			->orderBy('category_desc')->lists('category_desc', 'category_code');

			return \Response::json($data,200);
		}
	}

	public function brand()
	{
		if(\Request::ajax()){
			$filter = \Input::get('categories');
			$data = array();
			if($filter != ''){
				$data = \Sku::select('brand_code', 'brand_desc')
				->whereIn('category_code',$filter)
				->groupBy('brand_code')
				->orderBy('brand_desc')->lists('brand_desc', 'brand_code');
			}

			return \Response::json($data,200);
		}
	}

}