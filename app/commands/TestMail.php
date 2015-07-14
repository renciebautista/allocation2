<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TestMail extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'test:mail';

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
		$user = User::find(1);
		$cycles = Cycle::getBySubmissionDeadline();
		$cycle_ids = array();
		foreach ($cycles as $value) {
			$cycle_ids[] = $value->id;
		}
		$data['cycles'] = $cycles;
		$data['user'] = $user->getFullname();
		$data['email'] = $user->email;
		$data['fullname'] = $user->getFullname();
		$data['cycle_ids'] = $cycle_ids;
		$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$cycle_ids);
		Mail::send('emails.mail1', $data, function($message) use ($data){
			$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
		});

		$this->line("Mail sent.");
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
