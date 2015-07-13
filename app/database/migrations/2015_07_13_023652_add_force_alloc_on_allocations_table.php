<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForceAllocOnAllocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('allocations', function(Blueprint $table)
		{
			$table->decimal('forced_sold_to_gsv',12,2)->nullable()->after('sold_to_gsv');
			$table->decimal('forced_sold_to_gsv_p',12,2)->nullable()->after('sold_to_gsv_p');
			$table->integer('forced_sold_to_alloc')->nullable()->after('sold_to_alloc');
			$table->decimal('forced_ship_to_gsv',12,2)->nullable()->after('ship_to_gsv');
			$table->decimal('forced_ship_to_gsv_p',12,2)->nullable()->after('ship_to_gsv_p');
			$table->integer('forced_ship_to_alloc')->nullable()->after('ship_to_alloc');
			$table->decimal('forced_outlet_to_gsv',12,2)->nullable()->after('outlet_to_gsv');
			$table->decimal('forced_outlet_to_gsv_p',12,2)->nullable()->after('outlet_to_gsv_p');
			$table->integer('forced_outlet_to_alloc')->nullable()->after('outlet_to_alloc');
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
			$table->dropColumn(array('forced_sold_to_gsv', 
				'forced_sold_to_gsv_p',
				'forced_sold_to_alloc',
				'forced_ship_to_gsv',
				'forced_ship_to_gsv_p',
				'forced_ship_to_alloc',
				'forced_outlet_to_gsv',
				'forced_outlet_to_gsv_p',
				'forced_outlet_to_alloc'));
		});
	}

}
