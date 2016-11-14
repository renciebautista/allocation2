<?php

class CustomerRemapController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /customerremap
	 *
	 * @return Response
	 */
	public function index()
	{
		$customers = SplitOldCustomer::getAll();
		return View::make('customerremap.index',compact('customers'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /customerremap/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /customerremap
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /customerremap/{id}
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
	 * GET /customerremap/{id}/edit
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
	 * PUT /customerremap/{id}
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
	 * DELETE /customerremap/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$customers = SplitOldCustomer::all();
		Excel::create("Customer Inactive - Active Mapping", function($excel) use($customers){
			$excel->sheet('Sheet1', function($sheet) use($customers) {
				$sheet->row(1,['id', 'from_customer', 'to_plant', 'to_customer', 'to_plant', 'split']);
				$sheet->fromModel($customers,null, 'A1', false);
			})->download('xls');

		});
	}

	public function import(){
		return View::make('customerremap.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				SplitOldCustomer::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('CustomerRemapController@index')
					->with('class', 'alert-success')
					->with('message', 'Customer Inactive / Active mapping successfuly updated');
		}else{

			return Redirect::action('CustomerRemapController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}