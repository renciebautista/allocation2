<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Flynsarmy\CsvSeeder\CsvSeeder;

class OutletSalesTableSeeder extends CsvSeeder {

	public function __construct()
	{
		$this->table = 'outlet_sales';
		$this->filename = app_path().'/database/seeds/seed_files/outletsales.csv';
	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

		parent::run();
	}

}