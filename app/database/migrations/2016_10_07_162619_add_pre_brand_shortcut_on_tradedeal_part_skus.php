<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPreBrandShortcutOnTradedealPartSkus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_part_skus', function(Blueprint $table)
		{
			$table->string('pre_brand_shortcut')->after('pre_variant')->nullable();
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
			$table->dropColumn(['pre_brand_shortcut']);
		});
	}

}
