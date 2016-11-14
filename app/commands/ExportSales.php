<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class ExportSales extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'export:sales';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export ETOP customer and sales.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->line('Exporting sales');

		set_time_limit(0);
		ini_set('memory_limit', -1);
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		$customers_list =  DB::table('customers')
			->select('areas.group_code as group_code','group_name','area_name',
				'customer_name','customer_code','customers.area_code as area_code',
				'customers.area_code_two as area_code_two','multiplier','active','from_dt','sob_customer_code')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->join('groups', 'areas.group_code', '=', 'groups.group_code')
			->orderBy('groups.id')
			->orderBy('areas.id')
			->orderBy('customers.id')
			->get();

		$_shiptos_list = DB::table('ship_tos')
			->select('customer_code','ship_to_code','ship_to_name','split', 'leadtime', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'active')
			->get();

		$_accounts_list = DB::table('accounts')
			->select('accounts.id','ship_to_code','area_code', 'account_name', 'channel_name','accounts.account_group_code', 
				'channels.channel_code', 'active', 'account_groups.account_group_name')
			->join('channels', 'accounts.channel_code', '=', 'channels.channel_code')
			->join('account_groups', 'accounts.account_group_code', '=', 'account_groups.account_group_code')
			->get();

		$_outlets = DB::table('outlets')->get();

		foreach ($customers_list as $customer) {
			foreach ($_shiptos_list as $_shipto){
				if($customer->customer_code == $_shipto->customer_code){
					if(!is_null($_shipto->ship_to_code)){
						unset($_shipto->accounts);
						foreach ($_accounts_list as $_account){
							if(($_account->area_code == $customer->area_code) && ($_account->ship_to_code == $_shipto->ship_to_code)){
								// $outlets = array();
								// foreach ($_outlets as $_outlet) {
								// }
								$_shipto->accounts[] = (array) $_account;
							}
						}
					}
					$customer->shiptos[] = (array)	$_shipto;
				}
			}
			$data[] = (array)$customer;
		}
		$filename = 'Customer Masterfile '.date('Y-m-d').'.xlsx';
		$filePath = storage_path('exports/'.$filename);
		$writer = WriterFactory::create(Type::XLSX);
		$writer->setShouldCreateNewSheetsAutomatically(true); // default value
		$writer->openToFile($filePath); // write data to a file or to a PHP stream

		$header = array('GROUP CODE', 'GROUP', 'AREA CODE', 'AREA CODE TWO', 'AREA', 'SOLD TO CODE', 'SOLD TO', 'SOLD TO STATUS (ACTIVE)','SALES MULTIPLIER', 'FROM DT', 
					'SOB SOLD TO CODE', 'SHIP TO CODE', 'CUSTOMER SHIP TO NAME', '% SPLIT', 'LEAD TIME (DAYS)', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY',
					'SUNDAY', 'SHIP TO POINT STATUS (ACTIVE)', 
				 	'CHANNEL CODE', 'CHANNEL',  'ACCOUNT GROUP CODE', 'ACCOUNT GROUP', 'ACCOUNT NAME', 'ACCOUNT STATUS (ACTIVE)');
		$writer->addRow($header);

		foreach ($data as $customer) {
			$writer->addRow( array($customer['group_code'],
				$customer['group_name'],
				$customer['area_code'],
				$customer['area_code_two'],
				$customer['area_name'],
				$customer['customer_code'],
				$customer['customer_name'],
				$customer['active'],
				$customer['multiplier'],
				$customer['from_dt'],
				$customer['sob_customer_code']));

			if(isset($customer['shiptos'])){
				foreach ($customer['shiptos'] as $shipto) {
					$writer->addRow(array($customer['group_code'],
				 		$customer['group_name'],
						$customer['area_code'],
						$customer['area_code_two'],
						$customer['area_name'],
						$customer['customer_code'],
						$customer['customer_name'],
						$customer['active'],
						$customer['multiplier'],
						$customer['from_dt'],
						$customer['sob_customer_code'],
						$shipto['ship_to_code'],
						$shipto['ship_to_name'],
						$shipto['split'],
						$shipto['leadtime'],
						$shipto['mon'],
						$shipto['tue'],
						$shipto['wed'],
						$shipto['thu'],
						$shipto['fri'],
						$shipto['sat'],
						$shipto['sun'],
						$shipto['active']));
					if(isset($shipto['accounts'])){
						foreach ($shipto['accounts'] as $account) {
							$writer->addRow(array($customer['group_code'],
						 		$customer['group_name'],
								$customer['area_code'],
								$customer['area_code_two'],
								$customer['area_name'],
								$customer['customer_code'],
								$customer['customer_name'],
								$customer['active'],
								$customer['multiplier'],
								$customer['from_dt'],
								$customer['sob_customer_code'],
								$shipto['ship_to_code'],
								$shipto['ship_to_name'],
								$shipto['split'],
								$shipto['leadtime'],
								$shipto['mon'],
								$shipto['tue'],
								$shipto['wed'],
								$shipto['thu'],
								$shipto['fri'],
								$shipto['sat'],
								$shipto['sun'],
								$shipto['active'],
								$account['channel_code'],
								$account['channel_name'],
								$account['account_group_code'],
								$account['account_group_name'],
								$account['account_name'],
								$account['active']));
						}
					}
			 	}
			}
		 	
		}

		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Customer Masterfile');

		$newSheet = $writer->addNewSheetAndMakeItCurrent();
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('MT Primary Sales');
		
		$mt_sales = DB::table('mt_primary_sales')->get();
		foreach($mt_sales as $key => $value)
		{
			if(isset($value->gsv)){
				$value->gsv = (double) $value->gsv;
			}
			$mt_sales[$key] = (array) $value;
		}
		$export_data = $mt_sales;
		$header = array('record_id', 'area_code', 'customer_code', 'child_sku_code', 'gsv');
		$writer->addRow($header);
		$writer->addRows($export_data); // add multiple rows at a time

		$newSheet = $writer->addNewSheetAndMakeItCurrent();
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('DT Secondary Sales');
		$dt_sales = DB::table('dt_secondary_sales')->get();
		foreach($dt_sales as $key => $value)
		{
			if(isset($value->gsv)){
				$value->gsv = (double) $value->gsv;
			}
			$dt_sales[$key] = (array) $value;
		}
		$export_data = $dt_sales;
		$header = array('record_id', 'area_code', 'customer_code', 'child_sku_code', 'coc_03_code', 'gsv');
		$writer->addRow($header);
		$writer->addRows($export_data); // add multiple rows at a time

		$newSheet = $writer->addNewSheetAndMakeItCurrent();
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Ship To Sales');
		$shipto_sales = DB::table('ship_to_sales')->get();
		foreach($shipto_sales as $key => $value)
		{
			if(isset($value->gsv)){
				$value->gsv = (double) $value->gsv;
			}
			$shipto_sales[$key] = (array) $value;
		}
		$export_data = $shipto_sales;
		$header = array('record_id', 'ship_to_code', 'child_sku_code','gsv');
		$writer->addRow($header);
		$writer->addRows($export_data); // add multiple rows at a time

		$newSheet = $writer->addNewSheetAndMakeItCurrent();
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Outlet Sales');
		$outlet_sales = DB::table('outlet_sales')->get();
		foreach($outlet_sales as $key => $value)
		{
			if(isset($value->gsv)){
				$value->gsv = (double) $value->gsv;
			}
			$outlet_sales[$key] = (array) $value;
		}
		$export_data = $outlet_sales;
		$header = array('record_id', 'area_code', 'customer_code', 'account_name', 'outlet_code', 'child_sku_code', 'coc_03_code', 'gsv');
		$writer->addRow($header);
		$writer->addRows($export_data); // add multiple rows at a time

		// export groups
		$newSheet = $writer->addNewSheetAndMakeItCurrent();
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Groups');
		$groups = DB::table('groups')->get();
		foreach($groups as $key => $value)
		{
			$groups[$key] = (array) $value;
		}
		$export_data = $groups;
		$header = array('id', 'group_code', 'group');
		$writer->addRow($header);
		$writer->addRows($export_data); // add multiple rows at a time

		// areas
		$newSheet = $writer->addNewSheetAndMakeItCurrent();
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Areas');
		$areas = DB::table('areas')->get();
		foreach($areas as $key => $value)
		{
			$areas[$key] = (array) $value;
		}
		$export_data = $areas;
		$header = array('id', 'group_code', 'area_code', 'area_name');
		$writer->addRow($header);
		$writer->addRows($export_data); // add multiple rows at a time

		// customers
		$newSheet = $writer->addNewSheetAndMakeItCurrent();
		$sheet = $writer->getCurrentSheet();
		$sheet->setName('Customers');
		$customers = DB::table('customers')->get();
		foreach($customers as $key => $value)
		{
			$customers[$key] = (array) $value;
		}
		$export_data = $customers;
		$header = array('id', 'area_code', 'area_code_two', 'customer_code', 'sob_customer_code', 'customer_name', 'active', 'multiplier', 'from_dt');
		$writer->addRow($header);
		$writer->addRows($export_data); // add multiple rows at a time



		$file = CustomerMasterfile::where('filename',$filename)->first();
		if(empty($file)){
			$newfile = new CustomerMasterfile;
			$newfile->filename = $filename;
			$newfile->save();
		}
		
		$writer->close();
		$timeSecond = strtotime(date('Y-m-d H:i:s'));
		$differenceInSeconds = $timeSecond - $timeFirst;
		$this->line($filePath);
		$this->line( 'Time used ' . $differenceInSeconds . " sec");


	}

	// /**
	//  * Get the console command arguments.
	//  *
	//  * @return array
	//  */
	// protected function getArguments()
	// {
	// 	return array(
	// 		array('example', InputArgument::REQUIRED, 'An example argument.'),
	// 	);
	// }

	// /**
	//  * Get the console command options.
	//  *
	//  * @return array
	//  */
	// protected function getOptions()
	// {
	// 	return array(
	// 		array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
	// 	);
	// }

}
