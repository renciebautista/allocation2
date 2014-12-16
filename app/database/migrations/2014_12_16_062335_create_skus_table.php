<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSkusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('skus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('sku_code');
			$table->string('sku_desc');
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
		Schema::drop('skus');
	}

}
