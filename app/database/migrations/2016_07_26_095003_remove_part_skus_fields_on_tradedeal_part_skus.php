<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemovePartSkusFieldsOnTradedealPartSkus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->dropColumn(['set_name']);
			$table->string('host_code')->nullable()->after('activity_id');
			$table->string('host_desc')->nullable()->after('host_code');
			$table->decimal('host_cost',10,2)->nullable()->after('host_desc');
			$table->integer('host_pcs_case')->nullable()->after('host_cost');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->string('set_name')->after('activity_id');
			$table->dropColumn(['host_code', 'host_desc', 'host_cost', 'host_pcs_case']);
		});
	}

}
