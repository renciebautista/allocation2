<?php

class ShiptoController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /shipto
	 *
	 * @return Response
	 */
	public function index()
	{
		$shiptos = ShipTo::all();
		return View::make('shipto.index',compact('shiptos'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /shipto/create
	 *
	 * @return Response
	 */
	public function create()
	{

	}

	/**
	 * Store a newly created resource in storage.
	 * POST /shipto
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /shipto/{id}
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
	 * GET /shipto/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$shipto = ShipTo::findOrFail($id);
		$days = [ ['id' => 'mon', 'desc' => 'Monday'],
		 	['id' => 'tue', 'desc' => 'Tuesday'], 
		 	['id' => 'wed', 'desc' => 'Wednesday'],
		 	['id' => 'thu', 'desc' => 'Thursday'],
		 	['id' => 'fri', 'desc' => 'Friday'],
			['id' => 'sat', 'desc' => 'Saturday'],
			['id' => 'sun', 'desc' => 'Sunday']];

		return View::make('shipto.edit', compact('shipto', 'days'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /shipto/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// dd(Input::all());
		$shipto = ShipTo::findOrFail($id);
		$input = Input::all();
		$rules = array(
	        'ship_to_name' => 'required|between:2,128|unique:roles,name,'.$id,
	        'leadtime' => 'required|min:1'
	    );
		$validation = Validator::make($input,$rules);

		if($validation->passes())
		{
			DB::beginTransaction();

			try {
				$shipto->customer_code = strtoupper(Input::get('customer_code'));
				$shipto->sold_to_code = strtoupper(Input::get('sold_to_code'));
				$shipto->ship_to_code = strtoupper(Input::get('ship_to_code'));
				$shipto->ship_to_name = strtoupper(Input::get('ship_to_name'));
				if(Input::get('split') == ''){
					$shipto->split = null;
				}else{
					$shipto->split = Input::get('split');
				}
				
				$shipto->leadtime = Input::get('leadtime');
				$shipto->mon = 0;
				$shipto->tue = 0;
				$shipto->wed = 0;
				$shipto->thu = 0;
				$shipto->fri = 0;
				$shipto->sat = 0;
				$shipto->sun = 0;
				if(Input::has('days')){
					foreach (Input::get('days') as $day) {
						$shipto->$day = 1;
					}
				}
				
				$shipto->active = (Input::has('active')) ? 1 : 0;
				$shipto->update();

				DB::commit();

				return Redirect::route('shipto.edit', $id)
					->withInput()
					->with('class', 'alert-success')
					->with('message', 'Ship To details successfully updated.');
			} catch (Exception $e) {
				dd($e);
				DB::rollback();
			}

		}

		return Redirect::route('shipto.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');


		
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /shipto/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$shiptos = ShipTo::all();

		Excel::create("Ship To", function($excel) use($shiptos){
			$excel->sheet('Sheet1', function($sheet) use($shiptos) {
				$sheet->fromModel($shiptos,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('shipto.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				ShipTo::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('ShiptoController@index')
					->with('class', 'alert-success')
					->with('message', 'Ship To list successfuly updated');
		}else{

			return Redirect::action('ShiptoController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}