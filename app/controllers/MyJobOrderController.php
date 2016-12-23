<?php

class MyJobOrderController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /myjoborder
	 *
	 * @return Response
	 */
	public function index()
	{	
		$statuses = JoborderStatus::getLists();
		$jotasks = Joborder::getMyJoTask(Auth::user());
		$josubtasks = Joborder::getMyJoSubTask(Auth::user());
		$joborders = Joborder::myJoborder(Auth::user(), Input::get('st'),  Input::get('tsk'),  Input::get('stk'));
		
		return View::make('myjoborders.index',compact('joborders', 'statuses', 'jotasks', 'josubtasks'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /myjoborder/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /myjoborder
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /myjoborder/{id}
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
	 * GET /myjoborder/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$joborder = Joborder::findOrFail($id);
		if($joborder->assigned_to != Auth::user()->id){
			return Response::make(View::make('shared/404'), 404);
		}else{
			$artworks = JoborderArtwork::where('joborder_id', $joborder->id)->get();
			$comments = $joborder->comments()->orderBy('created_at', 'asc')->get();
			$jostatus = JoborderStatus::getLists();
			$joudpatestatus = JoborderStatus::getUpdateLists();
			$dept_users = User::getDepartmentStaff($joborder->department_id);
			$staff =true;
			return View::make('myjoborders.edit',compact('joborder', 'comments', 'artworks', 'jostatus', 'dept_users', 'joudpatestatus', 'staff'));
		}
		
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /myjoborder/{id}
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
	 * DELETE /myjoborder/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}