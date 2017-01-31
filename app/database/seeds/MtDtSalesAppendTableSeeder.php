<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Flynsarmy\CsvSeeder\CsvSeeder;

class MtDtSalesAppendTableSeeder extends CsvSeeder {

	public function __construct()
	{
		$this->table = 'mt_dt_sales';
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
		$this->filename = $folderpath.$latest.'/append_sales.csv';

		$this->offset_rows = 1;
	    $this->mapping = [
	        2 => 'area_code',
	        4 => 'customer_code',
	        6 => 'distributor_code',
	        9 => 'plant_code',
	        10 => 'account_name',
	        12 => 'coc_03_code',
	        14 => 'coc_04_code',
	        16 => 'coc_05_code',
	       	18 => 'child_sku_code',
	       	20 => 'gss',
	       	21 => 'gsv',
	    ];

		echo $latest .'-->'.$this->filename.PHP_EOL; 


	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();
		parent::run();
	}

}