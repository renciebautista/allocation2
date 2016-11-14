<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPrOnTradedealSchemeSkus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_scheme_skus', function(Blueprint $table)
		{
			$table->decimal('pur_req', 12,2)->after('tradedeal_part_sku_id');
			$table->decimal('free_cost', 12,2)->after('pur_req');
			$table->decimal('cost_to_sale', 12,2)->after('free_cost');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tradedeal_scheme_skus', function(Blueprint $table)
		{
			$table->dropColumn(['pur_req', 'free_cost', 'cost_to_sale']);
		});
	}

}
