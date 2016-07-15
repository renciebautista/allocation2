<?php

class Level4Controller extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /level4
	 *
	 * @return Response
	 */
	public function index()
	{
		$l4channels = Level4::all();
		return View::make('level4.index',compact('l4channels'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /level4/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /level4
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /level4/{id}
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
	 * GET /level4/{id}/edit
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
	 * PUT /level4/{id}
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
	 * DELETE /level4/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function import(){
		return View::make('level4.import');
	}

	public function export(){
		$channels = Level4::all();

		Excel::create("Level 4 Channels", function($excel) use($channels){
			$excel->sheet('Sheet1', function($sheet) use($channels) {
				$sheet->fromModel($channels,null, 'A1', true);
			})->download('xls');

		});
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Level4::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('Level4Controller@index')
					->with('class', 'alert-success')
					->with('message', 'Channel list successfuly updated');
		}else{

			return Redirect::action('Level4Controller@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}