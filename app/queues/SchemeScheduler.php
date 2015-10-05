<?php

class SchemeScheduler{
	public function fire($job, $data){
		Artisan::call('update:scheme',array('id' => $data['id']));
		$job->delete();
		return true;
		
	}
}