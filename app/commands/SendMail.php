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
				foreach ($users as $user) {
					$data['cycles'] = $cycles;
					$data['user'] = $user->getFullname();
					$data['email'] = $user->email;
					$data['fullname'] = $user->getFullname();
					$data['cycle_ids'] = $cycle_ids;
					if($user->role_id == 2){
						$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$cycle_ids);
					}
					if($user->role_id == 3){
						$data['activities'] = Activity::PmogActivitiesForApproval($user->id,$cycle_ids);
					}
					if($_ENV['MAIL_TEST']){
						Mail::send('emails.mail1', $data, function($message) use ($data){
							$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
						});
					}else{
						// Mail::send('emails.mail1', $data, function($message) use ($data){
						// 	$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITY STATUS');
						// });
					}
						
					// if(count($data['activities']) > 0){
					// 	$total_mails++;
					// 	if($_ENV['MAIL_TEST']){
					// 		Mail::queue('emails.mail1', $data, function($message) use ($data){
					// 			$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
					// 		});
					// 	}else{
					// 		// Mail::send('emails.mail1', $data, function($message) use ($data){
					// 		// 	$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITY STATUS');
					// 		// });
					// 	}
						
					// }
					
				}
				$total_users = count($users);
				$this->line("Total users {$total_users}");
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
