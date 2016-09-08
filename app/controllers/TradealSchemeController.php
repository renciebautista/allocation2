<?php

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
		$dealtypes = TradedealType::get()->lists('tradedeal_type', 'id');
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
		$tradedeal = Tradedeal::find($scheme->tradedeal_id);
		$deal_type = TradedealType::find(Input::get('deal_type'));
		$uom = TradedealUom::find(Input::get('uom'));
		$selected = Input::get('ch');

		$buy = str_replace(",", '', Input::get('buy'));
		$free = str_replace(",", '', Input::get('free'));

		// dd($uom);

		$scheme->tradedeal_id = $tradedeal->id;
		$scheme->name = $deal_type->tradedeal_type.": ".$buy."+".$free." ".$uom->tradedeal_uom;
		$scheme->tradedeal_type_id = $deal_type->id;
		$scheme->buy = $buy;
		$scheme->free = $free;
		$scheme->coverage = str_replace(",", '', Input::get('coverage'));
		$scheme->tradedeal_uom_id = $uom->id;
		if(Input::has('premium_sku')){
			$scheme->pre_id = Input::get('premium_sku');
		}
		$scheme->save();

		TradedealSchemeSku::where('tradedeal_scheme_id', $scheme->id)->delete();
		$selectedskus = [];
		if(Input::has('skus')){
			foreach (Input::get('skus') as $value) {
				if($deal_type->id == 1){
					TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
					'tradedeal_part_sku_id' => $value,
					'qty' => 1]);
				}else{
					TradedealSchemeSku::create(['tradedeal_scheme_id' => $scheme->id,
					'tradedeal_part_sku_id' => $value,
					'qty' => Input::get('qty')[$value]]);
				}
				
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
		
		return Redirect::back()
			->with('class', 'alert-success')
			->with('message', 'Scheme successfuly updated');
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

}