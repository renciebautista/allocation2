<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateLe extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:letemplate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
		$activity = Activity::find($id);
		if(!empty($activity)){
			$tradedeal = Tradedeal::getActivityTradeDeal($activity);
			if(!empty($tradedeal)){
				$schemes = TradedealScheme::where('tradedeal_id', $tradedeal->id)->get();
				if(!empty($schemes)){
					File::deleteDirectory(storage_path('le/'.$activity->id));
					foreach ($schemes as $scheme) {
						LeTemplateRepository::generateTemplate($scheme);
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
