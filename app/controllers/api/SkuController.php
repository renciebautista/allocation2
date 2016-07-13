<?php
namespace Api;
class SkuController extends \BaseController {

	public function category()
	{
		if(\Request::ajax()){
			$filter = \Input::get('divisions');
			$data = \Pricelist::select('category_code', 'category_desc')
			->whereIn('division_code',$filter)
			->groupBy('category_code')
			->orderBy('category_desc')->lists('category_desc', 'category_code');
			return \Response::json($data,200);
		}
	}

	public function sobcategory()
	{
		if(\Request::ajax()){
			$ids = \Input::get('depdrop_parents');
			$params = \Input::get('depdrop_params');
        	$div_id = empty($ids[0]) ? null : $ids[0];
        	$scheme_id = empty($params[0]) ? null : $params[0];
			$scheme = \Scheme::find($scheme_id);
			$records = \Pricelist::select('category_code as id', 'category_desc as name')
				->where('division_code',$div_id)
				->groupBy('category_code')
				->orderBy('category_desc')->get();

			$data['output'] = $records;
			if(!empty($scheme->scategory_code)){
				$data['selected'] = $scheme->scategory_code;
			}
			
			return \Response::json($data,200);
		}
	}

	public function sobbrand()
	{
		if(\Request::ajax()){
			$ids = \Input::get('depdrop_parents');
			$params = \Input::get('depdrop_params');
        	$cat_id = empty($ids[0]) ? null : $ids[0];
        	$scheme_id = empty($params[0]) ? null : $params[0];
			$scheme = \Scheme::find($scheme_id);

			$records = \Pricelist::select('brand_desc as id', 'brand_desc as name')
				->where('active',1)
				->where('launch',0)
				->where('category_code',$cat_id)
				->groupBy('brand_desc')
				->orderBy('brand_desc')->get();



			$data['output'] = $records;
			if(!empty($scheme->brand_desc)){
				$data['selected'] = $scheme->brand_desc;
			}
			
			return \Response::json($data,200);
		}
	}

	public function categories()
	{
		if(\Request::ajax()){
			$q = \Input::get('q');
			$data = \Pricelist::select('category_code', 'category_desc')
			// ->where('division_code',\Input::get('q'))
			->whereIn('division_code',$q)
			->groupBy('category_code')
			->orderBy('category_desc')->lists('category_desc', 'category_code');

			return \Response::json($data,200);
		}
	}

	public function categoryselected()
	{
		if(\Request::ajax()){
			$divisions = \Input::get('divisions');
			$id = \Input::get('id');
			$data['selection'] = \Pricelist::select('category_code', 'category_desc')
			->whereIn('division_code',$divisions)
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
				// $data = \Pricelist::select('cpg_code', \DB::raw('CONCAT(brand_desc, " - ", cpg_desc) AS brand_desc'))
				// ->where('active',1)
				// ->where('launch',0)
				// ->whereIn('category_code',$filter)
				// ->groupBy('cpg_code')
				// ->orderBy('brand_desc')->get();

				$data = \Pricelist::select('brand_desc')
				->where('active',1)
				->where('launch',0)
				->whereIn('category_code',$filter)
				->groupBy('brand_desc')
				->orderBy('brand_desc')->get();

				if(\Auth::user()->inRoles(['PROPONENT'])){
					$user_id = \Auth::id();
				}else{
					
				}

				$data2 = \LaunchSkuAccess::select('brand_desc')
				->join('pricelists','pricelists.sap_code','=','launch_sku_access.sku_code','left')
				->where('launch_sku_access.user_id',$user_id)
				->where('active',1)
				->where('launch',1)
				->whereIn('category_code',$filter)
				->groupBy('brand_desc')
				->orderBy('brand_desc')->get();

				foreach($data2 as $row) {
				    $data->add($row);
				}
			}



			return \Response::json($data->lists('brand_desc', 'brand_desc'),200);
		}
	}



	public function brandselected()
	{
		if(\Request::ajax()){
			$filter = \Input::get('categories');
			
			$id = \Input::get('id');
			$activity = \Activity::find($id);
			$data = array();
			$data['selection']= array();
			if($filter != ''){
				// $selection = \Pricelist::select('cpg_code', \DB::raw('CONCAT(brand_desc, " - ", cpg_desc) AS brand_desc'))
				// ->whereIn('category_code',$filter)
				// ->where('active',1)
				// ->where('launch',0)
				// ->groupBy('cpg_code')
				// ->orderBy('brand_desc')->get();

				$selection = \Pricelist::select('brand_desc')
					->where('active',1)
					->where('launch',0)
					->whereIn('category_code',$filter)
					->groupBy('brand_desc')
					->orderBy('brand_desc')->get();

				if(\Auth::user()->inRoles(['PROPONENT'])){
					$user_id = \Auth::id();
				}else{
					$user_id = $activity->created_by;
				}

				$data2 = \LaunchSkuAccess::select('brand_desc')
				->join('pricelists','pricelists.sap_code','=','launch_sku_access.sku_code','left')
				->where('launch_sku_access.user_id',$user_id)
				->where('active',1)
				->where('launch',1)
				->whereIn('category_code',$filter)
				->groupBy('brand_desc')
				->orderBy('brand_desc')->get();

				foreach($data2 as $row) {
				    $selection->add($row);
				}

				$data['selection'] = $selection->lists('brand_desc', 'brand_desc');
			}

			$data['selected'] = \ActivityBrand::selected_brand($id);

			return \Response::json($data,200);
		}
	}

	public function getReferenceSku(){
		if(\Request::ajax()){
			$id = \Input::get('id');
			$activity = \Activity::findOrFail($id);
			$divisions = \ActivityDivision::getList($id);
			$categories = \ActivityCategory::selected_category($id);
			$brands = \ActivityBrand::selected_brand($id);
			$data = \Sku::items($divisions,$categories,$brands);
			return \Response::json($data,200);
		}
	}

	

}