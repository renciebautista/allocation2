<?php

use Rencie\Cpm\CpmActivity;
use Rencie\Cpm\Cpm;
class DashboardController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /dashboard
	 *
	 * @return Response
	 */
	public function index()
	{
		// $activity_1 = new  CpmActivity;
		// $activity_1->id = 'A';
		// $activity_1->description = 'Market Analysis';
		// $activity_1->duration = 1;
		// // $activity_2->predecessors = array();
		// $activities[] = $activity_1;

		// $activity_2 = new  CpmActivity;;
		// $activity_2->id = 'B';
		// $activity_2->description = 'Product Design';
		// $activity_2->duration = 3;
		// $activity_2->predecessors = array('A');
		// $activities[] = $activity_2;

		// $activity_3 = new CpmActivity;
		// $activity_3->id = 'C';
		// $activity_3->description = 'Manufacturing Study';
		// $activity_3->duration = 1;
		// $activity_3->predecessors = array('A');
		// $activities[] = $activity_3;

		// $activity_4 = new CpmActivity;
		// $activity_4->id = 'D';
		// $activity_4->description = 'Select best product design';
		// $activity_4->duration = 1;
		// $activity_4->predecessors = array('B','C');
		// $activities[] = $activity_4;

		// $activity_5 = new CpmActivity;
		// $activity_5->id = 'E';
		// $activity_5->description = 'Detailed Marketing Plans';
		// $activity_5->duration = 1;
		// $activity_5->predecessors = array('D');
		// $activities[] = $activity_5;


		// $cpm = new Cpm($activities);
		// echo $cpm->TotalDuration();

		return View::make('dashboard.index');
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /dashboard/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /dashboard
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /dashboard/{id}
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
	 * GET /dashboard/{id}/edit
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
	 * PUT /dashboard/{id}
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
	 * DELETE /dashboard/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}