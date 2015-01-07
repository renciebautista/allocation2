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
		return View::make('scheme.index', compact('id'));
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
	public function store()
	{
		//
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
		$skus = array('20226140');
		$channels = array('C1','C2','C3');
		$qty = 10000;
		// $allocations = Allocation::customers($skus,$channels);
		// $total_sales = Allocation::total_sales($skus,$channels);
		$_allocation = new AllocationRepository;
		$allocations = $_allocation->customers($skus,$channels);
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