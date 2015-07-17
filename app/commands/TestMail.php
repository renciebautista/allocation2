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
		$role_id = $this->argument('role_id');
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
				if($role_id == 2){
					$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$cycle_ids);
				}
				if($role_id == 3){
					$data['activities'] = Activity::PmogActivitiesForApproval($user->id,$cycle_ids);
				}
				if(count($data['activities'])>0){
					
					if($_ENV['MAIL_TEST']){
						Mail::send('emails.mail1', $data, function($message) use ($data){
							$message->to("rbautista@chasetech.com", $data['fullname']);
							$message->bcc("rosarah.reyes@unilever.com");
							$message->subject('TOP ACTIVITY STATUS');
						});
					}else{
						Mail::send('emails.mail1', $data, function($message) use ($data){
							$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITY STATUS');
						});
					}
				}
			break;

			case 'mail2':
				$cycles = Cycle::getByApprovalDeadline();
				$cycle_ids = array();
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
				}
				$data['cycles'] = $cycles;
				$data['user'] = $user->getFullname();
				$data['email'] = $user->email;
				$data['fullname'] = $user->getFullname();
				$data['cycle_ids'] = $cycle_ids;
				$data['activities'] = Activity::ApproverActivitiesForApproval($user->id,$cycle_ids);
				if(count($data['activities'])>0){
					if($_ENV['MAIL_TEST']){
						Mail::send('emails.mail2', $data, function($message) use ($data){
							$message->to("rbautista@chasetech.com", $data['fullname']);
							$message->bcc("rosarah.reyes@unilever.com");
							$message->subject('FOR APPROVAL: TOP ACTIVITIES');
						});	
					}else{
						Mail::send('emails.mail2', $data, function($message) use ($data){
							$message->to($data['email'], $data['fullname'])->subject('FOR APPROVAL: TOP ACTIVITIES');
						});
					}
				}
			break;

			case 'mail3':
				$cycles = Cycle::getByApprovalDeadlinePassed();
				$cycle_ids = array();
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
				}
				$data['cycles'] = $cycles;
				$data['user'] = $user->getFullname();
				$data['email'] = $user->email;
				$data['fullname'] = $user->getFullname();
				$data['cycle_ids'] = $cycle_ids;
				if($role_id == 2){
					$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$cycle_ids);
				}
				if($role_id == 3){
					$data['activities'] = Activity::PmogActivitiesForApproval($user->id,$cycle_ids);
				}
				if($role_id  > 3){
					$data['activities'] = Activity::ApproverActivities($user->id,$cycle_ids);
				}
				if(count($data['activities'])>0){
					if($_ENV['MAIL_TEST']){
						Mail::send('emails.mail3', $data, function($message) use ($data){
							$message->to("rbautista@chasetech.com", $data['fullname']);
							$message->bcc("rosarah.reyes@unilever.com");
							$message->subject('TOP ACTIVITY STATUS');
						});	
					}else{
						Mail::send('emails.mail3', $data, function($message) use ($data){
							$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITY STATUS');
						});
					}
				}
			break;

			case 'mail4':
				$cycles = Cycle::getByReleaseDate();
				$cycle_ids = array();
				$cycle_names = "";
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
					$cycle_names .= $value->cycle_name ." - ";
				}
				$data['cycles'] = $cycles;
				$data['user'] = $user->getFullname();
				$data['email'] = $user->email;
				$data['fullname'] = $user->getFullname();
				$data['cycle_ids'] = $cycle_ids;
				$data['cycle_names'] = $cycle_names;
				
				$data['activities'] = Activity::Released($cycle_ids);

				if(count($data['activities'])>0){
					if($_ENV['MAIL_TEST']){
						Mail::send('emails.mail4', $data, function($message) use ($data){
							$message->to("rbautista@chasetech.com", $data['fullname']);
							$message->bcc("rosarah.reyes@unilever.com");
							$message->subject('TOP ACTIVITIES FOR: ('.$data['cycle_names'].')');
						});	
					}else{
						Mail::send('emails.mail4', $data, function($message) use ($data){
							$message->to($data['email'], $data['fullname'])->subject('TOP ACTIVITIES FOR: ('.$data['cycle_names'].')');
						});
					}
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
			array('role_id', InputArgument::REQUIRED, 'An example argument.'),
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
