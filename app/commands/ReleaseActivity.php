<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ReleaseActivity extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'release:activity';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send mail to all active users';

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
		$cycles = Cycle::getByReleaseDate();
		$cycle_ids = array();
		$cycle_names = "";
		foreach ($cycles as $value) {
			$cycle_ids[] = $value->id;
		}

		$data['cycle_ids'] = $cycle_ids;
		$data['activities'] = Activity::Released($cycle_ids);
		$data['cycles'] = Activity::ReleasedCyles($cycle_ids);
		foreach ($data['cycles'] as $value) {
			$cycle_names .= $value->cycle_name ." - ";
		}
		$data['cycle_names'] = substr($cycle_names, 0,-3);

		$users = User::GetPlanners(['PROPONENT' ,'PMOG PLANNER','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR','FIELD SALES']);
		$cnt = 0;
		if(count($cycles)>0){
			foreach ($users as $user) {
				$data['user'] = $user->first_name;
				$data['email'] = $user->email;
				$data['fullname'] = $user->getFullname();
		
				// Mail::send('emails.mail5', $data, function($message) use ($data){
				// 	$message->to("rbautista@chasetech.com", $data['fullname']);
				// 	// $message->bcc("rosarah.reyes@unilever.com");
				// 	$message->subject('TEST ACTIVITy');
				// });

				if(count($data['activities'])>0){
					if($_ENV['MAIL_TEST']){
						if(count($data['cycles']) > 1){
							Mail::send('emails.mail4', $data, function($message) use ($data){
								$message->to("rbautista@chasetech.com", $data['fullname']);
								// $message->bcc("rosarah.reyes@unilever.com");
								$message->subject('TOP ACTIVITIES FOR: ('.$data['cycle_names'].')');
							});	
						}else{
							Mail::send('emails.mail4', $data, function($message) use ($data){
								$message->to("rbautista@chasetech.com", $data['fullname']);
								// $message->bcc("rosarah.reyes@unilever.com");
								$message->subject('TOP ACTIVITIES FOR: '.$data['cycle_names']);
							});	
						}
					}else{
						if(count($data['cycles']) > 1){
							Mail::send('emails.mail4', $data, function($message) use ($data){
								$message->to(trim(strtolower($data['email'])), $data['fullname']);
								$message->subject('TOP ACTIVITIES FOR: ('.$data['cycle_names'].')');
							});
						}else{
							Mail::send('emails.mail4', $data, function($message) use ($data){
								$message->to(trim(strtolower($data['email'])), $data['fullname']);
								$message->subject('TOP ACTIVITIES FOR: '.$data['cycle_names']);
							});
						}
						
					}
				}

				$cnt++;
			}
		}
		

		$this->line('Total email sent:'. $cnt);
		
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
