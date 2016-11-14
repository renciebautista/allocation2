<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MakeSkuFormatNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE tradedeal_part_skus MODIFY host_sku_format varchar(255) NULL;');
		DB::statement('ALTER TABLE tradedeal_part_skus MODIFY pre_sku_format varchar(255) NULL;');
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE tradedeal_part_skus MODIFY host_sku_format varchar(255) NOT NULL;');
		DB::statement('ALTER TABLE tradedeal_part_skus MODIFY pre_sku_format varchar(255) NOT NULL;');
	}

}
