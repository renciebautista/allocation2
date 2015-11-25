<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateScheme extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'update:scheme';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update activity scheme';

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
		$id = $this->argument('id');
		$this->line("Updating scheme allocation via command line");
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		// echo $id;
		$scheme = Scheme::find($id);
		if(!empty($scheme)){
			if($scheme->compute == 1){
				$this->line("Compute");
				$skulist = SchemeSku::select('sku')
					->where('scheme_id',$scheme->id)->get();

				$skus = array();
				foreach ($skulist as $list) {
					$skus[] = $list->sku;
				}
				SchemeAllocRepository::updateAllocation($skus,$scheme);

				// update final alloc
				$final_alloc = SchemeAllocation::finalallocation($scheme->id);
				$total_cases = 0;
				$total_deals = 0;
				if($scheme->activity->activitytype->uom == 'CASES'){
					$total_deals = $final_alloc * $scheme->deals;
					$total_cases = $final_alloc;
					$final_tts = $final_alloc * $scheme->deals * $scheme->srp_p; 
				}else{
					
					if($final_alloc > 0){
						$total_cases = round($final_alloc/$scheme->deals);
						$total_deals = $final_alloc;
					}
					$final_tts = $final_alloc * $scheme->srp_p; 
				}
				
				$final_pe = $total_deals *  $scheme->other_cost;
				
				$scheme->final_alloc = $final_alloc;
				$scheme->final_total_deals = $total_deals;
				$scheme->final_total_cases = $total_cases;

				$per = 0;
				if($scheme->ulp_premium != ""){
					$scheme->final_tts_r = 0;
					$non = $scheme->srp_p * $total_deals;
					$per = $total_deals * $other_cost;
					$scheme->final_pe_r = $non+$per;
				}else{
					$scheme->final_tts_r = $final_tts;
					$scheme->final_pe_r = $final_pe;
				}
				
				$scheme->final_total_cost = $scheme->final_tts_r+$scheme->final_pe_r;
				$scheme->updating = 0;
				$scheme->update();
			}
			

			SchemeAllocRepository::updateCosting($scheme);

			$timeSecond = strtotime(date('Y-m-d H:i:s'));
			$differenceInSeconds = $timeSecond - $timeFirst;
			$this->line( 'Time used ' . $differenceInSeconds . " sec");
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
			array('id', InputArgument::REQUIRED, 'An example argument.'),
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
