<?php

class SobholidaysController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /sobholidays
	 *
	 * @return Response
	 */
	public function index()
	{
		$holidays = SobHoliday::all();
		return View::make('sobholiday.index',compact('holidays'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /sobholidays/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$shiptos = ShipTo::getAllShipTo();
		return View::make('sobholiday.create',compact('shiptos'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /sobholidays
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		
		$input['date'] = date('Y-m-d',strtotime(Input::get('date')));
		$validation = Validator::make($input, SobHoliday::$rules);

		if($validation->passes())
		{
			$hol = SobHoliday::where('date',date('Y-m-d',strtotime(Input::get('date'))))->first();
			if(count($hol)== 0){
				$holiday = new SobHoliday();
				$holiday->description = strtoupper(Input::get('desc'));
				$holiday->date = date('Y-m-d',strtotime(Input::get('date')));
				$holiday->save();

				$data = [];
				foreach (Input::get('shiptos') as $key => $value) {
					$data[] = ['sob_holiday_id' => $holiday->id, 'ship_to_code' => $value];	
				}

				if(!empty($data)){
					ShipHoliday::insert($data);
				}

				return Redirect::route('sobholiday.index')
					->with('class', 'alert-success')
					->with('message', 'Ship To Holiday successfuly created.');
			}else{
				$messages = array(
				    'exist' => 'The selected date already exist.',
				);
				return Redirect::route('sobholiday.create')
					->withInput()
					->withErrors($messages)
					->with('class', 'alert-danger')
					->with('message', 'There were validation errors.');
			}
		}

		return Redirect::route('sobholiday.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /sobholidays/{id}
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
	 * GET /sobholidays/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$holiday = SobHoliday::findOrFail($id);
		$shiptos = ShipTo::getAllShipTo();
		$selected = ShipHoliday::getSelected($holiday->id);
		return View::make('sobholiday.edit',compact('shiptos', 'holiday', 'selected'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /sobholidays/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		
		$input['date'] = date('Y-m-d',strtotime(Input::get('date')));
		$validation = Validator::make($input, SobHoliday::$rules);
		if ($validation->passes())
		{
			$holiday = SobHoliday::findOrFail($id);
			if (is_null($holiday))
			{
				return Redirect::route('sobholiday.index')
					->with('class', 'alert-danger')
					->with('message', 'Record does not exist.');
			}

			$hol = SobHoliday::where('date',date('Y-m-d',strtotime(Input::get('date'))))
				->where('id', '!=', $id)
				->first();

			if(count($hol)== 0){

				ShipHoliday::where('sob_holiday_id',$holiday->id)->delete();

				$holiday->description = strtoupper(Input::get('desc'));
				$holiday->date = date('Y-m-d',strtotime(Input::get('date')));
				$holiday->save();

				$data = [];
				if(Input::has('shiptos')){
					foreach (Input::get('shiptos') as $key => $value) {
						$data[] = ['sob_holiday_id' => $holiday->id, 'ship_to_code' => $value];	
					}
				}
				

				if(!empty($data)){
					ShipHoliday::insert($data);
				}
			}else{
				$messages = array(
				    'exist' => 'The selected date already exist.',
				);
				return Redirect::route('sobholiday.edit', $id)
					->withInput()
					->withErrors($messages)
					->with('class', 'alert-danger')
					->with('message', 'There were validation errors.');
			}

			return Redirect::route('sobholiday.edit', $id)
				->with('class', 'alert-success')
				->with('message', 'Record successfuly updated.');
		}

		return Redirect::route('sobholiday.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /sobholidays/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}