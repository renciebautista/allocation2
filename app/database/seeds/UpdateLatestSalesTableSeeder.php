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
		// $this->call('MtDtSalesTableSeeder');
		// echo "Sales uploaded".PHP_EOL; 
		// $this->call('MtDtHierarchyTableSeederTableSeeder');
		// echo "Mt Dt Hierarchy created".PHP_EOL; 
		// $this->call('UpdateCustomerTreeTableSeeder');
		// echo "Customer Tree created".PHP_EOL; 
		// $timeSecond = strtotime(date('Y-m-d H:i:s'));
		// $differenceInSeconds = $timeSecond - $timeFirst;
		// echo  'Time used ' . $differenceInSeconds . " sec";

		Mail::send('emails.confirm',[], function($message){
          	$message->to("rbautista@chasetech.com");
			// $message->bcc("rosarah.reyes@unilever.com");
			$message->subject('Upload Sales Complete');
        });

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}