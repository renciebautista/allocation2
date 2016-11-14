<?php

class ChannelController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /channel
	 *
	 * @return Response
	 */
	public function index()
	{
		$channels = SubChannel::getAll();
		return View::make('channel.index',compact('channels'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /channel/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /channel
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /channel/{id}
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
	 * GET /channel/{id}/edit
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
	 * PUT /channel/{id}
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
	 * DELETE /channel/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function export(){
		// $channels = Channel::all();
		$channels = SubChannel::getAll();
		Excel::create("Channels", function($excel) use($channels){
			$excel->sheet('Sheet1', function($sheet) use($channels) {
				$sheet->fromModel($channels,null, 'A1', true);

			})->download('xls');

		});
	}

	public function import(){
		return View::make('channel.import');
	}

	public function upload(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Channel::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('ChannelController@index')
					->with('class', 'alert-success')
					->with('message', 'Channel list successfuly updated');
		}else{

			return Redirect::action('ChannelController@import')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}