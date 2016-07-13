<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealPartSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			$table->increments('id');
			$table->integer('activity_id')->unsigned();
			$table->foreign('activity_id')->references('id')->on('activities');
			$table->string('host_code');
			$table->string('host_desc');
			$table->decimal('host_cost',10,2);
			$table->integer('host_pcs_case');
			$table->string('ref_code');
			$table->string('ref_desc');
			$table->string('pre_code')->nullable();
			$table->string('pre_desc')->nullable();
			$table->decimal('pre_cost',10,2)->nullable();
			$table->integer('pre_pcs_case')->nullable();
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
		Schema::drop('tradedeal_part_skus');
	}

}
