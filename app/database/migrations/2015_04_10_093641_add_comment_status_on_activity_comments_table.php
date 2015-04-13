<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCommentStatusOnActivityCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_comments', function(Blueprint $table)
		{
			$table->string('comment_status')->after('activity_id')->nullable();
			$table->string('class')->after('comment_status')->nullable();
			$table->dropColumn(array('comment_status_id'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_comments', function(Blueprint $table)
		{
			$table->dropColumn(array('comment_status', 'class'));
			$table->integer('comment_status_id')->after('comment')->nullable();
		});
	}

}
