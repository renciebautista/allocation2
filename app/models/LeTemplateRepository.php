<?php

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;


class LeTemplateRepository  {

	public static function generateTemplate($tradedealscheme){

		$tradedeal = Tradedeal::findorFail($tradedealscheme->tradedeal_id);
		$activity = Activity::findorFail($tradedeal->activity_id);

		File::deleteDirectory(storage_path('le/'.$activity->id.'/'.$tradedealscheme->id));

		$scheme_uom_abv;
    	$scheme_uom_abv2;
    	if($tradedealscheme->tradedeal_uom_id == 1){
    		$scheme_uom_abv = 'P';
    		$scheme_uom_abv2 = 'PC';
    	}
    	if($tradedealscheme->tradedeal_uom_id == 2){
    		$scheme_uom_abv = 'D';
    		$scheme_uom_abv2 = 'DZ';
    	}
    	if($tradedealscheme->tradedeal_uom_id == 3){
    		$scheme_uom_abv = 'C';
    		$scheme_uom_abv2 = 'CS';
    	}

    	$host_skus = TradedealSchemeSku::getHostSku($tradedealscheme);
		if($tradedealscheme->tradedeal_type_id == 1){
			
			foreach ($host_skus as $host_sku) {
				self::generateIndividualHeader($tradedealscheme, $tradedeal, $activity, $host_sku, $scheme_uom_abv, $scheme_uom_abv2);
				self::generateIndividualMechanics($tradedealscheme, $tradedeal, $activity, $host_sku, $scheme_uom_abv, $scheme_uom_abv2);
				self::generateIndividualSiteAllocation($tradedealscheme, $tradedeal, $activity, $host_sku, $scheme_uom_abv, $scheme_uom_abv2);
			}
		}

		// if($tradedealscheme->tradedeal_type_id == 2){
		// 	$host_skus = TradedealSchemeSku::getHostSku($tradedealscheme);
		// 	self::generateCollective($tradedealscheme, $tradedeal, $activity, $host_skus, $scheme_uom_abv, $scheme_uom_abv2);
		// }

		if($tradedealscheme->tradedeal_type_id == 3){
			self::generateCollective($tradedealscheme, $tradedeal, $activity, $host_skus, $scheme_uom_abv, $scheme_uom_abv2);
		}
	}

	private static function getdealId($activity, $tradedealscheme, $host_sku, $scheme_uom_abv){
		$brand = $host_sku->brand_shortcut;
    	$month_year = date('ym',strtotime($activity->eimplementation_date));
    	$host_variant = substr(strtoupper($host_sku->variant),0,1);
    	$series = TradeIndividualSeries::getSeries($month_year, $tradedealscheme->id, $host_sku->host_id);
    	return 'B'.$month_year.$scheme_uom_abv.$brand.$host_variant .sprintf("%02d", $series->series);
	}

	private static function getIndFileName($tradedealscheme, $scheme_uom_abv2, $host_sku){
		$deal_id = TradedealSchemeAllocation::getSchemeCode($tradedealscheme, $host_sku);
		return $deal_id->scheme_code.' - ' .$tradedealscheme->buy.'+'.$tradedealscheme->free.' '.$scheme_uom_abv2.' '.$host_sku->brand_shortcut.' '.$host_sku->host_sku_format.' '.$host_sku->variant;
	}

