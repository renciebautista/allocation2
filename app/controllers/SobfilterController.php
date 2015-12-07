<?php

class SobfilterController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /sobfilter
	 *
	 * @return Response
	 */
	public function index()
	{
		$filters = SobFilter::all();
		return View::make('sobfilter.index',compact('filters'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /sobfilter/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /sobfilter
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /sobfilter/{id}
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
	 * GET /sobfilter/{id}/edit
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
	 * PUT /sobfilter/{id}
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
	 * DELETE /sobfilter/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$filters = SobFilter::all();

		Excel::create("SOB Filters", function($excel) use($filters){
			$excel->sheet('Sheet1', function($sheet) use($filters) {
				$sheet->fromModel($filters,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('sobfilter.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				SobFilter::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('SobfilterController@index')
					->with('class', 'alert-success')
					->with('message', 'SOB Filter successfuly updated');
		}else{

			return Redirect::action('SobfilterController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}
}