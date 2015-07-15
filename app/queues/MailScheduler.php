<?php

class MailScheduler{
	public function fire($job, $data){
		$job_id = $job->getJobId(); // Get job id

		$ejob = Job::where('job_id',$job_id)->first(); // Find the job in database

		$ejob->status = 'running'; //Set job status to running

		$ejob->save();
		// $user = User::find($data['id']);
		// if($user->role_id == 2){
		// 	$data['activities'] = Activity::ProponentActivitiesForApproval($user->id,$data['cycle_ids']);
		// }
		// if($user->role_id == 3){
		// 	$data['activities'] = Activity::PmogActivitiesForApproval($user->id,$data['cycle_ids']);
		// }

		// Mail::send($data['template'], $data, function($message) use ($data){
		// 	$message->to("rbautista@chasetech.com", $data['fullname'])->subject('TOP ACTIVITY STATUS');
		// });

		$ejob->status = 'finished'; //Set job status to finished

		$ejob->save();
		
		$job->delete();
		return true;

	}
}