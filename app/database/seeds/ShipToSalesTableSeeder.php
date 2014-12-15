<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Flynsarmy\CsvSeeder\CsvSeeder;

class ShipToSalesTableSeeder extends CsvSeeder {

	public function __construct()
	{
		$this->table = 'ship_to_sales';
		$this->filename = app_path().'/database/seeds/seed_files/shiptosales.csv';
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