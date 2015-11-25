<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Flynsarmy\CsvSeeder\CsvSeeder;

class OutletSalesTableSeeder extends CsvSeeder {

	public function __construct()
	{
		$this->table = 'outlet_sales';
		$folderpath = app_path().'/database/seeds/seed_files/Sales/';
		$folders = File::directories($folderpath);
		$latest = '11232015';
		foreach ($folders as $value) {
			$_dir = explode("/", $value);
			$cnt = count($_dir);
			$name = $_dir[$cnt - 1];
			$latest_date = DateTime::createFromFormat('mdY', $latest);
			$now = DateTime::createFromFormat('mdY', $name);
			if($now > $latest_date){
				$latest = $name;
			}
		}
		$this->filename = $folderpath.$latest.'/outletsales.csv';
		// $this->filename = app_path().'/database/seeds/seed_files/outletsales.csv';
		// echo $this->filename;
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