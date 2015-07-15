<?php

class MailScheduler{
	public function fire($job, $data){

		// $user = User::find($data['user_id']);
		// $cycles = Cycle::whereIn('id',$data['cycles']);
		// $data2['cycles'] = $cycles;
		// $data2['user'] = $user->getFullname();
		// $data2['email'] = $user->email;
		// $data2['fullname'] = $user->getFullname();
		// $data2['activities'] = Activity::ProponentActivitiesForApproval($user->id,$data['cycles']);

		// Mail::send('emails.mail1', $data2, function($message) use ($data){
		// 	$message->to("rbautista@chasetech.com", $data2['fullname'])->subject('TOP ACTIVITY STATUS');
		// });

		File::append(storage_path().'/queue.txt',"hello world".PHP_EOL); //Add content to file
		
		// $job->delete();
		return true;

	}
}