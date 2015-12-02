<?php

class PricelistController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /pricelist
	 *
	 * @return Response
	 */
	public function index()
	{
		$pricelists = Pricelist::getAll();
		return View::make('pricelist.index', compact('pricelists'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /pricelist/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /pricelist
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /pricelist/{id}
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
	 * GET /pricelist/{id}/edit
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
	 * PUT /pricelist/{id}
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
	 * DELETE /pricelist/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$items = Pricelist::all();
		Excel::create("Pricelist", function($excel) use($items){
			$excel->sheet('Sheet1', function($sheet) use($items) {
				$sheet->fromModel($items,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('pricelist.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Pricelist::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('PricelistController@index')
					->with('class', 'alert-success')
					->with('message', 'Price list successfuly updated');
		}else{

			return Redirect::action('PricelistController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}