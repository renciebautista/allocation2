<?php

class SobScheduler{
	public function fire($job, $data){
		Artisan::call('make:sob',array('cycle_ids' => explode(",", $data['cycle_ids'])));
		$job->delete();
		return true;
	}
}