	private static function generateIndividualHeader($tradedealscheme, $tradedeal, $activity, $host_sku, $scheme_uom_abv, $scheme_uom_abv2 ){
		$folder_name = self::getIndFileName($tradedealscheme, $scheme_uom_abv2, $host_sku);

		$allocations = TradedealSchemeAllocation::getAllocation($tradedealscheme, $host_sku);
    	if($tradedeal->nonUlpPremium()){
    		$header = ['DEAL ID','PROMO TYPE','IONUMBER','START DATE','END DATE','DEAL DESCRIPTION','DEAL AMOUNT','SALES ORG','DISTRIBUTOR ID','ALLOCATED BUDGET','Non Unilever Flag'];
    		$free = $host_sku->pre_desc;
    	}else{
    		$header = ['DEAL ID','IONUMBER','START DATE','END DATE','DEAL DESCRIPTION','DEAL AMOUNT','SALES ORG','DISTRIBUTOR ID','ALLOCATED BUDGET'];
    		$free = $host_sku->pre_variant;
    	}

    	$brand = $host_sku->brand_shortcut;

    	$budgets = ActivityBudget::getBudgets($activity->id);
    	$io_number = 'non-ULP PREMIUM';
    	if(!empty($budgets)){
    		if(isset($budgets[0])){
    			$io_number = $budgets[0]->io_number;
    		}
    	}
    	$start_date = date('d.m.Y', strtotime($activity->eimplementation_date));
    	$end_date = date('d.m.Y', strtotime($activity->end_date));

    	$_scheme = $tradedealscheme->buy.'+'.$tradedealscheme->free;
    	$_uom = $scheme_uom_abv2;

    	$total_deals = TradedealSchemeAllocation::getTotalDeals($tradedealscheme,$host_sku);
    	$deal_amount =  number_format($total_deals * $host_sku->pre_cost, 2, '.', '');

        Excel::create($folder_name. ' - 1 Header', function($excel) use ($header, $allocations, $tradedeal, $io_number, $start_date,$end_date, $deal_amount, $host_sku){
            $excel->sheet('Sheet1', function($sheet) use ($header, $allocations, $tradedeal, $io_number, $start_date, $end_date, $deal_amount, $host_sku) {
                $sheet->row(1, $header);
                $row = 2;
                foreach ($allocations as $value) {
                    if(($value->final_pcs > 0) && ( $value->ship_to_code != '')){
                        if($tradedeal->nonUlpPremium()){
                            $contents = $value->scheme_code.','.'BBFREE'.','.$io_number.','.(string)$start_date.','.(string)$end_date.','.$value->scheme_desc.','.$deal_amount.','.'P001'.','.$value->plant_code.','.number_format($value->final_pcs * $host_sku->pre_cost, 2, '.', '').','.'X';    
                            $sheet->getStyle("G".$row)->getNumberFormat()->setFormatCode('0.00');
                            $sheet->getStyle("J".$row)->getNumberFormat()->setFormatCode('0.00');
                        }else{
                            $contents = $value->scheme_code.','.$io_number.','.(string)$start_date.','.(string)$end_date.','.$value->scheme_desc.','.$deal_amount.','.'P001'.','.$value->ship_to_code.','.number_format($value->final_pcs * $host_sku->pre_cost, 2, '.', '');
                            $sheet->getStyle("F".$row)->getNumberFormat()->setFormatCode('0.00');
                            $sheet->getStyle("I".$row)->getNumberFormat()->setFormatCode('0.00');
                        }
                        $row_data = explode(",", $contents);
                        $sheet->row($row, $row_data);
                        $row++;
                    }
                }
            });
        })->store('csv', storage_path('le/'.$activity->id.'/'.$tradedealscheme->id.'/'.$folder_name));

	}

	private static function generateIndividualMechanics($tradedealscheme, $tradedeal, $activity, $host_sku, $scheme_uom_abv, $scheme_uom_abv2){
		$folder_name = self::getIndFileName($tradedealscheme, $scheme_uom_abv2, $host_sku);
    	$header = ['Promotion ID','Disc Seq','Buy Product','Buy Type (Value/Volume)','Min Buy','Min Buy  UoM/Curr','Get Type','Get Material','Get Qty.','Get UOM','Outlet Type','Outlet Sub Type', '','Promotion Id','Exclude outlet'];

    	$sub_types = TradedealSchemeSubType::getSchemeSubtypes($tradedealscheme);
    	$deal_id = TradedealSchemeAllocation::getSchemeCode($tradedealscheme, $host_sku);

    	$min_buy = $tradedealscheme->buy;

    	if($tradedealscheme->tradedeal_uom_id == 1){
    		$min_buy = $tradedealscheme->buy;
    	}

    	if($tradedealscheme->tradedeal_uom_id == 2){
    		$min_buy = $tradedealscheme->buy * 12;
    	}

    	if($tradedealscheme->tradedeal_uom_id == 3){
    		$min_buy = $tradedealscheme->buy * $host_sku->host_pcs_case;
    	}

    	$free = $tradedealscheme->free;
        if($tradedealscheme->tradedeal_uom_id == 2){
            $free = $tradedealscheme->free * 12;
        }

        if($tradedealscheme->tradedeal_uom_id == 3){
            $free = $tradedealscheme->free * $host_sku->pre_pcs_case;
        }

        Excel::create($folder_name. ' - 2 Mechanics', function($excel) use ($header, $sub_types, $deal_id, $host_sku, $free, $min_buy){
            $excel->sheet('Sheet1', function($sheet) use ($header, $sub_types, $deal_id, $host_sku, $free, $min_buy) {
                $sheet->row(1, $header);
                $first_row = true;
                $row = 2;
                foreach ($sub_types as $value) {
                    if($first_row){
                         $row_data = array($deal_id->scheme_code, '1', $host_sku->host_code,'Volume', $min_buy, 'PC',
                             'O - AND', $host_sku->pre_code, $free, 'PC', '', $value->sub_type, '', '' );
                         $first_row = false;
                    }else{
                         $row_data = array('', '', '','', '', '',
                             '', '', '', '', '', $value->sub_type, '', '' );
                         $first_row = false;
                    }
                    $sheet->row($row, $row_data);
                    $row++;
                }
            });
        })->store('csv', storage_path('le/'.$activity->id.'/'.$tradedealscheme->id.'/'.$folder_name));
	}

