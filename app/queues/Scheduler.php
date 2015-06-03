<?php

class Scheduler{
	public function fire($job, $data){
		$job_id = $job->getJobId(); // Get job id

		$ejob = Job::where('job_id',$job_id)->first(); // Find the job in database

		$ejob->status = 'running'; //Set job status to running

		$ejob->save();

		// Artisan::call('make:pdf');
		Artisan::call('make:pdf',array('id' => $data['id']));
		File::append(storage_path().'/queue.txt',$data['string'].$job_id.PHP_EOL); //Add content to file

		$ejob->status = 'finished'; //Set job status to finished

		$ejob->save();
		$job->delete();
		return true;
		
	}
}