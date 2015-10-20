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
					'GROUP',	
					'AREA',	
					'SOLD TO',
					'SHIP TO CODE',
					'CUSTOMER SHIP TO NAME',	
					'WEEK #',
					'ALLOCATION'
				));

			})->download('xls');

		});
	}

}