<?php


use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

use League\Csv\Reader;

class CustomerMasterController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /customermaster
	 *
	 * @return Response
	 */
	public function index()
	{
		$exports = CustomerMasterfile::all();
		return View::make('customermaster.index',compact('exports'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /customermaster/create
	 *
	 * @return Response
	 */
	public function export()
	{
		Artisan::call('export:sales');
		return Redirect::route('customermaster.index')
				->with('class', 'alert-success')
			->with('message', 'Export successfuly created');

	}


	public function download($id){
		$file = CustomerMasterfile::find($id);
		if(!empty($file)){
			$path = storage_path().'/exports/'.$file->filename;
			return Response::download($path, $file->filename);
		}else{
			echo 'File not found';
		}
	}

	public function exportall()
	{

		// $file_name = 'MT DT Hierarchy.csv';
		// $file = public_path()."/".$file_name; //CSV file is store this path
		// File::delete($file);
  //       $query = sprintf("
  //       	SELECT 'id', 'area_code', 'customer_code', 'customer_code', 'plant_code', 'account_name', 'coc_03_code', 'coc_04_code', 'coc_05_code',
  //       	'id', 'group_code', 'area_code', 'area_name', 'id', 'group_code', 'group_name', 'id', 'area_code', 'area_code_two', 'customer_code',
  //       	'sob_customer_code', 'customer_name', 'active', 'multiplier', 'from_dt', 'trade_deal', 'id', 'customer_code', 'sold_to_code', 'ship_to_code', 'plant_code',
  //       	'ship_to_name', 'split', 'leadtime', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun', 'active', 'id', 'group_code', 'group', 'area_code',
  //       	'area', 'customer_code', 'customer', 'distributor_code', 'distributor_name', 'plant_code', 'ship_to_name', 'id', 'area_code', 'ship_to_code',
  //       	'account_group_code', 'channel_code', 'account_name', 'active', 
  //       	'id', 'channel_code', 'coc_03_code', 'l3_desc', 'l4_code', 'l4_desc', 'l5_code', 'l5_desc', 'rtm_tag', 'trade_deal',
  //       	'id' ,'channel_code', 'channel_name'
		// 	UNION ALL
  //       	select * from mt_dt_hieracry
		// 	left join areas on areas.area_code = mt_dt_hieracry.area_code
		// 	left join groups on groups.group_code = areas.group_code
		// 	left join customers on customers.customer_code = mt_dt_hieracry.customer_code
		// 	left join ship_tos on ship_tos.plant_code = mt_dt_hieracry.plant_code
		// 	left join ship_to_plant_codes on ship_to_plant_codes.distributor_code = mt_dt_hieracry.distributor_code
		// 	left join accounts on (mt_dt_hieracry.account_name = accounts.account_name AND ship_tos.ship_to_code = accounts.ship_to_code)
		// 	left join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
		// 	left join channels on channels.channel_code = sub_channels.channel_code INTO OUTFILE '%s' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' LINES TERMINATED BY '\n'",$file);

  //       // \DB::getpdo()->exec($query);
		// DB::statement($query);

  //       return Response::download($file, $file_name);

		set_time_limit(0);
		ini_set('memory_limit', -1);
		// $select = sprintf('select * from mt_dt_hieracry
		// 	left join areas on areas.area_code = mt_dt_hieracry.area_code
		// 	left join groups on groups.group_code = areas.group_code
		// 	left join customers on customers.customer_code = mt_dt_hieracry.customer_code
		// 	left join ship_tos on ship_tos.plant_code = mt_dt_hieracry.plant_code
		// 	left join ship_to_plant_codes on ship_to_plant_codes.distributor_code = mt_dt_hieracry.distributor_code
		// 	left join accounts on (mt_dt_hieracry.account_name = accounts.account_name AND ship_tos.ship_to_code = accounts.ship_to_code)
		// 	left join sub_channels on sub_channels.coc_03_code = mt_dt_hieracry.coc_03_code
		// 	left join channels on channels.channel_code = sub_channels.channel_code
		// 	left join level4 on level4.l4_code = mt_dt_hieracry.coc_04_code
		// 	left join level5 on level5.l5_code = mt_dt_hieracry.coc_05_code');

		// $data = \DB::connection('mysql')->select($select);

		//  Helper::debug($data[0]);
		// // query_to_csv($data, 'data.csv');

		// $fp = fopen('data.csv', 'w');
  //       foreach ($data as $row) {
  //           fputcsv($fp, $row);
  //       }

  //       fclose($fp);

		// function data_to_csv($data, $filename)
		//     {
		        
		//     }

		// set_time_limit(0);
		// ini_set('memory_limit', -1);
		// $table = MtDtHieracry::getAllHierarcy();
		// Helper::debug($table[0]);
		// $output='';
  // 		foreach ($table as $row) {
  //     		$output.=  implode(",",$row->toArray());
  // 		}
  // 		$headers = array(
  //     		'Content-Type' => 'text/csv',
  //     		'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
  // 		);

  		// return Response::make(rtrim($output, "\n"), 200, $headers);

		// Helper::debug($datas[0]);
		// // Excel::create("MT DT Hierarcy", function($excel) use($datas){
		// // 	$excel->sheet('Sheet1', function($sheet) use($datas) {
		// // 		$sheet->fromModel($datas,null, 'A1', true);
		// // 	})->download('xls');

		// // });

		$header = ['id','area_code','customer_code','distributor_code','plant_code','account_name','coc_03_code','coc_04_code','coc_05_code','id','group_code','area_code','area_name','id','group_code','group_name','id','area_code','area_code_two','customer_code','sob_customer_code','customer_name','active','multiplier','from_dt','trade_deal','id','customer_code','sold_to_code','ship_to_code','plant_code','ship_to_name','split','leadtime','mon','tue','wed','thu','fri','sat','sun','active','id','group_code','group','area_code','area','customer_code','customer','distributor_code','distributor_name','plant_code','ship_to_name','id','area_code','ship_to_code','account_group_code','channel_code','account_name','active','id','channel_code','coc_03_code','l3_desc','l4_code','l4_desc','l5_code','l5_desc','rtm_tag','trade_deal','id','channel_code','channel_name'];

	$table = MtDtHieracry::getAllHierarcy();

		// $header = $table->getAttributes();
		// dd($table[0]->getAttributes());
		// $output='';
  // 		foreach ($table as $row) {
  //     		$output.=  implode(",",$row->toArray());
  // 		}
  // 		$headers = array(
  //     		'Content-Type' => 'text/csv',
  //     		'Content-Disposition' => 'attachment; filename="ExportFileName.csv"',
  // 		);

  // 		return Response::make(rtrim($output, "\n"), 200, $headers);


		// Helper::debug($datas[0]);
		$writer = WriterFactory::create(Type::XLSX);
		$writer->setShouldCreateNewSheetsAutomatically(true); // default value
		$writer->openToBrowser('MT DT Hierarcy.xlsx'); // write data to a file or to a PHP stream
		$writer->addRow($header); // add multiple rows at a time

		foreach ($table as $key => $value) {
			$table[$key] = (array) $value;
		}
		// Helper::debug($datas[0]);
		$writer->addRows($table); // add multiple rows at a time

		 
		$writer->close();

	}
	
}