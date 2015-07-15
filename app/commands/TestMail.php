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
		$type = $this->argument('type');
		$user_id = $this->argument('user_id');
		$user = User::find($user_id);
		switch ($type) {
			case 'mail1':
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
				if($_ENV['MAIL_TEST']){
					Mail::send('emails.mail1', $data, function($message) use ($data){
						$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
					});	
				}else{

				}
			break;
			
			default:
				# code...
				break;
		}
		// $cycles = Cycle::getBySubmissionDeadline();
		// $cycle_ids = array();
		// foreach ($cycles as $value) {
		// 	$cycle_ids[] = $value->id;
		// }
		// $data['cycles'] = $cycles;
		// $data['user'] = $user->getFullname();
		// $data['email'] = $user->email;
		// $data['fullname'] = $user->getFullname();
		// $data['cycle_ids'] = $cycle_ids;
		// $data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$cycle_ids);
		// Mail::send('emails.mail1', $data, function($message) use ($data){
		// 	$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
		// });
		$this->line("Mail sent.");
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
			array('user_id', InputArgument::REQUIRED, 'An example argument.'),
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
