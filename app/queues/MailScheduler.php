<?php

class MailScheduler{
	public function fire($job, $data){
		Mail::send($data['template'], $data, function($message) use ($data){
			$message->to($data['to'], $data['fullname'])->subject($data['subject']);
		});
		return true;
	}
}