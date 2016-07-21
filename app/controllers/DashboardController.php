<?php

use Rencie\Cpm\CpmActivity;
use Rencie\Cpm\Cpm;
class DashboardController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /dashboard
	 *
	 * @return Response
	 */
	public function index()
	{
		// dd(Auth::user()->updated_at);
		$datetime1 = date_create();
		$datetime2 = date_create(Auth::user()->last_update);
		$interval = date_diff($datetime1, $datetime2);

		$settings = Setting::where('id', 1)->first();
		$change_password = false;
		if($interval->days > $settings->pasword_expiry){
			$change_password = true;
		}
		$ongoings = Activity::summary(9,'ongoing');
		// Helper::print_array($ongoings);
		$upcomings = Activity::summary(9,'nextmonth');
		// Helper::print_array($upcomings);
		$lastmonths = Activity::summary(9,'lastmonth');
		// Helper::print_array($lastmonths);
		return View::make('dashboard.index',compact('ongoings', 'upcomings', 'lastmonths', 'settings','change_password'));
	}

	
	public function filters(){
		$divisions = Sku::divisions();
		$sel_divisions = UserDivisionFilter::getList(Auth::id());
		return View::make('dashboard.filters', compact('divisions','sel_divisions'));
	}

	public function savefilters(){

		UserDivisionFilter::where('user_id',Auth::id())->delete();
		if (Input::has('division'))
		{
		   	$divisions = array();
			foreach (Input::get('division') as $division) {
				$divisions[] = array('user_id' => Auth::id(), 'division_code' => $division);
			}
			UserDivisionFilter::insert($divisions);
		}

		UserCategoryFilter::where('user_id',Auth::id())->delete();
		if (Input::has('category'))
		{
		   	$categories = array();
			foreach (Input::get('category') as $category) {
				$categories[] = array('user_id' => Auth::id(), 'category_code' => $category);
			}
			UserCategoryFilter::insert($categories);
		}

		UserBrandFilter::where('user_id',Auth::id())->delete();
		if (Input::has('brand'))
		{
		   	$brands = array();
			foreach (Input::get('brand') as $brand) {
				$brands[] = array('user_id' => Auth::id(), 'brand_code' => $brand);
			}
			UserBrandFilter::insert($brands);
		}

		UserCustomerFilter::where('user_id',Auth::id())->delete();
		$_customers = Input::get('customers');
		if(!empty($_customers)){
			$customers = explode(",", $_customers);
			if(!empty($customers)){
				$activity_customers = array();
				$area_list = array();
				foreach ($customers as $customer_node){
					$activity_customers[] = array('user_id' => Auth::id(), 'customer_node' => trim($customer_node));	
				}
				UserCustomerFilter::insert($activity_customers);
			}
		}

		return Redirect::back()
			->with('class', 'alert-success')
			->with('message', 'Activity filters successfully updated.');
	}

	public function categoryselected()
	{
		if(Request::ajax()){
			$q = Input::get('d');
			$data['selection'] = Sku::select('category_code', 'category_desc')
			->whereIn('division_code',$q)
			->groupBy('category_code')
			->orderBy('category_desc')->lists('category_desc', 'category_code');

			$data['selected'] = UserCategoryFilter::selected_category();


			return Response::json($data,200);
		}
	}

	public function brandselected()
	{
		if(Request::ajax()){
			$filter = Input::get('b');
			$data = array();
			$data['selection']= array();
			if($filter != ''){
				$data['selection'] = Sku::select('brand_code', 'brand_desc')
				->whereIn('category_code',$filter)
				->groupBy('brand_code')
				->orderBy('brand_desc')->lists('brand_desc', 'brand_code');
			}

			$data['selected'] = UserBrandFilter::selected_brand();

			return Response::json($data,200);
		}
	}

	public function customerselected(){
		$data = array();
		$sel = UserCustomerFilter::where('user_id',Auth::id())->get();
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row->customer_node;
			}
		}
		return Response::json($data,200);
	}

}