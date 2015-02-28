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

	public function categoryselected()
	{
		if(\Request::ajax()){
			$q = \Input::get('q');
			$id = \Input::get('id');
			$data['selection'] = \Sku::select('category_code', 'category_desc')
			->where('division_code',$q)
			->groupBy('category_code')
			->orderBy('category_desc')->lists('category_desc', 'category_code');

			$data['selected'] = \ActivityCategory::selected_category($id);


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

	public function brandselected()
	{
		if(\Request::ajax()){
			$filter = \Input::get('categories');
			$id = \Input::get('id');
			$data = array();
			$data['selection']= array();
			if($filter != ''){
				$data['selection'] = \Sku::select('brand_code', 'brand_desc')
				->whereIn('category_code',$filter)
				->groupBy('brand_code')
				->orderBy('brand_desc')->lists('brand_desc', 'brand_code');
			}

			$data['selected'] = \ActivityBrand::selected_brand($id);

			return \Response::json($data,200);
		}
	}

}