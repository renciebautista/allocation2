<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeWord extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:word';

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
		set_time_limit(0);
		$id = $this->argument('id');
		$this->line("Generating Word via comamnd line using PHPWord");
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		// echo $id;
		$activity = Activity::find($id);
		if(!empty($activity)){
			$worddoc = new WordDoc($activity->id);
			$pdf_name = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
			$filepath = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			$word_path = storage_path().$filepath.'/'. str_replace(":","_", $pdf_name).'.docx';
			$worddoc->save($word_path);

			$this->line($word_path);
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
