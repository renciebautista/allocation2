<?php

class AreaController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /area
	 *
	 * @return Response
	 */
	public function index()
	{
		$areas = Area::getAll();
		return View::make('area.index', compact('areas'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /area/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /area
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /area/{id}
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
	 * GET /area/{id}/edit
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
	 * PUT /area/{id}
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
	 * DELETE /area/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$areas = Area::all();

		Excel::create("Areas", function($excel) use($areas){
			$excel->sheet('Sheet1', function($sheet) use($areas) {
				$sheet->fromModel($areas,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('area.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Area::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('AreaController@index')
					->with('class', 'alert-success')
					->with('message', 'Area list successfuly updated');
		}else{

			return Redirect::action('AreaController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}