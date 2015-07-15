<?php

class MailScheduler{
	public function fire($job, $data){
		Artisan::call('test:mail',array('type' => $data['type'], 'user_id' => $data['user_id'], 'role_id' => $data['role_id']));
		$job->delete();
		return true;
	}
}