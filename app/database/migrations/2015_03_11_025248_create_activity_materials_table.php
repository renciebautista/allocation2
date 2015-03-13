<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityMaterialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_materials', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->integer('source_id')->unsigned();
			$table->foreign('source_id')->references('id')->on('material_sources');
			$table->string('material');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activity_materials', function(Blueprint $table)
		{
			$table->dropForeign('activity_materials_activity_id_foreign');
			$table->dropForeign('activity_materials_source_id_foreign');
		});

		Schema::drop('activity_materials');
	}

}
