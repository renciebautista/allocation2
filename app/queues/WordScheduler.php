<?php

class WordScheduler{
	public function fire($job, $data){
		Artisan::call('make:word',array('id' => $data['id']));
		return true;
		
	}
}