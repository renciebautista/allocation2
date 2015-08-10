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
		$user = User::find($user_id);
		$template = AllocationReportTemplate::findOrFail($id);

		$this->line('Template name' . $template->name);

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
		$data['groups'] = AllocationReportFilter::getList($template->id,10);
		$data['areas'] = AllocationReportFilter::getList($template->id,11);
		$data['soldtos'] = AllocationReportFilter::getList($template->id,12);
		$data['shiptos'] = AllocationReportFilter::getList($template->id,13);
		$data['outlets'] = AllocationReportFilter::getList($template->id,14);

		$token = md5(uniqid(mt_rand(), true));
		
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		$filePath = storage_path('exports/'.$token.'.xlsx');
		$writer = WriterFactory::create(Type::XLSX);
		$writer->openToFile($filePath); // write data to a file or to a PHP stream
		$take = 1000; // adjust this however you choose
		$counter = 0; // used to skip over the ones you've already processed
		while($rows = AllocationReport::getReport($data,$take,$counter))
		{
			if(count($rows) == 0){
		    	break;
		    }
		    $counter += $take;
		    foreach($rows as $key => $value)
			{
			    $rows[$key] = (array) $value;
			} 
			$export_data = $rows;

		    $writer->addRows($export_data); // add multiple rows at a time
		}
		$writer->close();
		$timeSecond = strtotime(date('Y-m-d H:i:s'));
		$differenceInSeconds = $timeSecond - $timeFirst;
		$this->line( 'Time used ' . $differenceInSeconds . " sec");

		
		$newfile = new AllocationReportFile;
		$newfile->created_by = $user->id;
		$newfile->token = $token;
		$newfile->file_name = $token.'.xlsx';

		$data['template'] = $template;
    	$data['token'] = $token;
    	$data['user'] = $user;
    	Mail::send('emails.allocreport', $data, function($message) use ($data){
			$message->to($user->email, $user->first_name);
			$message->subject('Allocation Report - '.$template->name);
		});
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
