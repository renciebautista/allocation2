<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDescOnSchemeSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('scheme_skus', function(Blueprint $table)
		{
			$table->string('sku_desc')->after('sku');
			$table->string('division_code');
			$table->string('division_desc');
			$table->string('category_code');
			$table->string('category_desc');
			$table->string('brand_code');
			$table->string('brand_desc');
			$table->string('cpg_code');	
			$table->string('cpg_desc');	
			$table->string('packsize_code');
			$table->string('packsize_desc');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('scheme_skus', function(Blueprint $table)
		{
			$table->dropColumn(['sku_desc', 'division_code', 'division_desc',
			 'category_code', 'category_desc',
			 'brand_code', 'brand_desc',
			 'cpg_code', 'cpg_desc',
			 'packsize_code', 'packsize_desc']);
		});
	}

}
