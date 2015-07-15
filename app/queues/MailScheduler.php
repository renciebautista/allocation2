<?php

class MailScheduler{
	public function fire($job, $data){

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
		Mail::send('emails.mail1', $data, function($message) {
			$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
		});

		//File::append(storage_path().'/queue.txt',$data['user_id'].$job_id.PHP_EOL); //Add content to file
		
		$job->delete();
		return true;

	}
}