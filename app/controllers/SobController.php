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
					'LPBT / PC',
					'PC / CS',
					'GROUP',	
					'AREA',	
					'SOLD TO',
					'SHIP TO CODE',
					'CUSTOMER SHIP TO NAME',
					'SOLD TO ALLOCATION',
					'SHIP TO ALLOCATION',	
					'WEEK #',
					'YEAR',
					'LOADING DATE',
					'RECEIVING DATE',
					'SOB ALLOCATION QTY',
					'SOB ALLOCATION VALUE',
					'DATE CREATED',
					'DATE UPDATED'
				));

			})->download('xls');

		});
	}

	public function download(){

		$years = AllocationSob::select('year')
			->groupBy('year')
			->orderBy('year')
			->lists('year', 'year');
		$exporttypes = ['1' => 'Without PO Generated','2' => 'With PO Generated'];


		return View::make('sob.download',compact('years', 'exporttypes'));
	}

	public function downloadreport(){
		$input = Input::all();
		$type_id = Input::get('activity_type');
		$year_id = Input::get('year');
		$week_id = Input::get('week');
		$brand_id = Input::get('brand');
		$exporttype = Input::get('exporttype');
		$rules = array(
	        'year' => 'required|integer|min:1',
	        'week' => 'required',
	        'activity_type' => 'required',
	        'brand' => 'required'
	    );

		$validation = Validator::make($input,$rules);

		if($validation->passes())
		{	
			set_time_limit(0);
			ini_set('memory_limit', -1);

			$activity_type = ActivityType::find($type_id);
			$brand_shortcut = $brand_id;
			$year = $year_id;
			$week = $week_id;

			$so_series = So::find(1);
			

			if($so_series->locked){
				return Redirect::route('sob.download')
					->withInput()
					->withErrors($validation)
					->with('class', 'alert-danger')
					->with('message', 'Cannot run simultaneous SOB PO generation.');
			}else{
				$hash = null;
				if($exporttype == 1){
					DB::beginTransaction();
					try {
						$hash = md5(date('Y-m-d H:i:s'));
						$sobs = AllocationSob::join('allocations', 'allocations.id', '=', 'allocation_sobs.allocation_id')
							->join('schemes', 'schemes.id', '=', 'allocations.scheme_id')
							->join('activities', 'activities.id', '=', 'schemes.activity_id')
							->where('weekno', $week)
							->where('year',$year)
							->where('brand_shortcut', $brand_shortcut)
							->where('activities.activity_type_id', $activity_type->id)
							->where('activities.status_id', 9)
							->where('activities.disable', 0)
							->whereNull('po_no')
							->groupBy('allocation_sobs.ship_to_code')
							->orderBy('allocation_sobs.id')
							->get();

						
						$so_series->locked = true;
						$so_series->update();

						$series = $so_series->series;
						foreach ($sobs as $sob) {
							$po_series = $activity_type->prefix ."_". $brand_shortcut .substr($year,2).str_pad($week,2, '0', STR_PAD_LEFT).sprintf("%05d", $series);

							Scheme::where('id',$sob->scheme_id)
								->update(['processed' => 1]);

							$shipTo = ShipTo::where('ship_to_code',$sob->ship_to_code)->first();
							$day_of_week = 0;
							if($activity_type->default_loading == 2){
								for ($i=1; $i < 8 ; $i++) { 
									$day_of_week = $i;
									if($shipTo->getDayOfWeek($i)){
										break;
									}
								}
							}else{
								for ($i=7; $i > 0 ; $i--) { 
									$day_of_week = $i;
									if($shipTo->getDayOfWeek($i)){
										break;
									}
								}
							}
							
							$day_of_week = $day_of_week - 1;

							$week_start = new DateTime();
							$week_start->setISODate($year,$week,$day_of_week);
							$loading_date = $week_start->format('Y-m-d');
							$receipt_date = date('Y-m-d', strtotime($loading_date . '+ '.$shipTo->leadtime.' days'));

							AllocationSob::join('allocations', 'allocations.id', '=', 'allocation_sobs.allocation_id')
								->join('schemes', 'schemes.id', '=', 'allocations.scheme_id')
								->join('activities', 'activities.id', '=', 'schemes.activity_id')
								->where('activities.status_id', 9)
								->where('activities.disable', 0)
								->where('weekno', $week)
								->where('year', $year)
								->where('allocation_sobs.ship_to_code',$sob->ship_to_code)
								->where('brand_shortcut', $brand_shortcut)
								->update(['po_no' => $po_series, 'loading_date' => $loading_date,
								 'receipt_date' => $receipt_date, 'allocation_sobs.updated_at' => date('Y-m-d h:i:s'),
								 'hash' => $hash]);
							$series++;
							if($series == 99999){
								$series = 1;
							}
						}

						$so_series->series = $series;
						$so_series->locked = false;
						$so_series->update();

						DB::commit();
					} catch (Exception $e) {
						DB::rollback();
						dd($e);
					}
				}
								
				$soldtos =  AllocationSob::getSOBFilters($input,$hash);

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
						$data .= $soldto->row_no . chr(9)
							.$soldto->col2 . chr(9)
							.$soldto->col3 . chr(9)
							.$soldto->col4 . chr(9)
							.$soldto->sob_customer_code . chr(9)
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
					}
				    
				}
				$filename = $year.'_'.$week.'_'.$activity_type->prefix."_". $brand_shortcut;
				header('Content-type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.$filename.'.txt');
				header('Pragma: no-cache');
				header('Expires: 0');

				echo $data;
			}
		}else{
			return Redirect::route('sob.download')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('week_id', $week_id)
			->with('message', 'There were validation errors.');
		}
	}
}