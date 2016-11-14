<?php

class CustomerController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /customer
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$customers = Customer::search(Input::all());
		return View::make('customer.index',compact('customers'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /customer/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /customer
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /customer/{id}
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
	 * GET /customer/{id}/edit
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
	 * PUT /customer/{id}
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
	 * DELETE /customer/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$customer = Customer::findOrFail($id);
		$customer->delete();
		$class = 'alert-success';
		$message = 'Customer successfully deleted.';
		// }else{
		// 	$class = 'alert-danger';
		// 	$message = 'Cannot delete cycle.';
		// }
		
		return Redirect::route('customer.index')
				->with('class', $class )
				->with('message', $message);
	}

	public function export(){
		$customers = Customer::select('id', 'area_code',  'customer_code', 'customer_name', 'trade_deal', 'multiplier','active')->get();
		Excel::create("Customers", function($excel) use($customers){
			$excel->sheet('Sheet1', function($sheet) use($customers) {
				$sheet->fromModel($customers,null, 'A1', true);

			})->download('xls');
		});
	}

	public function import(){
		return View::make('customer.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Customer::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('CustomerController@index')
					->with('class', 'alert-success')
					->with('message', 'Customer list successfuly updated');
		}else{

			return Redirect::action('CustomerController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}