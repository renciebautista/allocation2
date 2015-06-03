<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ScheduleActivity extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'schedule:activity';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Queue all approved activity for PDF creation.';

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
		$this->line("Scheduling approved activity.");
		$this->line(Carbon::now());
		$activities = Activity::select('activities.id', 'activities.circular_name')
			->join('cycles', 'cycles.id', '=', 'activities.cycle_id')
			// ->where('cycles.submission_deadline',date('Y-m-d'))
			// ->where('status_id',8)
			->where('pdf',0)
			->where('scheduled',0)
			->get();
		foreach ($activities as $activity->id) {
			$job_id = Queue::push('Scheduler', array('string' => "Scheduling ".$activity->circular_name, 'id' => $activity->id));
			Job::create(array('job_id' => $job_id));

			$this->line("Scheduling ".$activity->circular_name);
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
