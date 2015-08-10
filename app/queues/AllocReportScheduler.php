<?php

class AllocReportScheduler{
	public function fire($job, $data){
		set_time_limit(0);
		Artisan::call('make:allocreport',array('temp_id' => $data['temp_id'],
			'user_id' => $data['user_id'],
			'cycles' => $data['cycles']));
		return true;
	}
}	