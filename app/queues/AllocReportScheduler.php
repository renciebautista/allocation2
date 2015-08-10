<?php

class AllocReportScheduler{
	public function fire($job, $data){
		Artisan::call('make:allocreport',array('temp_id' => $data['temp_id'],
			'cycles' => $data['cycles'],
			'user_id' => $data['user_id']));
		$job->delete();
		return true;
	}
}