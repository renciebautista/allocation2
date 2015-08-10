<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIdsOnAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocations', function(Blueprint $table)
		{
			$table->string('group_code')->nullable()->after('shipto_id');
			$table->string('area_code')->nullable()->after('group');
			$table->string('sold_to_code')->nullable()->after('area');
			$table->string('ship_to_code')->nullable()->after('sold_to');
			$table->string('channel_code')->nullable()->after('ship_to');
			$table->string('account_group_code')->nullable()->after('channel');
			$table->string('outlet_code')->nullable()->after('account_group_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('allocations', function(Blueprint $table)
		{
			$table->dropColumn(array('group_code', 'area_code', 'sold_to_code', 'ship_to_code', 'channel_code', 'account_group_code', 'outlet_code'));
		});
	}

}
