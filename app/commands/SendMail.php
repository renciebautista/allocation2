<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendMail extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mail:queue';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Queue mail alerts';

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
		$type = $this->argument('type');
		switch ($type) {
			case 'mail1':
				$this->line("Queuing email for {$type}");
				$users = User::GetPlanners(['PROPONENT' ,'PMOG']);
				$cycles = Cycle::getBySubmissionDeadline();
				$cycle_ids = array();
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
				}
				$total_mails = 0;
				$total_users = count($users);
				$this->line("Total users {$total_users}");
				foreach ($users as $user) {
					$job_id = Queue::push('MailScheduler', array('template' => 'emails.mail1', 'id' => $user->id, 'cycle_ids' => $cycle_ids));
					Job::create(array('job_id' => $job_id));
					$this->line("Scheduling mail");
				}
				$this->line("Total queued email {$total_mails}");
			break;
			case 'mail2':
				$this->line("Queuing email for {$type}");
				break;
			case 'mail3':
				$this->line("Queuing email for {$type}");
				break;
			case 'mail4':
				$this->line("Queuing email for {$type}");
				break;
			default:
				$this->line("Not valid type.");
				break;
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
			array('type', InputArgument::REQUIRED, 'An example argument.'),
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
