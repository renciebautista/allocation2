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
		$cycles = Input::get('cy');
		$fileName = Input::get('desc');
		$allocations = AllocationSob::getByCycle($cycles);
		$data = array();
		foreach ($allocations as $result) {
			if(isset($result->value)){
				$result->value = (double) $result->value;
			}
		   	$data[] = (array)$result;  
		}
		
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
					'LOADING DATE',
					'RECEIVING DATE',
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

		$types = AllocationSob::orderBy('activitytype_desc')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->join('activities', 'activities.id', '=', 'schemes.activity_id')
			->groupBy('activity_type_id')
			->lists('activitytype_desc', 'activity_type_id');

		$brands = AllocationSob::orderBy('brand_desc')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->groupBy('brand_desc')
			->lists('brand_desc', 'brand_desc');


		return View::make('sob.weekly',compact('years', 'weeks', 'types', 'brands'));
	}

	public function generateweekly(){
		$input = Input::all();
		$rules = array(
	        'filename' => 'required|between:4,128',
	        'type' => 'required|integer|min:1',
	        'year' => 'required|integer|min:1',
	        'week' => 'required|integer|min:1',
	        'brand' => 'required'
	    );
		$validation = Validator::make($input,$rules);

		if($validation->passes())
		{
			SobForm::download(Input::get('year'),Input::get('week'),Input::get('type'), Input::get('brand'), Auth::user(), Input::get('filename'));
		}else{
			return Redirect::route('sob.weekly')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
		}

		

		
	}

}