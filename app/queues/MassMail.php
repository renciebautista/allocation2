<?php

class MassMail{
	public function fire($job, $data){
		Artisan::call('release:activity');
		$job->delete();
		return true;
	}
}