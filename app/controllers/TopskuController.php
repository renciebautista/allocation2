<?php

class TopskuController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /topsku
	 *
	 * @return Response
	 */
	public function index()
	{
		$skus = Sku::searchTop();
		return View::make('topsku.index', compact('skus'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /topsku/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /topsku
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /topsku/{id}
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
	 * GET /topsku/{id}/edit
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
	 * PUT /topsku/{id}
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
	 * DELETE /topsku/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$skus = Sku::orderBy('division_desc')
			->orderBy('category_desc')
			->orderBy('brand_desc')
			->orderBy('sku_desc')
			->get();

		Excel::create("Top Skus", function($excel) use($skus){
			$excel->sheet('Sheet1', function($sheet) use($skus) {
				$sheet->fromModel($skus,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('topsku.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Sku::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('TopskuController@index')
					->with('class', 'alert-success')
					->with('message', 'Top SKU list successfuly updated');
		}else{

			return Redirect::action('TopskuController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
	}

}