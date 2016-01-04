<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;



use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;


class MakeAllocReport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:allocreport';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create allocation report.';

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
		$id = $this->argument('temp_id');
		$user_id = $this->argument('user_id');
		$cycles = $this->argument('cycles');
		$fileName = $this->argument('name');
		
		$user = User::find($user_id);
		$template = AllocationReportTemplate::findOrFail($id);
		$headers = AllocSchemeField::getFields($template->id);

		$this->line('Template name ' . $template->name);

		$data['cycles'] = explode(",", $cycles);
		$data['status'] = AllocationReportFilter::getList($template->id,1);
		$data['scopes'] = AllocationReportFilter::getList($template->id,2);
		$data['proponents'] = AllocationReportFilter::getList($template->id,3);
		$data['planners'] = AllocationReportFilter::getList($template->id,4);
		$data['approvers'] = AllocationReportFilter::getList($template->id,5);
		$data['activitytypes'] = AllocationReportFilter::getList($template->id,6);
		$data['divisions'] = AllocationReportFilter::getList($template->id,7);
		$data['categories'] = AllocationReportFilter::getList($template->id,8);
		$data['brands'] = AllocationReportFilter::getList($template->id,9);
		$data['customers'] = AllocationReportFilter::getList($template->id,10);
		$data['outlets'] = AllocationReportFilter::getList($template->id,11);
		$data['channels'] = AllocationReportFilter::getList($template->id,12);
		$data['fields'] = $headers;
		$token = md5(uniqid(mt_rand(), true));
		
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		$filePath = storage_path('exports/'.$token.'.xlsx');
		$writer = WriterFactory::create(Type::XLSX);
		$writer->setShouldCreateNewSheetsAutomatically(true); // default value
		$writer->openToFile($filePath); // write data to a file or to a PHP stream
		$take = 1000; // adjust this however you choose
		$counter = 0; // used to skip over the ones you've already processed

		
		$header = array();
		foreach ($headers as $value) {
			$header[] = $value->desc_name;
		}
		
		$writer->addRow($header); // add multiple rows at a time
		while($rows = AllocationReport::getReport($data,$take,$counter,$user))
		{
			if(count($rows) == 0){
				break;
			}
			$counter += $take;
			foreach($rows as $key => $value)
			{
				if(isset($value->cost_of_premium)){
					$value->cost_of_premium = (double) $value->cost_of_premium;
				}
				if(isset($value->other_cost_per_deal)){
					$value->other_cost_per_deal = (double) $value->other_cost_per_deal;
				}
				if(isset($value->purchase_requirement)){
					$value->purchase_requirement = (double) $value->purchase_requirement;
				}
				if(isset($value->total_unilever_cost)){
					$value->total_unilever_cost = (double) $value->total_unilever_cost;
				}

				if(isset($value->list_price_after_tax)){
					$value->list_price_after_tax = (double) $value->list_price_after_tax;
				}
				if(isset($value->list_price_before_tax)){
					$value->list_price_before_tax = (double) $value->list_price_before_tax;
				}
				if(isset($value->cost_to_sale)){
					$value->cost_to_sale = (double) $value->cost_to_sale;
				}
				if(isset($value->sold_to_sales)){
					$value->sold_to_sales = (double) $value->sold_to_sales;
				}

				if(isset($value->sold_to_sales_p)){
					$value->sold_to_sales_p = (double) $value->sold_to_sales_p;
				}
				if(isset($value->ship_to_sales)){
					$value->ship_to_sales = (double) $value->ship_to_sales;
				}
				if(isset($value->ship_to_sales_p)){
					$value->ship_to_sales_p = (double) $value->ship_to_sales_p;
				}
				if(isset($value->outlet_sales)){
					$value->outlet_sales = (double) $value->outlet_sales;
				}

				if(isset($value->outlet_sales_p)){
					$value->outlet_sales_p = (double) $value->outlet_sales_p;
				}
				
				if(isset($value->tts_requirement)){
					$value->tts_requirement = (double) $value->tts_requirement;
				}
				if(isset($value->pe_requirement)){
					$value->pe_requirement = (double) $value->pe_requirement;
				}

				if(isset($value->total_cost)){
					$value->total_cost = (double) $value->total_cost;
				}

				$rows[$key] = (array) $value;
			} 
			$export_data = $rows;
			// var_dump($export_data);
			$writer->addRows($export_data); // add multiple rows at a time
		}
		$writer->close();
		$timeSecond = strtotime(date('Y-m-d H:i:s'));
		$differenceInSeconds = $timeSecond - $timeFirst;
		$this->line( 'Time used ' . $differenceInSeconds . " sec");

		
		$newfile = new AllocationReportFile;
		$newfile->created_by = $user->id;
		$newfile->template_id = $template->id;
		$newfile->token = $token;
		$newfile->file_name = $token.'.xlsx';
		$newfile->template_name = $fileName.'.xlsx';
		$newfile->save();
		
		// $template->token = $token;
		// $template->token = $token;
		// $template->file_name = $token.'.xlsx';
		// $template->template_name = $template->name.'_'.date('Y_m_d').'.xlsx';
		// $template->report_generated = date('Y-m-d H:i:s');
		// $template->update();


		$data['template'] = $template;
		$data['token'] = $token;
		$data['user'] = $user;
		$data['name'] = $fileName;
		
		$this->line($template->file_name);

		if($_ENV['MAIL_TEST']){
			Mail::send('emails.allocreport', $data, function($message) use ($user, $template, $fileName){
				$message->to("rbautista@chasetech.com", $user->first_name);
				$message->bcc("grace.erum@unilever.com");
				$message->subject('Allocation Report - '.$fileName);
			});	
		}else{
			Mail::send('emails.allocreport', $data, function($message) use ($user, $template, $fileName){
				$message->to($user->email, $user->first_name);
				$message->subject('Allocation Report - '.$$fileName);
			});	
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
			array('temp_id', InputArgument::REQUIRED, 'Template Id'),
			array('user_id', InputArgument::REQUIRED, 'User Id'),
			array('cycles', InputArgument::REQUIRED, 'Selected Cycles'),
			array('name', InputArgument::REQUIRED, 'Template Name'),
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
