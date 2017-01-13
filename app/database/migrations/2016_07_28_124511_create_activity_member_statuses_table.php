<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityMemberStatusesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_member_statuses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('mem_status');
		});

		DB::statement("INSERT INTO activity_member_statuses (id, mem_status) VALUES
			(1, 'FOR APPROVAL'),
			(2, 'DENIED'),
			(3, 'APPROVE'),
			(4, 'WAITING FOR RELEASE');");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activity_member_statuses');
	}

}
