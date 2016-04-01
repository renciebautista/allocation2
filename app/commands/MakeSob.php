<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeSob extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:sob';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate SOB Allocation';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$cycles = $this->argument('cycle_ids');
		$cycle_ids = explode(",", $cycles);
		$activity_status = 9;

		$activities = Activity::with('activitytype')
			->where('activities.status_id',$activity_status)
			->where('activities.with_sob',1)
			->whereIn('activities.cycle_id',$cycle_ids)
			->groupBy('activity_type_id')
			->get();

		// $this->line('Total activities with : ' .count($cycle_ids));

		foreach ($activities as $activity) {
			$activity_schemes = Scheme::select('schemes.id')
				->join('activities', 'activities.id', '=','schemes.activity_id')
				->where('activities.status_id',$activity_status)
				->where('activities.with_sob', 1)
				->where('activities.activity_type_id', $activity->activity_type->id)
				->whereIn('activities.cycle_id',$cycle_ids)
				->get();

			// $this->line($activity_schemes->count());

			$scheme_ids = [];
			foreach ($activity_schemes as $key => $scheme) {
				$scheme_ids[] = $scheme->id;
			}

			$brands = Scheme::select('brand_desc', 'brand_shortcut')
				->whereIn('id',$scheme_ids)
				->groupBy('brand_desc')
				->get();
			foreach ($brands as $brand) {
				$splits = AllocationSob::select('weekno', 'year')
					->join('schemes', 'schemes.id', '=','allocation_sobs.scheme_id')
					->where('schemes.brand_desc', $brand->brand_desc)
					->whereIn('scheme_id',$scheme_ids)
					->groupBy('year', 'weekno')
					->orderBy('year', 'weekno')
					->get();
				foreach ($splits as $split) {
					$po_no = $activity->activitytype->prefix ."_". $brand->brand_shortcut ."_". $split->year ."_". sprintf("%02d", $split->weekno);
					$sobs = AllocationSob::join('allocations', 'allocations.id', '=', 'allocation_sobs.allocation_id')
						->join('schemes', 'schemes.id', '=', 'allocations.scheme_id')
						->where('weekno', $split->weekno)
						->where('year', $split->year)
						->where('brand_desc', $brand->brand_desc)
						->whereIn('allocation_sobs.scheme_id',$scheme_ids)
						->groupBy('allocation_sobs.ship_to_code')
						->orderBy('allocation_sobs.id')
						->get();
					$series = 1;
					foreach ($sobs as $sob) {
						$po_series = $po_no ."_". sprintf("%02d", $series);
						$this->line('PO SERIES : ' .$po_series);

						$shipTo = ShipTo::where('ship_to_code',$sob->ship_to_code)->first();

						$week_start = new DateTime();
						$week_start->setISODate($split->year,$split->weekno,$shipTo->dayofweek);
						$loading_date = $week_start->format('Y-m-d');
						$receipt_date = date('Y-m-d', strtotime($loading_date . '+ '.$shipTo->leadtime.' days'));

						AllocationSob::join('allocations', 'allocations.id', '=', 'allocation_sobs.allocation_id')
							->join('schemes', 'schemes.id', '=', 'allocations.scheme_id')
							->where('weekno', $split->weekno)
							->where('year', $split->year)
							->where('allocation_sobs.ship_to_code',$sob->ship_to_code)
							->where('brand_desc', $brand->brand_desc)
							->whereIn('allocation_sobs.scheme_id',$scheme_ids)
							->update(['po_no' => $po_series, 'loading_date' => $loading_date, 'receipt_date' => $receipt_date]);
						$series++;
					}
				}
			}
		}
		
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('cycle_ids', InputArgument::REQUIRED, 'Cycle ID/s'),
		);
	}

	// /**
	//  * Get the console command options.
	//  *
	//  * @return array
	//  */
	// protected function getOptions()
	// {
	// 	return array(
	// 		array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
	// 	);
	// }

}
