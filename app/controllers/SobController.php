<?php

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;



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
		set_time_limit(0);
		ini_set('memory_limit', -1);
		
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
				    'ALLOCATION SOB ID',
				    'PO NO',
				    'TOP CYCLE',
					'ACTIVITY TYPE',
					'ACTIVITY ID',
					'ACTIVITY NAME',
					'DIVISION',
					'CATEGORY',
					'BRAND',	
					'BRAND SHORTCUT',
					'SCHEME',	
					'ITEM CODE',	
					'ITEM DESCRIPTION',
					'GROUP',	
					'AREA',	
					'SOLD TO',
					'SHIP TO CODE',
					'CUSTOMER SHIP TO NAME',
					'ALLOCATION',	
					'WEEK #',
					'YEAR',
					'LOADING DATE',
					'RECEIVING DATE',
					'SOB ALLOCATION',
					'VALUE'
				));

			})->download('xls');

		});
	}

	public function download(){
		
		$types = AllocationSob::orderBy('activitytype_desc')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->join('activities', 'activities.id', '=', 'schemes.activity_id')
			->groupBy('activity_type_id')
			->lists('activitytype_desc', 'activity_type_id');

		$brands = AllocationSob::selectRaw('CONCAT(brand_desc, " - ", brand_shortcut) AS brand_desc, brand_shortcut')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->groupBy('brand_shortcut')
			->lists('brand_desc', 'brand_shortcut');


		$years = AllocationSob::select('year')
			->groupBy('year')
			->orderBy('year')
			->lists('year', 'year');

		$weeks = AllocationSob::select('weekno')
			->groupBy('weekno')
			->orderBy('weekno')
			->lists('weekno', 'weekno');


		return View::make('sob.download',compact('types', 'brands', 'years', 'weeks'));
	}

	public function downloadreport(){
		$input = Input::all();
		$rules = array(
	        'filename' => 'required|between:4,128',
	        'type' => 'required|integer|min:1',
	        'brand' => 'required',
	        'year' => 'required',
	        'week' => 'required'
	    );
		$validation = Validator::make($input,$rules);

		if($validation->passes())
		{	
			set_time_limit(0);
			ini_set('memory_limit', -1);

			$soldtos =  AllocationSob::getSOBFilters($input);

			$data = '';
			$cnt = 0;
			$last_po ='';
			foreach ($soldtos as $soldto)
			{
				if($soldto->allocation > 0){
					if($last_po != $soldto->po_no){
						$last_po = $soldto->po_no;
						$cnt++;
					}
					$soldto->row_no = $cnt;
					// $data .= '"'.$email.'"'.PHP_EOL;
					$data .= $soldto->row_no . chr(9)
						.$soldto->col2 . chr(9)
						.$soldto->col3 . chr(9)
						.$soldto->col4 . chr(9)
						.$soldto->ship_to_code_1 . chr(9)
						.$soldto->ship_to_code_2 . chr(9)
						.$soldto->col7 . chr(9)
						.$soldto->col8 . chr(9)
						.$soldto->po_no . chr(9)
						.$soldto->currentdate . chr(9)
						.$soldto->col11 . chr(9)
						.$soldto->col12 . chr(9)
						.$soldto->col13 . chr(9)
						.$soldto->col14 . chr(9)
						.$soldto->col15 . chr(9)
						.$soldto->col16 . chr(9)
						.$soldto->item_code . chr(9)
						.$soldto->allocation . chr(9)
						.$soldto->col19 . chr(9)
						.$soldto->col20 . chr(9)
						.$soldto->col21 . chr(9)
						.$soldto->col22 . chr(9)
						.$soldto->col23 . chr(9)
						.$soldto->deliverydate . chr(9)
						.$soldto->col25 . chr(9)
						.$soldto->col26 . chr(9)
						.$soldto->col27 . chr(9)
						.$soldto->col28 . chr(9)
						.$soldto->col29 . chr(9)
						.$soldto->col30 . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.'' . chr(9)
						.PHP_EOL;

		            // $csv->insertOne((array)$soldto);
				}
			    
			}

			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename='.Input::get('filename').'.txt');
			header('Pragma: no-cache');
			header('Expires: 0');

			echo $data;

			// $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
			// $cnt = 0;
			// $last_po ='';
			// foreach ($soldtos as $soldto) {
			// 	if($soldto->allocation > 0){
			// 		if($last_po != $soldto->po_no){
			// 			$last_po = $soldto->po_no;
			// 			$cnt++;
			// 		}
			// 		$soldto->row_no = $cnt;

		 //            $csv->insertOne((array)$soldto);
			// 	}
	  //       }
	  //       $csv->output(Input::get('filename').'.txt');


		}else{
			return Redirect::route('sob.download')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
		}
	}
}