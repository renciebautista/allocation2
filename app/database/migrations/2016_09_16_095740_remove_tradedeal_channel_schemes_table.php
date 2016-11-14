<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveTradedealChannelSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('tradedeal_channel_schemes');
		Schema::drop('tradedeal_host_skus');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('tradedeal_channel_schemes', function(Blueprint $table)
		{
			$table->engine = "MyISAM";
			
			$table->increments('id');
			$table->integer('tradedeal_channel_id')->unsigned();
			$table->foreign('tradedeal_channel_id')->references('id')->on('tradedeal_channels');
			$table->integer('tradedeal_part_sku_id')->unsigned();
			$table->foreign('tradedeal_part_sku_id')->references('id')->on('tradedeal_part_skus');
			$table->integer('tradedeal_uom_id')->unsigned();
			$table->foreign('tradedeal_uom_id')->references('id')->on('tradedeal_uoms');
			$table->integer('buy');
			$table->integer('free');
			$table->decimal('coverage',12, 2);

			$table->timestamps();

		});

		Schema::create('tradedeal_host_skus', function(Blueprint $table)
		{
			$table->engine = "MyISAM";

			$table->increments('id');
			$table->integer('tradedeal_part_sku_id')->unsigned();
			$table->foreign('tradedeal_part_sku_id')->references('id')->on('tradedeal_part_skus');
			$table->string('code');
			$table->string('desc');
			$table->decimal('cost',10,2);
			$table->integer('pcs_case');
			$table->integer('qty');
		});
	}

}
