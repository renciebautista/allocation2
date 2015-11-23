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
		$weeks = Week::getDays();
		return View::make('shipto.edit', compact('shipto','weeks'));
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
		$shipto = ShipTo::findOrFail($id);
		$input = Input::all();
		$rules = array(
	        'ship_to_name' => 'required|between:4,128|unique:roles,name,'.$id,
	        'dayofweek' => 'required',
	        'leadtime' => 'required|min:1'
	    );
		$validation = Validator::make($input,$rules);

		if($validation->passes())
		{
			DB::beginTransaction();

			try {
				$shipto->ship_to_code = strtoupper(Input::get('ship_to_code'));
				$shipto->ship_to_name = strtoupper(Input::get('ship_to_name'));
				$shipto->dayofweek = Input::get('dayofweek');
				$shipto->leadtime = Input::get('leadtime');
				$shipto->update();

				DB::commit();

				return Redirect::route('shipto.edit', $id)
					->withInput()
					->with('class', 'alert-success')
					->with('message', 'Ship To details successfully updated.');
			} catch (Exception $e) {
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