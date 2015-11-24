<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Flynsarmy\CsvSeeder\CsvSeeder;

class ShipToSalesTableSeeder extends CsvSeeder {

	public function __construct()
	{
		$this->table = 'ship_to_sales';
		$folderpath = app_path().'/database/seeds/seed_files/Sales/';
		$folders = File::directories($folderpath);
		$latest = '';
		foreach ($folders as $value) {
			$_dir = explode("/", $value);
			$cnt = count($_dir);
			$name = $_dir[$cnt -1];
			if(strtotime($name) > strtotime($latest)){
				$latest = $name;
			}
		}
		$this->filename = app_path().$folderpath.$latest.'/shiptosales.csv';
		// $this->filename = app_path().'/database/seeds/seed_files/shiptosales.csv';
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