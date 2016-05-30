<?php
namespace Api;
use Input;
class SobController extends \BaseController {

	public function years()
	{
		if(\Request::ajax()){
	        $exporttype = Input::get('depdrop_all_params')['exporttype'];

			$records = \AllocationSob::select('year as id', 'year as name')
				->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
				->join('activities', 'activities.id', '=', 'schemes.activity_id')
				->where('activities.status_id', 9)
				->where(function($query) use ($exporttype){
					if($exporttype == 1){
						$query->whereNull('po_no');
					}else{
						$query->whereNotNull('po_no');
					}
				})
				->groupBy('year')
				->orderBy('year')
				->get();
			$data['output'] = $records;
			$data['selected'] = Input::get('depdrop_all_params')['year_id'];
			return \Response::json($data,200);
		}
	}

	public function weeks()
	{
		if(\Request::ajax()){
			$exporttype = Input::get('depdrop_all_params')['exporttype'];
	        $year = Input::get('depdrop_all_params')['year'];
			$records = \AllocationSob::select('weekno as id', 'weekno as name')
				->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
				->join('activities', 'activities.id', '=', 'schemes.activity_id')
				->where('activities.status_id', 9)
				->where('year',$year)
				->where(function($query) use ($exporttype){
					if($exporttype == 1){
						$query->whereNull('po_no');
					}else{
						$query->whereNotNull('po_no');
					}
				})
				->groupBy('weekno')
				->orderBy('weekno')
				->get();
			$data['output'] = $records;
			$data['selected'] = Input::get('depdrop_all_params')['week_id'];
			return \Response::json($data,200);
		}
	}

	public function weekactivitytype()
	{
		if(\Request::ajax()){
			$exporttype = Input::get('depdrop_all_params')['exporttype'];
			$year = Input::get('depdrop_all_params')['year'];
			$week = Input::get('depdrop_all_params')['week'];
			$records =  \AllocationSob::select('activity_type_id as id', 'activitytype_desc as name')
				->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
				->join('activities', 'activities.id', '=', 'schemes.activity_id')
				->where('activities.status_id', 9)
				->where('year',$year)
				->where('weekno',$week)
				->where(function($query) use ($exporttype){
					if($exporttype == 1){
						$query->whereNull('po_no');
					}else{
						$query->whereNotNull('po_no');
					}
				})
				
				->groupBy('activity_type_id')
				->orderBy('activitytype_desc')
				->get();
			$data['output'] = $records;
			$data['selected'] = Input::get('depdrop_all_params')['type_id'];
			return \Response::json($data,200);
		}
	}

	public function weekbrand()
	{
		if(\Request::ajax()){
			$exporttype = Input::get('depdrop_all_params')['exporttype'];
			$year = Input::get('depdrop_all_params')['year'];
			$week = Input::get('depdrop_all_params')['week'];
			$type = Input::get('depdrop_all_params')['activity_type'];

			$records =  \AllocationSob::select('schemes.brand_shortcut as id', 'schemes.brand_shortcut as name')
				->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
				->join('activities', 'activities.id', '=', 'schemes.activity_id')
				->where('activities.status_id', 9)
				->where('year',$year)
				->where('weekno',$week)
				->where('activity_type_id',$type)
				->where(function($query) use ($exporttype){
					if($exporttype == 1){
						$query->whereNull('po_no');
					}else{
						$query->whereNotNull('po_no');
					}
				})
				->groupBy('brand_shortcut')
				->orderBy('brand_shortcut')
				->get();
			$data['output'] = $records;
			$data['selected'] = Input::get('depdrop_all_params')['brand_id'];
			return \Response::json($data,200);
		}
	}
}