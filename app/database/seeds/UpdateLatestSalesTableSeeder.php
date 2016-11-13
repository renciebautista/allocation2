<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UpdateLatestSalesTableSeeder extends Seeder {

	public function run()
	{
		set_time_limit(0);
		ini_set('memory_limit', -1);
		Eloquent::unguard();
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		// $this->call('MtPrimarySalesTableSeeder');
		// $this->call('DtSecondarySalesTableSeeder');
		// $this->call('ShipToSalesTableSeeder');
		// $this->call('OutletSalesTableSeeder');
		$this->call('MtDtSalesTableSeeder');

		$summaries =  DB::table('mt_dt_sales')
			->groupBy('coc_05_code')
			->groupBy('coc_04_code')
			->groupBy('coc_03_code')
			->groupBy('account_name')
			->groupBy('plant_code')
			->groupBy('distributor_code')
			->groupBy('customer_code')
			->groupBy('area_code')
			->get();

		DB::table('mt_dt_hieracry')->truncate();

		foreach ($summaries as $value) {
			$sum = new MtDtHieracry;
			$sum->area_code = $value->area_code;
			$sum->customer_code = $value->customer_code;
			$sum->distributor_code = $value->distributor_code;
			$sum->plant_code = $value->plant_code;
			$sum->account_name = $value->account_name;
			$sum->coc_03_code = $value->coc_03_code;
			$sum->coc_04_code = $value->coc_04_code;
			$sum->coc_05_code = $value->coc_05_code;
			$sum->save();
		}


		$timeSecond = strtotime(date('Y-m-d H:i:s'));
		$differenceInSeconds = $timeSecond - $timeFirst;
		echo  'Time used ' . $differenceInSeconds . " sec";

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}