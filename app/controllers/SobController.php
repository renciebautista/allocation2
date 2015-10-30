<?php


class SobController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /sob
	 *
	 * @return Response
	 */
	public function index()
	{
		$cycles = Activity::select('cycle_desc','cycle_id')
			->groupBy('cycle_id')
			->orderBy('cycle_desc')
			->lists('cycle_desc', 'cycle_id');
		return View::make('sob.index', compact('cycles'));
	}

	public function generate(){
		// dd(Input::all());
		// $soballocations = 

		$cycles = Input::get('cy');
		$fileName = Input::get('desc');
		$allocations = AllocationSob::getByCycle($cycles);
		$data = array();
		foreach ($allocations as $result) {
			if(isset($result->value)){
				$result->value = (double) $result->value;
			}
		   	$data[] = (array)$result;  
		   #or first convert it and then change its properties using 
		   #an array syntax, it's up to you
		}

		// dd($data);
		
		Excel::create($fileName, function($excel) use($data){
			$excel->sheet('SOB Allocation', function($sheet) use($data) {
				$sheet->fromArray($data,null, 'A1', true);
				$sheet->row(1, array(
				    'ID',
					'ACTIVITY TYPE',
					'CATEGORY',
					'BRAND',	
					'SCHEME',	
					'ITEM CODE',	
					'ITEM DESCRIPTION',
					'GROUP',	
					'AREA',	
					'SOLD TO',
					'SHIP TO CODE',
					'CUSTOMER SHIP TO NAME',	
					'WEEK #',
					'YEAR',
					'ALLOCATION',
					'VALUE'
				));

			})->download('xls');

		});
	}

	public function weekly(){
		$weeks = AllocationSob::select('weekno')
			->groupBy('weekno')
			->orderBy('weekno')
			->lists('weekno', 'weekno');
		$years = AllocationSob::select('year')
			->groupBy('year')
			->orderBy('year')
			->lists('year', 'year');
		return View::make('sob.weekly',compact('years', 'weeks'));
	}

	public function generateweekly(){
		$input = Input::all();
		$rules = array(
	        'filename' => 'required|between:4,128',
	        'year' => 'required|integer|min:1',
	        'week' => 'required|integer|min:1'
	    );
		$validation = Validator::make($input,$rules);

		if($validation->passes())
		{
			SobForm::download(Input::get('year'),Input::get('week'), Input::get('filename'));
		}else{
			return Redirect::route('sob.weekly')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
		}

		

		
	}

}