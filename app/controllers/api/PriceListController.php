<?php
namespace Api;
class PriceListController extends \BaseController {

	public function getSku(){
		if(\Request::ajax()){
			$code = \Input::get('code');
			$data = \Pricelist::where('sap_code', $code)->first();
			return \Response::json($data,200);
		}
	}

	public function tdpricelistsku(){
		if(\Request::ajax()){
			$code = \Input::get('code');
			$activity_id = \Input::get('ac_id');
			$new_sku = \Pricelist::where('sap_code', $code)->first();
			$data['sku'] = $new_sku;
			$data['variant'] = '';

			$sku =  \TradedealPartSku::where('host_code', $code)->where('activity_id', $activity_id)->first();
			if(!empty($sku)){
				$data['variant']  = $sku->variant;
			}

			return \Response::json($data,200);
		}
	}

	public function tdprepricelistsku(){
		if(\Request::ajax()){
			$code = \Input::get('code');
			$activity_id = \Input::get('ac_id');
			$new_sku = \Pricelist::where('sap_code', $code)->first();
			$data['sku'] = $new_sku;
			$data['variant'] = '';

			$sku =  \TradedealPartSku::where('pre_code', $code)->where('activity_id', $activity_id)->first();
			if(!empty($sku)){
				$data['variant']  = $sku->pre_variant;
			}

			return \Response::json($data,200);
		}
	}

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

	public function getHostSku(){
		if(\Request::ajax()){
			$id = \Input::get('id');
			$activity = \Activity::findOrFail($id);
			$brands = \ActivityBrand::selected_brand($activity->id);
			$data = \Pricelist::involves($brands,$activity);
			return \Response::json($data,200);
		}
	}

	public function getPremiumSku(){
		if(\Request::ajax()){
			$data = \Pricelist::items();
			$data[0] = "";
			return \Response::json($data,200);
		}
	}
}