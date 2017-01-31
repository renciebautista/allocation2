<?php

class AccountGroupController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /accountgroup
	 *
	 * @return Response
	 */
	public function index()
	{
		$accountgroups = AccountGroup::all();
		return View::make('accountgroup.index', compact('accountgroups'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /accountgroup/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /accountgroup
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /accountgroup/{id}
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
	 * GET /accountgroup/{id}/edit
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
	 * PUT /accountgroup/{id}
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
	 * DELETE /accountgroup/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$accountgroups = AccountGroup::all();

		Excel::create("Account Groups", function($excel) use($accountgroups){
			$excel->sheet('Sheet1', function($sheet) use($accountgroups) {
				$sheet->fromModel($accountgroups,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('accountgroup.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				AccountGroup::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('AccountGroupController@index')
					->with('class', 'alert-success')
					->with('message', 'Account Group list successfuly updated');
		}else{

			return Redirect::action('AccountGroupController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}