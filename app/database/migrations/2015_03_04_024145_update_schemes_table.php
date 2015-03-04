<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSchemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->string('item_code')->nullable()->after('name');
			$table->string('item_barcode')->nullable()->after('item_code');
			$table->string('item_casecode')->nullable()->after('item_barcode');
			$table->decimal('pr',12,2)->nullable()->after('item_casecode');
			$table->decimal('srp_p',12,2)->nullable()->after('pr');
			$table->decimal('other_cost',12,2)->nullable()->after('srp_p');
			$table->decimal('ulp',12,2)->nullable()->after('other_cost');
			$table->decimal('cost_sale',12,2)->nullable()->after('ulp');
			$table->integer('deals')->nullable()->after('quantity');
			$table->decimal('tts_r',12,2)->nullable()->after('deals');
			$table->decimal('pe_r',12,2)->nullable()->after('tts_r');
			$table->decimal('total_cost',12,2)->nullable()->after('pe_r');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('schemes', function(Blueprint $table)
		{
			$table->dropColumn(array('item_code', 'item_barcode', 'item_casecode', 'pr', 'srp_p', 'other_cost',
				'ulp', 'cost_sale', 'deals', 'tts_r', 'pe_r', 'total_cost'));
		});
	}

}
