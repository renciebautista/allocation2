<?php
use Alchemy\Zippy\Zippy;
class TradealSchemeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /tradealscheme
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /tradealscheme/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /tradealscheme
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /tradealscheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /tradealscheme/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$scheme = TradedealScheme::findOrFail($id);
		$tradedeal = Tradedeal::findOrFail($scheme->tradedeal_id);
		$activity = Activity::findOrFail($tradedeal->activity_id);
		$dealtypes = TradedealType::getList();
		$dealuoms = TradedealUom::get()->lists('tradedeal_uom', 'id');
		$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();
		$channels = TradedealChannel::getSchemeChannels($activity, $scheme);
		$sel_channels = TradedealSchemeChannel::getSelected($scheme);
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
		// dd(Input::all());
		$scheme = TradedealScheme::findOrFail($id);
		// Helper::debug($scheme);
		$tradedeal = Tradedeal::find($scheme->tradedeal_id);

		$deal_type = TradedealType::find(Input::get('deal_type'));
		// Helper::debug($deal_type);
		$uom = TradedealUom::find(Input::get('uom'));
		// Helper::debug($uom);
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
		    'ch.required' => 'Channels Involved is required',
		);


		$rules = array(
			'scheme_name' => 'required|unique:tradedeal_schemes,name,'.$id.',id,tradedeal_id,'.$tradedeal->id,
		    'skus' => 'required|invalid_collective:'.$invalid_collective.'|invalid_premiums:'.$invalid_premiums,
		    'buy' => 'required|numeric',
		    'free' => 'required|numeric',
		    'ch' => 'required'
		);

		$validation = Validator::make(Input::all(), $rules, $messages);

		
		if($validation->passes()){
			
			
			$selected = Input::get('ch');

			$buy = str_replace(",", '', Input::get('buy'));
			$free = str_replace(",", '', Input::get('free'));

			$scheme->tradedeal_id = $tradedeal->id;
			// $scheme->name = $deal_type->tradedeal_type.": ".$buy."+".$free." ".$uom->tradedeal_uom;
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

				// Helper::debug($premuim_sku);
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
				foreach (Input::get('skus') as $value) {
					if($deal_type->id == 1){
						$host = TradedealPartSku::find($value);
						if($scheme->tradedeal_uom_id == 1){
							$pur_req = $buy * $host->host_cost;
							$pre_cost = $free * $host->pre_cost;
						}else if($scheme->tradedeal_uom_id == 2){
							$pur_req = $buy * $host->host_cost * 12;
							$pre_cost = $free * $host->pre_cost * 12;
						}else{
							$pur_req = $buy * $host->host_cost * $host_pcs_case;
							$pre_cost = $free * $host->pre_cost * $pre_pcs_case;
						}
						TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
						'tradedeal_part_sku_id' => $value,
						'qty' => 1,
						'pur_req' => $pur_req,
						'free_cost' => $pre_cost,
						'cost_to_sale' =>  ($pre_cost/$pur_req) * 100]);
					}else if($deal_type->id == 2){
						TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
						'tradedeal_part_sku_id' => $value,
						'qty' => Input::get('qty')[$value]]);
					}else{
						TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
						'tradedeal_part_sku_id' => $value,
						'qty' => 1]);
					}	
				}

				if($deal_type->id == 3){
					$lowest_cost = 0;
					$skus = [];
					foreach (Input::get('skus') as $sku){
						$host = TradedealPartSku::find($value);
						if($lowest_cost == 0){
				        	$lowest_cost = $host->host_cost;
				        	$skus[0] = $host;
				        }else{
				        	if($lowest_cost > $host->host_cost){
					        	$lowest_cost = $host->host_cost;
					        	$skus[0] = $host;
					        }
				        }
					}
						
					$_scheme = TradedealScheme::findOrFail($id);
					// Helper::debug($_scheme);
					$pur_req = 0;
					$pre_cost = 0;

					// Helper::debug($_scheme);

					if($_scheme->tradedeal_uom_id == 1){
						$pur_req = $buy * $host->host_cost;
						$pre_cost = $free * $host->pre_cost;
					}else if($_scheme->tradedeal_uom_id == 2){
						$pur_req = $buy * $host->host_cost * 12;
						$pre_cost = $free * $host->pre_cost * 12;
					}else{
						$pur_req = $buy * $host->host_cost * $host_pcs_case;
						$pre_cost = $free * $host->pre_cost * $pre_pcs_case;
					}
					
					
					$_scheme->pur_req = $pur_req;
					$_scheme->free_cost = $pre_cost;
					$_scheme->cost_to_sale = ($pre_cost/$pur_req) * 100;
					$_scheme->save();
				}

			}


			TradedealSchemeChannel::where('tradedeal_scheme_id', $scheme->id)->delete();
			if(Input::has('ch')){
				foreach ($selected as $value) {
					$ch = new TradedealSchemeChannel;
					$ch->tradedeal_scheme_id = $scheme->id;
					$ch->tradedeal_channel_id = $value;
					$ch->save();
				}
			}

			// update trade deal scheme allocations

			TradedealAllocRepository::updateAllocation($scheme);
			LeTemplateRepository::generateTemplate($scheme);
			
			// return Redirect::back()
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $tradedeal->activity_id)) . "#tradedeal")
				->with('class', 'alert-success')
				->with('message', 'Scheme successfuly updated');
		}

		return Redirect::back()
		// return Redirect::to(URL::action('ActivityController@edit', array('id' => $tradedeal->activity_id)) . "#tradedeal")
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