<?php

class MailScheduler{
	public function fire($job, $data){

		$user = User::find($data['user_id']);
		$cycles = Cycle::whereIn('id',$data['cycles']);
		$data['cycles'] = $cycles;
		$data['user'] = $user->getFullname();
		$data['email'] = $user->email;
		$data['fullname'] = $user->getFullname();
		$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$data['cycles']);

		Mail::send('emails.mail1', $data, function($message) use ($data){
			$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
		});
		
		$job->delete();
		return true;

	}
}