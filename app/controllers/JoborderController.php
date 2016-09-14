<?php

class JoborderController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /joborder
	 *
	 * @return Response
	 */
	public function index()
	{
		$joborders = Joborder::departmentJoborder(Auth::user());
		$statuses = JoborderStatus::getLists();
		return View::make('joborders.index',compact('joborders', 'statuses'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /joborder/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /joborder
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /joborder/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Redirect::route('joborders.edit',$id);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /joborder/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$joborder = Joborder::findOrFail($id);
		$artworks = JoborderArtwork::where('joborder_id', $joborder->id)->get();
		$comments = $joborder->comments()->orderBy('created_at')->get();
		$jostatus = JoborderStatus::getLists();
		$dept_users = User::getDepartmentStaff($joborder->department_id);
		return View::make('joborders.edit',compact('joborder', 'comments', 'artworks', 'jostatus', 'dept_users'));
	}

	public function uploadphoto($id){
		$joborder = Joborder::findOrFail($id);
		if(Input::hasFile('files')){
			$files = Input::file('files');
			$distination = storage_path().'/joborder_files/';
			foreach ($files as $file) {
				if(!empty($file)){
					$original_file_name = $file->getClientOriginalName();
					$file_name = pathinfo($original_file_name, PATHINFO_FILENAME);
					$extension = File::extension($original_file_name);
					$actual_name = uniqid('img_').'.'.$extension;
					$file->move($distination,$actual_name);

					JoborderArtwork::create(['joborder_id' => $id,
						'random_name' => $actual_name, 
						'file_name' => $file_name.'.'.$extension]);
				}
				
			}
		}


		return Redirect::to(URL::action('JoborderController@edit', array('id' => $id)))
			->with('class', 'alert-success')
			->with('message', 'Artwork was successfuly updated.');
		
	}

	public function jorderartworkdownload($random_name = null)
	{
		$file = JoborderArtwork::where('random_name', $random_name)->first();
		if(!empty($file)){
			$path = storage_path().'/joborder_files/'.$file->random_name;
			if (file_exists($path)) { 
				return Response::download($path, $file->file_name);
			}
		}
	}

	public function artworkdelete($random_name = null){
		$file = JoborderArtwork::where('random_name', $random_name)->first();
		if(!empty($file)){
			$path = storage_path().'/joborder_files/'.$file->random_name;
			if (file_exists($path)) { 
				File::delete($path);
				$file->delete();
			}
		}
		return Redirect::back()
			->with('class', 'alert-success')
			->with('message', 'Artwork was successfuly updated.');
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /joborder/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$joborder = Joborder::findOrFail($id);
		$comment = JoborderComment::create(['joborder_id' => $joborder->id, 
				'created_by' => Auth::user()->id,
				'comment' => Input::get('comment')]);

		if(Input::hasFile('files')){
			$files = Input::file('files');
			$distination = storage_path().'/joborder_files/';
			foreach ($files as $file) {
				if(!empty($file)){
					$original_file_name = $file->getClientOriginalName();
					$file_name = pathinfo($original_file_name, PATHINFO_FILENAME);
					$extension = File::extension($original_file_name);
					$actual_name = uniqid('img_').'.'.$extension;
					$file->move($distination,$actual_name);

					CommentFile::create(['comment_id' => $comment->id,
						'random_name' => $actual_name, 
						'file_name' => $file_name.'.'.$extension]);
				}
				
			}
			
		}
		
		if(Input::has('assigned_to')){
			$joborder->assigned_to = Input::get('assigned_to');
			$joborder->joborder_status_id = 2;
			$joborder->save();
		}


		return Redirect::back()
			->with('class', 'alert-success')
			->with('message', 'Comment was successfuly posted.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /joborder/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function download($random_name = null)
	{
		$file = CommentFile::where('random_name', $random_name)->first();
		if(!empty($file)){
			$path = storage_path().'/joborder_files/'.$file->random_name;
			if (file_exists($path)) { 
				return Response::download($path, $file->file_name);
			}
		}
	}

	// public function departments(){
		
	// }

	// public function unassignededit($id){
	// 	$joborder = Joborder::findOrFail($id);
	// 	$comments = $joborder->comments()->orderBy('created_at')->get();
	// 	return View::make('joborders.unassignededit',compact('joborder', 'comments'));
	// }

	public function udpate($id){
		
	}

}