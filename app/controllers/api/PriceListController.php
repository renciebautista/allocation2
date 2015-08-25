<?php
namespace Api;
class PriceListController extends \BaseController {

	public function involve()
	{
		if(\Request::ajax()){
			$filter = \Input::get('brand');
			$data = array();
			if($filter != ''){
				$data = \Pricelist::select('sap_code', \DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
				->where('active',1)
				->where('launch',0)
				->whereIn('cpg_code',$filter)
				->orderBy('full_desc')->get();

				if(\Auth::user()->inRoles(['PROPONENT'])){
					$user_id = \Auth::id();
				}else{
					
				}

				$data2 = \LaunchSkuAccess::select('sap_code', \DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
				->join('pricelists','pricelists.sap_code','=','launch_sku_access.sku_code','left')
				->where('launch_sku_access.user_id',$user_id)
				->where('active',1)
				->where('launch',1)
				->whereIn('cpg_code',$filter)
				->orderBy('full_desc')->get();

				foreach($data2 as $row) {
				    $data->add($row);
				}
			}
			return \Response::json($data->lists('full_desc', 'sap_code'),200);
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
				$selection = \Pricelist::select('sap_code', \DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
				->where('active',1)
				->where('launch',0)
				->whereIn('cpg_code',$filter)
				->orderBy('full_desc')->get();

				if(\Auth::user()->inRoles(['PROPONENT'])){
					$user_id = \Auth::id();
				}else{
					$user_id = $activity->created_by;
				}

				$data2 = \LaunchSkuAccess::select('sap_code', \DB::raw('CONCAT(sap_desc, " - ", sap_code) AS full_desc'))
					->join('pricelists','pricelists.sap_code','=','launch_sku_access.sku_code','left')
					->where('launch_sku_access.user_id',$user_id)
					->where('active',1)
					->where('launch',1)
					->whereIn('cpg_code',$filter)
					->orderBy('full_desc')->get();

				foreach($data2 as $row) {
				    $selection->add($row);
				}

				$data['selection'] = $selection->lists('full_desc', 'sap_code');
			}

			$data['selected'] = \ActivitySku::getSkus($id);

			return \Response::json($data,200);
		}
	}

}