	private static function generateIndividualSiteAllocation($tradedealscheme, $tradedeal, $activity, $host_sku, $scheme_uom_abv, $scheme_uom_abv2){
		$folder_name = self::getIndFileName($tradedealscheme, $scheme_uom_abv2, $host_sku);
    	$header = array('Promotion ID', 'Site ID', 'Budget', 'Currency', 'Quota', 'UoM');
    	$allocations = TradedealSchemeAllocation::getAllocation($tradedealscheme, $host_sku);

        Excel::create($folder_name. ' - 4 Site Allocation', function($excel) use ($header, $allocations){
            $excel->sheet('Sheet1', function($sheet) use ($header, $allocations) {
                $sheet->row(1, $header);
                $row = 2;
                foreach ($allocations as $value) {
                    if($value->final_pcs > 0){
                        $site_id = $value->plant_code;
                        $row_data = array($value->scheme_code, $site_id, '', '', $value->final_pcs, 'SET');
                        $sheet->row($row, $row_data);
                        $row++;
                    }
                }
            });
        })->store('csv', storage_path('le/'.$activity->id.'/'.$tradedealscheme->id.'/'.$folder_name));
	}

	private static function generateCollective($tradedealscheme, $tradedeal, $activity, $host_skus, $scheme_uom_abv, $scheme_uom_abv2){
		set_time_limit(0);
		$deal_id = TradedealSchemeAllocation::getCollecttiveSchemeCode($tradedealscheme);
		$folder_name = $deal_id->scheme_code.' - '.$tradedealscheme->buy.'+'.$tradedealscheme->free.' '.$scheme_uom_abv2.' ';
		$h_brands = [];
		foreach ($host_skus as $host) {
			$h_brands[] = $host->brand_shortcut;
		}

		$x_brand = array_unique($h_brands);

		$folder_name .= implode("_", $x_brand);	

    	$header = array('Promotion Number', 'Promotion Description', 'U2K2 I/O No', 'Start Date', 'End Date',
		    		'BUY Quota', 'Quota UOM', 'GET Quota', 'Quota UOM', 'Quantity Type', 'Header Qty', 'Site', 'Site BUY Quota',
		    		'Site GET Quota', 'Level', 'Cluster', 'outlet type', 'Outlet sub typ', 'Perf store',
		    		'Buy Mat 1', 'Buy Qty 1','Buy Mat 2', 'Buy Qty 2','Buy Mat 3', 'Buy Qty 3','Buy Mat 4', 'Buy Qty 4',
		    		'Buy Mat 5', 'Buy Qty 5','Buy Mat 6', 'Buy Qty 6','Buy Mat 7', 'Buy Qty 7','Buy Mat 8', 'Buy Qty 8',
		    		'Buy Mat 9', 'Buy Qty 9','Buy Mat 10', 'Buy Qty 10',
		    		'Get Mat 1', 'Get Qty 1','Get Mat 2', 'Get Qty 2','Get Mat 3', 'Get Qty 3','Get Mat 4', 'Get Qty 4',
		    		'Get Mat 5', 'Get Qty 5','Get Mat 6', 'Get Qty 6','Get Mat 7', 'Get Qty 7','Get Mat 8', 'Get Qty 8',
		    		'Get Mat 9', 'Get Qty 9','Get Mat 10', 'Get Qty 10');
 

    	$brands =[];

		$sub_types = TradedealSchemeSubType::getSchemeSubtypes($tradedealscheme);

    	$budgets = ActivityBudget::getBudgets($activity->id);
    	$io_number = 'NO IO';
    	if(!empty($budgets)){
    		if(isset($budgets[0])){
    			$io_number = $budgets[0]->io_number;
    		}	
    	}

    	$start_date = date('d/m/Y', strtotime($activity->eimplementation_date));
    	$end_date = date('d/m/Y', strtotime($activity->end_date));

    	$total_deals = TradedealSchemeAllocation::where('tradedeal_scheme_id', $tradedealscheme->id)->sum('final_pcs');

		$header_qty = $tradedealscheme->buy;
		$get_mat = $tradedealscheme->free;
		if($tradedealscheme->tradedeal_uom_id == 2){
    		$header_qty = $tradedealscheme->buy * 12;
    		$get_mat = $tradedealscheme->free * 12;
    	}

    	if($tradedealscheme->tradedeal_uom_id == 3){
    		// problem with multiple host in level 3 collective
    		// use lowest pr host sku
    		$header_qty = $tradedealscheme->buy * $host_skus[0]->host_pcs_case;
            $get_mat = $tradedealscheme->free * $tradedealscheme->pre_pcs_case;
    	}

    	$allocations = TradedealSchemeAllocation::getCollectiveAllocation($tradedealscheme);
    	$materials = TradedealSchemeSku::getHostSku($tradedealscheme);
    	$sub_types = TradedealSchemeSubType::getSchemeSubtypes($tradedealscheme);


        Excel::create($folder_name, function($excel) use ($header, $allocations, $tradedealscheme, $sub_types, $materials, $io_number, $start_date, $end_date, $total_deals, $header_qty, $get_mat){
            $excel->sheet('Sheet1', function($sheet) use ($header, $allocations, $sub_types, $materials, $tradedealscheme, $io_number, $start_date, $end_date, $total_deals, $header_qty, $get_mat) {
                // $sheet->setColumnFormat(array(
                //     'D' => 'yyyy-mm-dd'
                // ));
                $sheet->setColumnFormat(array(
                    // 'A' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER, 
                    // 'B' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,       
                    // 'C' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,       
                    'D' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,  
                    'E' => PHPExcel_Style_NumberFormat::FORMAT_TEXT,  
                    // 'F' => PHPExcel_Style_NumberFormat::FORMAT_NUMBER     
                ));
                $sheet->row(1, $header);
                // $s = explode('/', $start_date);
                // $s_date = implode("/", $s);
                // $e_date = '\"02/02/2017\"';
                $row = 2;
                foreach ($allocations as $value) {
                     if($value->final_pcs > 0){
                         foreach ($sub_types as $sub_type) {
                             foreach ($materials as $mat) {
                                 $row_data = array($value->scheme_code,
                                     $value->scheme_desc, 
                                     $io_number, 
                                     (string)$start_date, 
                                     (string)$end_date, 
                                     $total_deals, 
                                     'PC', 
                                     $total_deals, 
                                     'PC', 
                                     'C',
                                     $header_qty, 
                                     $value->plant_code, 
                                     $value->final_pcs, 
                                     $value->final_pcs, 
                                     'A920- Country/Site/Outlet Sub Type',
                                     '', 
                                     '', 
                                     $sub_type->sub_type,
                                     '', 
                                     $mat->host_code, 
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     $tradedealscheme->pre_code,
                                     $get_mat,
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     );
                                $sheet->row($row, $row_data);
                                $row++; 
                             }
                         }
                     }
                }
            });
        })->store('csv', storage_path('le/'.$activity->id.'/'.$tradedealscheme->id.'/'.$folder_name));
    	
    	// foreach ($allocations as $value) {
    	// 	if($value->final_pcs > 0){
    	// 		foreach ($sub_types as $sub_type) {
    	// 			foreach ($materials as $mat) {
    	// 				$row_data = array($value->scheme_code,
    	// 				 	$value->scheme_desc, 
    	// 				 	$io_number, 
    	// 				 	$start_date, 
    	// 				 	$end_date, 
    	// 				 	$total_deals, 
    	// 				 	'PC', 
    	// 				 	$total_deals, 
    	// 				 	'PC', 
    	// 				 	'C',
			  //   			$header_qty, 
			  //   			$value->plant_code, 
			  //   			$value->final_pcs, 
			  //   			$value->final_pcs, 
			  //   			'A920- Country/Site/Outlet Sub Type',
			  //   			'', 
			  //   			'', 
			  //   			$sub_type->sub_type,
			  //   			'', 
			  //   			$mat->host_code, 
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			$tradedealscheme->pre_code,
			  //   			$get_mat,
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			'',
			  //   			);

    	// 				$contents = implode(',', $row_data);
					// 	File::append($file, $contents.PHP_EOL);
    	// 			}
	    // 		}
    	// 	}
    	// }
	}
}