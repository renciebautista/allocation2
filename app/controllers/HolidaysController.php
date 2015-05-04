<?php

class HolidaysController extends \BaseController {

	public function getList()
	{
		if(Request::ajax()){
			$data = array();
			$holidays = Holiday::allHoliday();
			foreach ($holidays as $holiday) {
				$data[] = date_format(date_create($holiday),'m/d/Y');
			}
			
			return Response::json($data,200);
		}
	}

	/**
	 * Display a listing of the resource.
	 * GET /holidays
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$filter = Input::get('s');
		$holidays = Holiday::where('date', '>=', date("Y-m-d"))
			->where('description', 'LIKE' ,"%$filter%")
			->get();
		return View::make('holidays.index',compact('holidays'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /holidays/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('holidays.create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /holidays
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		
		$input['date'] = date('Y-m-d',strtotime(Input::get('date')));
		// Helper::print_array($input);
		$validation = Validator::make($input, Holiday::$rules);

		if($validation->passes())
		{
			$hol = Holiday::where('date',date('Y-m-d',strtotime(Input::get('date'))))->first();
			if(count($hol)== 0){
				$holiday = new Holiday();
				$holiday->description = strtoupper(Input::get('desc'));
				$holiday->date = date('Y-m-d',strtotime(Input::get('date')));
				$holiday->save();

				return Redirect::route('holidays.index')
					->with('class', 'alert-success')
					->with('message', 'Holiday successfuly created.');
			}else{
				$messages = array(
				    'exist' => 'The selected date already exist.',
				);
				return Redirect::route('holidays.create')
					->withInput()
					->withErrors($messages)
					->with('class', 'alert-danger')
					->with('message', 'There were validation errors.');
			}
			
		}

		return Redirect::route('holidays.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}


	public function show($id)
	{
		return Redirect::route('holidays.edit', $id);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /holidays/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$holiday = Holiday::findOrFail($id);
		if (is_null($holiday))
		{
			return Redirect::route('holidays.index')
				->with('class', 'alert-danger')
				->with('message', 'Record does not exist.');
		}
		return View::make('holidays.edit', compact('holiday'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /holidays/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		
		$input['date'] = date('Y-m-d',strtotime(Input::get('date')));
		// Helper::print_array($input);
		$validation = Validator::make($input, Holiday::$rules);
		if ($validation->passes())
		{
			$holiday = Holiday::findOrFail($id);
			if (is_null($holiday))
			{
				return Redirect::route('holidays.index')
					->with('class', 'alert-danger')
					->with('message', 'Record does not exist.');
			}

			$hol = Holiday::where('date',date('Y-m-d',strtotime(Input::get('date'))))
				->where('id', '!=', $id)
				->first();

			if(count($hol)== 0){
				$holiday->description = strtoupper(Input::get('desc'));
				$holiday->date = date('Y-m-d',strtotime(Input::get('date')));
				$holiday->save();
			}else{
				$messages = array(
				    'exist' => 'The selected date already exist.',
				);
				return Redirect::route('holidays.edit', $id)
					->withInput()
					->withErrors($messages)
					->with('class', 'alert-danger')
					->with('message', 'There were validation errors.');
			}

			return Redirect::route('holidays.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly updated.');
		}

		return Redirect::route('holidays.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /holidays/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$holiday = Holiday::findOrFail($id)->delete();
		if (is_null($holiday))
		{
			$class = 'alert-danger';
			$message = 'Record does not exist.';
		}else{
			$class = 'alert-success';
			$message = 'Record successfully deleted.';
		}
		return Redirect::route('holidays.index')
				->with('class', $class )
				->with('message', $message);
	}

}