<?php

class ShiptoPlantCodeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /shiptoplantcode
	 *
	 * @return Response
	 */
	public function index()
	{
		$mappings = ShipToPlantCode::all();
		return View::make('shiptoplantcode.index',compact('mappings'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /shiptoplantcode/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /shiptoplantcode
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /shiptoplantcode/{id}
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
	 * GET /shiptoplantcode/{id}/edit
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
	 * PUT /shiptoplantcode/{id}
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
	 * DELETE /shiptoplantcode/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$mappings = ShipToPlantCode::all();

		Excel::create("Ship To / Plant Code Mapping", function($excel) use($mappings){
			$excel->sheet('Sheet1', function($sheet) use($mappings) {
				$sheet->fromModel($mappings,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('shiptoplantcode.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				ShipToPlantCode::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('ShiptoPlantCodeController@index')
					->with('class', 'alert-success')
					->with('message', 'Ship To / Plant Code mapping successfuly updated');
		}else{

			return Redirect::action('ShiptoPlantCodeController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}
}