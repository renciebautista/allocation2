<?php

class MotherchildskuController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /motherchildsku
	 *
	 * @return Response
	 */
	public function index()
	{
		$skus = MotherChildSku::all();
		return View::make('motherchildsku.index',compact('skus'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /motherchildsku/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /motherchildsku
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /motherchildsku/{id}
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
	 * GET /motherchildsku/{id}/edit
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
	 * PUT /motherchildsku/{id}
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
	 * DELETE /motherchildsku/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$skus = MotherChildSku::all();

		Excel::create("Mother Child SKU", function($excel) use($skus){
			$excel->sheet('Sheet1', function($sheet) use($skus) {
				$sheet->fromModel($skus,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('motherchildsku.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				MotherChildSku::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('MotherchildskuController@index')
					->with('class', 'alert-success')
					->with('message', 'Mother Child SKU list successfuly updated');
		}else{

			return Redirect::action('MotherchildskuController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}