<?php
use Alchemy\Zippy\Zippy;
class TradealSchemeController extends \BaseController {
	
	public function edit($id)
	{
		$scheme = TradedealScheme::findOrFail($id);
		$tradedeal = Tradedeal::findOrFail($scheme->tradedeal_id);
		$activity = Activity::findOrFail($tradedeal->activity_id);
		$dealtypes = TradedealType::getList();
		$dealuoms = TradedealUom::get()->lists('tradedeal_uom', 'id');
		$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();

		$sel_hosts = TradedealSchemeSku::getSelected($scheme);
		return View::make('tradealscheme.edit', compact('activity', 'tradedeal', 'scheme', 'dealtypes', 
			'dealuoms', 'tradedeal_skus', 'channels', 'sel_channels', 'sel_hosts'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /tradealscheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$scheme = TradedealScheme::findOrFail($id);
		$tradedeal = Tradedeal::find($scheme->tradedeal_id);
		$activity = Activity::findOrFail($tradedeal->activity_id);

		$deal_type = TradedealType::find(Input::get('deal_type'));
		$uom = TradedealUom::find(Input::get('uom'));
		$selected_skus = [];
		$free_pcs_case = [];

		$invalid_premiums = true;

		if(Input::has('skus')){
			foreach (Input::get('skus') as $value) {
			 	$selected_skus[] = $value;
			 	$free_sku = TradedealPartSku::find($value);
				$free_pcs_case[] = $free_sku->pre_pcs_case;
			}
			if($uom->id == 3){
				$result = array_unique($free_pcs_case);
				if(count($result) > 1){
					$invalid_premiums = false;
				}
			}
			
		}

		$invalid_collective = true;
		if(($deal_type->id == 2) && ($uom->id == 3)){
			$host_pcs_case = [];
			if(count($selected_skus) > 0){
				foreach ($selected_skus as $value) {
					$part_sku = TradedealPartSku::find($value);
					$host_pcs_case[] = $part_sku->host_pcs_case;
				}
			}
			
			$result = array_unique($host_pcs_case);

			if(count($result) > 1){
				$invalid_collective = false;
			}

		}

		Validator::extend('invalid_collective', function($attribute, $value, $parameters) {
		    return $parameters[0];
		});

		Validator::extend('invalid_premiums', function($attribute, $value, $parameters) {
		    return $parameters[0];
		});
		Validator::extend('invalid_collective', function($attribute, $value, $parameters) {
		    return $parameters[0];
		});

		$messages = array(
		    'invalid_premiums' => 'Combination of Premium SKU with different pcs/case value is not allowed',
		    'invalid_collective' => 'Combination of participating SKU with different pcs/case value is not allowed',
		    'channels.required' => 'Channels Involved is required',
		);


		$rules = array(
			'scheme_name' => 'required|unique:tradedeal_schemes,name,'.$id.',id,tradedeal_id,'.$tradedeal->id,
		    'skus' => 'required|invalid_collective:'.$invalid_collective.'|invalid_premiums:'.$invalid_premiums,
		    'buy' => 'required|numeric',
		    'free' => 'required|numeric',
		    'channels' => 'required'
		);

		$validation = Validator::make(Input::all(), $rules, $messages);

		
		if($validation->passes()){
			
			
			$buy = str_replace(",", '', Input::get('buy'));
			$free = str_replace(",", '', Input::get('free'));

			$scheme->tradedeal_id = $tradedeal->id;
			$scheme->name = strtoupper(Input::get('scheme_name'));
			$scheme->tradedeal_type_id = $deal_type->id;
			$scheme->buy = $buy;
			$scheme->free = $free;
			$scheme->coverage = str_replace(",", '', Input::get('coverage'));
			$scheme->tradedeal_uom_id = $uom->id;

			$pcs_deal = 0;
			if($scheme->tradedeal_uom_id == 1){
				$pcs_deal = 1;
			}else if($scheme->tradedeal_uom_id == 2){
				$pcs_deal = 12;
			}else{
				if($tradedeal->non_ulp_premium){
					$pcs_deal = $tradedeal->non_ulp_pcs_case;
				}else{
					if(Input::has('skus')){
						$premium = TradedealPartSku::where('id', Input::get('skus')[0])->first();
					}
					$pcs_deal = $premium->pre_pcs_case;
				}
				
			}

			$scheme->pcs_deal = $pcs_deal;

			if(Input::has('premium_sku')){
				$premuim_sku = TradedealPartSku::find(Input::get('premium_sku'));

				$scheme->pre_id = $premuim_sku->id;
				$scheme->pre_code = $premuim_sku->pre_code;
				$scheme->pre_desc = $premuim_sku->pre_desc;
				$scheme->pre_cost = $premuim_sku->pre_cost;
				$scheme->pre_pcs_case = $premuim_sku->pre_pcs_case;
			}

			$scheme->save();
			
			TradedealSchemeSku::where('tradedeal_scheme_id', $scheme->id)->delete();
			$selectedskus = [];
			if(Input::has('skus')){
				TradedealSchemeSku::addHostSku(Input::get('skus'),$scheme);
			}
				
			TradedealSchemeChannel::createChannelSelection($scheme, $activity, Input::get('channels'));		

			// update trade deal scheme allocations

			TradedealAllocRepository::updateAllocation($scheme);
			// LeTemplateRepository::generateTemplate($scheme);
			
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $tradedeal->activity_id)) . "#tradedeal")
				->with('class', 'alert-success')
				->with('message', 'Scheme successfuly updated');
		}

		return Redirect::back()
				->withInput()
				->withErrors($validation)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
		
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /tradealscheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function exportle($id){
		$zippy = Zippy::load();
		$activity = Activity::findOrFail($id);
		$tradedeal = Tradedeal::getActivityTradeDeal($activity);

		$folders = array();
		$foldername = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
		$folder_name = str_replace(":","_", $foldername);
		$zip_path = storage_path().'/zipped/le/'.strtoupper(Helper::sanitize($folder_name)).'.zip';
		File::delete($zip_path);
		$nofile = 'public/nofile/robots.txt';
		$path = '/le/'.$activity->id;
		$distination = storage_path().$path ;
		
		$folders = [];
		$tradealschemes = TradedealScheme::where('tradedeal_id', $tradedeal->id)->get();
		foreach ($tradealschemes as $scheme) {
			$_path = $distination.'/'.$scheme->id;
			if(File::exists($_path)) {
				$folders[ $folder_name. '/'.$scheme->name] = $_path;
			}	
		}
		
		$archive = $zippy->create($zip_path,$folders,true);
		return Response::download($zip_path);
	}

}