<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateActivityDivisionCode extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_division_code', function(Blueprint $table)
		{
			$activities = Activity::where('division_code','>',0)->get();
			if(!empty($activities)){
				$activity_approver = array();
				foreach ($activities as $activity) {
					$activity_approver[] = array('activity_id' => $activity->id, 'user_id' => $activity->division_code);
				}
				ActivityApprover::insert($activity_approver);
			}
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_division_code', function(Blueprint $table)
		{
			
		});
	}

}
