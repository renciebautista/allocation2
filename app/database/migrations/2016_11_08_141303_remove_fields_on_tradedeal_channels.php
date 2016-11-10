<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveFieldsOnTradedealChannels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tradedeal_channels', function(Blueprint $table)
		{
			$table->dropColumn(['tradedeal_type_id', 'tradedeal_uom_id',
				'pre_code', 'pre_desc', 'pre_cost', 'pre_pcs_case', 
				'free', 'coverage', 'pur_req', 
				'buy_pcs', 'pcs_deal', 'scheme_code', 'rtm_tag']);

			$table->string('channel_code')->after('l5_desc')->nullable();
			$table->string('channel_desc')->after('channel_code')->nullable();
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
			$table->integer('tradedeal_type_id')->unsigned()->nullable();
			$table->integer('tradedeal_uom_id')->unsigned()->nullable();
			$table->string('pre_code')->nullable()->after('tradedeal_uom_id');
			$table->string('pre_desc')->nullable()->after('pre_code');
			$table->decimal('pre_cost',10,2)->nullable()->after('pre_desc');
			$table->integer('pre_pcs_case')->nullable()->after('pre_cost');
			$table->string('rtm_tag');

			$table->integer('free')->unsigned()->nullable();
			$table->decimal('pur_req', 10, 2);
			$table->integer('buy_pcs');
			$table->integer('pcs_deal');
			$table->string('scheme_code');
			$table->decimal('coverage', 12, 2)->after('free');

			$table->dropColumn(['channel_code', 'channel_desc']);
		});
	}

}
