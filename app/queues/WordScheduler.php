<?php

class WordScheduler{
	public function fire($job, $data){
		$job_id = $job->getJobId(); // Get job id

		$ejob = Job::where('job_id',$job_id)->first(); // Find the job in database

		$ejob->status = 'running'; //Set job status to running

		$ejob->save();

		Artisan::call('make:word',array('id' => $data['id']));
		
		$ejob->status = 'finished'; //Set job status to finished

		$ejob->save();
		
		$job->delete();
		return true;
		
	}
}