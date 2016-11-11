<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTradedealChannelMappings extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tradedeal_channel_mappings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('coc_03_code');
			$table->string('coc_04_code');
			$table->string('coc_05_code');
			$table->string('sub_channel_desc');
			$table->boolean('active')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tradedeal_channel_mappings');
	}

}
