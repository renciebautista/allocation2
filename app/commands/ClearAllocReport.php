<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ClearAllocReport extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'clear:allocreport';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear allocation report.';

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
		$files = File::allFiles(storage_path('exports'));
		foreach ($files as $file)
		{
		    // echo (string)$file, "\n";
		    $filecreated = filectime($file);
		    // echo  $filecreated, "\n";
		    $days = 86400 * 60  // 60 days
		    // $days = 60 * 1;
		    if((time() - $filecreated) > $days)
	        {
	           unlink($file);
	        }
		}
	}

	// /**
	//  * Get the console command arguments.
	//  *
	//  * @return array
	//  */
	// protected function getArguments()
	// {
	// 	return array(
	// 		array('example', InputArgument::REQUIRED, 'An example argument.'),
	// 	);
	// }

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
