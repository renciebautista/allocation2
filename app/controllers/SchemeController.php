<?php

class SchemeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /scheme
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$schemes = Scheme::where('activity_id',$id)->get();
		return View::make('scheme.index', compact('id', 'schemes'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /scheme/create
	 *
	 * @return Response
	 */
	public function create($id)
	{
		$activity = Activity::find($id);

		$categories = ActivityCategory::selected_category($id);
		$brands = ActivityBrand::selected_brand($id);

		$skus = Sku::orderBy('sku_desc')
			->where('division_code',$activity->division_code)
			->whereIn('category_code',$categories)
			->whereIn('brand_code',$brands)
			->lists('sku_desc', 'sku_code');

		return View::make('scheme.create', compact('id','skus'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /scheme
	 *
	 * @return Response
	 */
	public function store($id)
	{
		$validation = Validator::make(Input::all(), Scheme::$rules);

		if($validation->passes())
		{
			DB::transaction(function() {
				$scheme = new Scheme;
				$scheme->activity_id = Input::get('activity_id');
				$scheme->name = Input::get('name');
				$scheme->quantity = Input::get('quantity');
				$scheme->user_id = Auth::id();
				$scheme->save();

				$skus = array();
				foreach (Input::get('skus') as $sku){
					$skus[] = array('scheme_id' => $scheme->id, 'sku' => $sku);
				}
				SchemeSku::insert($skus);
				
			});

			return Redirect::action('SchemeController@index', array('id' => $id))
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
			
		}

		return Redirect::action('SchemeController@create', array('id' => $id))
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{	
		$scheme = Scheme::find($id);
		$skus = SchemeSku::getSkus($id);
		$customers = ActivityCustomer::customers($scheme->activity_id);
		// echo '<pre>';
		// print_r($customers);
		// echo '</pre>';

		$channels = array('C1', 'C2', 'C3');
		$qty = $scheme->quantity;

		$_allocation = new AllocationRepository;
		$allocations = $_allocation->customers($skus, $channels, $customers);
		
		$total_sales = $_allocation->total_sales();
		return View::make('scheme.show', compact('allocations','total_sales', 'qty','id'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /scheme/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}