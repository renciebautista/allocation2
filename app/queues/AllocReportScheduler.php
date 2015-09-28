<?php

class AllocReportScheduler{
	public function fire($job, $data){
		$job_id = $job->getJobId(); // Get job id
		set_time_limit(0);
		Artisan::call('make:allocreport',array('temp_id' => $data['temp_id'],
			'user_id' => $data['user_id'],
			'cycles' => $data['cycles']));
		$job->delete();

		File::append(storage_path().'/queue.txt',$data['temp_id'].'=>'.$data['user_id'].'=>'.$data['cycles'].'=>'.$job_id.PHP_EOL); //Add content to file
		return true;
	}
}	