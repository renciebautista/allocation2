<?php


class BrandController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /brand
	 *
	 * @return Response
	 */
	public function index()
	{
		$brands = Pricelist::getBrands();
		return View::make('brand.index', compact('brands'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /brand/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /brand
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /brand/{id}
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
	 * GET /brand/{id}/edit
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
	 * PUT /brand/{id}
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
	 * DELETE /brand/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$brands = $brands = Pricelist::getBrands();

		Excel::create("Brands", function($excel) use($brands){
			$excel->sheet('Sheet1', function($sheet) use($brands) {
				$sheet->fromModel($brands,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('brand.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Pricelist::updateBrand($reader->get());
			});

			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}

			return Redirect::action('BrandController@index')
					->with('class', 'alert-success')
					->with('message', 'Brand list successfuly updated');
		}else{

			return Redirect::action('BrandController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}