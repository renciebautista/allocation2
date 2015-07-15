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
		$this->line("Queuing email for {$type}");
		$total_mails = 0;
		switch ($type) {
			case 'mail1':
				$users = User::GetPlanners(['PROPONENT' ,'PMOG']);
				$cycles = Cycle::getBySubmissionDeadline();
				$cycle_ids = array();
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
				}
				foreach ($users as $user) {
					if($user->role_id == 2){
						$data['activities'] = Activity::ProponentActivitiesForApproval($user->user_id,$cycle_ids);
					}
					if($user->role_id == 3){
						$data['activities'] = Activity::PmogActivitiesForApproval($user->user_id,$cycle_ids);
					}

					if(count($data['activities']) > 0){
						$total_mails++;
						Queue::push('MailScheduler', array('type' => $type, 'user_id' => $user->user_id, 'role_id' => $user->role_id));
					}
				}
				$total_users = count($users);
				$this->line("Total users {$total_users}");
				$this->line("Total queued email {$total_mails}");
			break;
			case 'mail2':
				$users = User::GetPlanners(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
				$cycles = Cycle::getByApprovalDeadline();
				$cycle_ids = array();
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
				}
				foreach ($users as $user) {
					$data['activities'] = Activity::ApproverActivitiesForApproval($user->user_id,$cycle_ids);
					if(count($data['activities']) > 0){
						$total_mails++;
						Queue::push('MailScheduler', array('type' => $type, 'user_id' => $user->user_id, 'role_id' => $user->role_id));
					}
				}
				$total_users = count($users);
				$this->line("Total users {$total_users}");
				$this->line("Total queued email {$total_mails}");
				break;
			case 'mail3':
				$users = User::GetPlanners(['PROPONENT' ,'PMOG','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
				$cycles = Cycle::getByApprovalDeadlinePassed();
				$cycle_ids = array();
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
				}
				foreach ($users as $user) {
					if($user->role_id == 2){
						$data['activities'] = Activity::ProponentActivitiesForApproval($user->user_id,$cycle_ids);
					}
					if($user->role_id == 3){
						$data['activities'] = Activity::PmogActivitiesForApproval($user->user_id,$cycle_ids);
					}
					if($user->role_id  > 3){
						$data['activities'] = Activity::ApproverActivities($user->user_id,$cycle_ids);
					}
					if(count($data['activities']) > 0){
						$total_mails++;
						Queue::push('MailScheduler', array('type' => $type, 'user_id' => $user->user_id, 'role_id' => $user->role_id));
					}
				}
				$total_users = count($users);
				$this->line("Total users {$total_users}");
				$this->line("Total queued email {$total_mails}");
				break;
			case 'mail4':
				$users = User::GetPlanners(['PROPONENT' ,'PMOG','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR','FIELD SALES']);
				$cycles = Cycle::getByReleaseDate();
				$cycle_ids = array();
				foreach ($cycles as $value) {
					$cycle_ids[] = $value->id;
				}
				foreach ($users as $user) {
					$total_mails++;
					Queue::push('MailScheduler', array('type' => $type, 'user_id' => $user->user_id, 'role_id' => $user->role_id));

					// $data['activities'] = Activity::Released($cycle_ids);
					// if(count($data['activities']) > 0){
					// 	$total_mails++;
					// 	Queue::push('MailScheduler', array('type' => $type, 'user_id' => $user->user_id, 'role_id' => $user->role_id));
					// }
				}
				$total_users = count($users);
				$this->line("Total users {$total_users}");
				$this->line("Total queued email {$total_mails}");
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
