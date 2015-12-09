<?php

class SubChannelController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /subchannel
	 *
	 * @return Response
	 */
	public function index()
	{
		$subchannels = Subchannel::getAll();
		return View::make('subchannel.index',compact('subchannels'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /subchannel/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /subchannel
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /subchannel/{id}
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
	 * GET /subchannel/{id}/edit
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
	 * PUT /subchannel/{id}
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
	 * DELETE /subchannel/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		$subchannels = Subchannel::all();

		Excel::create("SubChannels", function($excel) use($subchannels){
			$excel->sheet('Sheet1', function($sheet) use($subchannels) {
				$sheet->fromModel($subchannels,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('subchannel.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Subchannel::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('SubchannelController@index')
					->with('class', 'alert-success')
					->with('message', 'Sub Channel list successfuly updated');
		}else{

			return Redirect::action('SubchannelController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}