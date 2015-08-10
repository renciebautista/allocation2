<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityApproverDescTableSeeder extends Seeder {

	public function run()
	{
		$approvers = ActivityApprover::all();
		foreach($approvers as $approver){
			$user = User::find($approver->user_id);
			$approver->approver_desc = $user->getFullname();
			$approver->contact_no = $user->contact_no;
			$approver->group_id = $user->roles[0]->id;
			$approver->update();
		}
	}

}