<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveSchemeOnTradedealChannels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_channels', function(Blueprint $table)
		{
			$table->dropColumn(['scheme', 'buy']);
			$table->string('pre_code')->nullable()->after('tradedeal_uom_id');
			$table->string('pre_desc')->nullable()->after('pre_code');
			$table->decimal('pre_cost',10,2)->nullable()->after('pre_desc');
			$table->integer('pre_pcs_case')->nullable()->after('pre_cost');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_channels', function(Blueprint $table)
		{
			$table->string('scheme');
			$table->integer('buy')->unsigned()->nullable();
			$table->dropColumn(['pre_code', 'pre_desc', 'pre_cost', 'pre_pcs_case']);
		});
	}

}
