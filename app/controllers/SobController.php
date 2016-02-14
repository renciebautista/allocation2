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

	// for updating

	public function booking(){
		Input::flash();
		$weeks = AllocationSob::getWeeks();
		$years = AllocationSob::getYears();

		$types = AllocationSob::orderBy('activitytype_desc')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->join('activities', 'activities.id', '=', 'schemes.activity_id')
			->groupBy('activity_type_id')
			->lists('activitytype_desc', 'activity_type_id');

		$brands = AllocationSob::orderBy('brand_desc')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->groupBy('brand_desc')
			->lists('brand_desc', 'brand_code');

		$status = BookingStatus::lists('status', 'id');
		$bookings = [];
		$allow_download = false;
		return View::make('sob.booking',compact('status', 'weeks', 'years', 'types', 'brands', 'bookings', 'allow_download'));
	}

	public function filterbooking(){
		// dd(Input::all());
		if(Input::get('submit') == 'skulist'){
			$weeks = Input::get('wk');
			$year = Input::get('yr');

			if((count($weeks) == 1) && (count($year) == 1)){
				$skulist = AllocationSob::getSkuList($weeks[0],$year[0]);
				$writer = WriterFactory::create(Type::XLSX); 
	            $writer->openToBrowser($year[0].$weeks[0].' SKU Lisit.xlsx');
	            $writer->addRow(array('Week #', 'Year', 'Item Code', 'Item Description', 'Category', 'Brand', 'Available'));  

	            foreach ($skulist as $sku) {
	            	$data[0] = $weeks[0];
	                $data[1] = $year[0];
	                $data[2] = $sku->item_code;
	                $data[3] = $sku->item_desc;
	                $data[4] = $sku->categories;
	                $data[5] = $sku->brands;
	                $writer->addRow($data); 
	            }

	            $writer->close();
			}else{
				return Redirect::action('SobController@booking')
					->with('class', 'alert-danger')
					->with('message', 'Multiple selected week or year.');
			}
			
			
		}else{
			Input::flash();
			$allow_download = false;
			$weeks = Input::get('wk');
			$year = Input::get('yr');
			if((count($weeks) == 1) && (count($year) == 1)){
				$allow_download = true;
			}

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
				->lists('brand_desc', 'brand_code');

			$status = BookingStatus::lists('status', 'id');

			$bookings = AllocationSob::getWeekBooking(Input::get('ty'),Input::get('br'),Input::get('wk'),Input::get('yr'),Input::get('st'));
			return View::make('sob.booking',compact('status', 'weeks', 'years', 'types', 'brands', 'bookings', 'allow_download'));
		}
		
	}

	public function showbooking($week,$year,$brand_code,$type){
		$schemes = AllocationSob::select('schemes.id', 'schemes.item_code', 'schemes.item_desc', 'allocation_sobs.scheme_id', 'schemes.activity_id', 'schemes.brand_shortcut')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->join('activities', 'activities.id' , '=', 'schemes.activity_id')
			->where('weekno', $week)
			->where('year', $year)
			->where('brand_code', $brand_code)
			->where('activities.activity_type_id',$type)
			->where('activities.disable',0)
			// ->where('activities.status_id',9)
			->groupBy('scheme_id')
			->orderBy('allocation_sobs.scheme_id')
			->get();
// 

		$scheme_ids = array();
		foreach ($schemes as $value) {
			$scheme_ids[] = $value->scheme_id;
			$activity = Activity::find($value->activity_id);
		}


		$soldtos = AllocationSob::select('sold_to_code', 'sold_to', 'allocations.sob_customer_code', 'allocation_sobs.ship_to_code', 'ship_to', 
			DB::raw('sum(allocation_sobs.allocation) as allocations'),
			'allocation_sobs.loading_date', 'allocation_sobs.receipt_date' ,'allocation_sobs.year',
			'allocation_sobs.weekno', DB::raw('sum(allocation) as total_allocation'))
				->join('allocations', 'allocations.id', '=', 'allocation_sobs.allocation_id')
				->where('weekno', $week)
				->where('year', $year)
				->whereIn('allocation_sobs.scheme_id',$scheme_ids)
				->groupBy('allocation_sobs.ship_to_code')
				->orderBy('allocation_sobs.id')
				// ->orderBy('ship_to')
				->get();



		$brand = Pricelist::getBrandByCode($brand_code);
		$activitytype = ActivityType::find($type);

		$po = $activitytype->prefix.$brand->brand_shortcut."_".$year."_".$week;

		return View::make('sob.showbooking',compact('soldtos', 'schemes', 'po'));
	}

	public function downloadbooking(){
		SobForm::download(2016,10,8, 431332749, Auth::user(), 'sample');

	}
}