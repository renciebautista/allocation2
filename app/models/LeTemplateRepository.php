<?php

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;


class LeTemplateRepository  {

	public static function generateTemplate($tradedealscheme){
		$tradedeal = Tradedeal::findorFail($tradedealscheme->tradedeal_id);
		$activity = Activity::findorFail($tradedeal->activity_id);

		if($tradedealscheme->tradedeal_type_id == 1){
			$host_skus = TradedealSchemeSku::getHostSku($tradedealscheme);
			foreach ($host_skus as $host_sku) {
				self::generateIndividualHeader($tradedealscheme, $tradedeal, $activity, $host_sku);
				self::generateIndividualMechanics($tradedealscheme, $tradedeal, $activity, $host_sku);
			}
			
		}

		if($tradedealscheme->tradedeal_type_id == 2){
			
		}

		if($tradedealscheme->tradedeal_type_id == 3){
			$host_sku = TradedealPartSku::find($tradedealscheme->pre_id);
			self::generateCollectiveAnyHeader($tradedealscheme, $tradedeal, $activity, $host_sku);
		}
	}

	private static function generateIndividualHeader($tradedealscheme, $tradedeal, $activity, $host_sku){
		Excel::create($tradedealscheme->dealType->tradedeal_type. ' - ' . $host_sku->host_desc. ' - 1 Header', function($excel) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    $excel->sheet('Sheet1', function($sheet) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    	$allocations = TradedealSchemeAllocation::getAllocation($tradedealscheme, $host_sku);

		    	if($tradedeal->nonUlpPremium()){
		    		$header = array('DEAL ID', 'PROMO TYPE', 'IONUMBER', 'START DATE',
			    	 	'END DATE', 'DEAL DESCRIPTION', 'DEAL AMOUNT', 'SALES ORG', 
			    	 	'DISTRIBUTOR ID', 'ALLOCATED BUDGET', 'Non Unilever Flag');
		    		
		    	}else{
		    		$header = array('DEAL ID', 'IONUMBER', 'START DATE',
			    	 	'END DATE', 'DEAL DESCRIPTION', 'DEAL AMOUNT', 'SALES ORG', 
			    	 	'DISTRIBUTOR ID', 'ALLOCATED BUDGET');
		    	}

		    	$sheet->row(1, $header);

		    	$row = 2;
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

		    	$brand = $host_sku->brand_shortcut;
		    	$deal_id = 'B'.date('ym').$scheme_uom_abv.$brand.$tradedealscheme->id;

		    	$budgets = ActivityBudget::getBudgets($activity->id);
		    	$io_number = '';
		    	if(!empty($budgets)){
		    		if(isset($budgets[0])){
		    			$io_number = $budgets[0]->io_number;
		    		}
		    		
		    	}
		    	
		    	$start_date = date('d.m.Y', strtotime($activity->eimplementation_date));
		    	$end_date = date('d.m.Y', strtotime($activity->end_date));
		    	$deal_desc = $tradedealscheme->dealType->tradedeal_type . ' ' .$tradedealscheme->buy.'+'.$tradedealscheme->free.' '.$host_sku->host_desc.' + '.$host_sku->pre_desc;
		    	$total_deals = TradedealSchemeAllocation::getTotalDeals($tradedealscheme,$host_sku);
		    	$deal_amount =  number_format($total_deals * $host_sku->pre_cost, 2, '.', '');
		    	foreach ($allocations as $value) {
		    		if($tradedeal->nonUlpPremium()){
			    		$row_data = array($deal_id, 'BBFREE', $io_number,$start_date, $end_date, $deal_desc, 
			    			$deal_amount, 'P001', $value->plant_code, 
			    			number_format($value->computed_pcs * $host_sku->pre_cost, 2, '.', ''), 'X');	
			    	}else{
			    		$row_data = array($deal_id,$io_number,$start_date, $end_date, $deal_desc, 
			    			$deal_amount, 'P001', $value->ship_to_code, 
			    			number_format($value->computed_pcs * $host_sku->pre_cost, 2, '.', ''));
			    	}

		    		$sheet->row($row, $row_data);
					$row++;
		    	}
		    });
		})->store('csv', storage_path('le/'.$tradedealscheme->id));
	}

	private static function generateIndividualMechanics($tradedealscheme, $tradedeal, $activity, $host_sku){
		Excel::create($tradedealscheme->dealType->tradedeal_type. ' - ' . $host_sku->host_desc. ' - 2 Mechanics', function($excel) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    $excel->sheet('Sheet1', function($sheet) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    	

		    	$sub_types = TradedealSchemeChannel::getSelectedDetails($tradedealscheme);

		    	$header = array('Promotion ID', 'Disc Seq', 'Buy Product', 'Buy Type (Value/Volume)',
		    			'Min Buy', 'Min Buy  UoM/Curr', 'Get Type', 'Get Material', 
		    			'Get Qty.', 'Get UOM', 'Outlet Type', 'Outlet Sub Type', 'Promotion Id', 'Exclude outlet');

		    	$sheet->row(1, $header);

		    	
		    	$scheme_uom_abv;
		    	if($tradedealscheme->tradedeal_uom_id == 1){
		    		$scheme_uom_abv = 'P';
		    	}
		    	if($tradedealscheme->tradedeal_uom_id == 2){
		    		$scheme_uom_abv = 'D';
		    	}
		    	if($tradedealscheme->tradedeal_uom_id == 3){
		    		$scheme_uom_abv = 'C';
		    	}

		    	$brand = $host_sku->brand_shortcut;
		    	$deal_id = 'B'.date('ym').$scheme_uom_abv.$brand.$tradedealscheme->id;

		    	$min_buy = $tradedealscheme->buy;
		    	if($tradedealscheme->tradedeal_type_id == 2){
		    		$min_buy = $tradedealscheme->buy * 12;
		    	}

		    	if($tradedealscheme->tradedeal_type_id == 3){
		    		$min_buy = $tradedealscheme->buy * $host_sku->host_pcs_case;
		    	}

		    	if($tradedeal->nonUlpPremium()){
		    		$free = $tradedealscheme->free;
		    	}else{
		    		$free = $tradedealscheme->free;
			    		if($tradedealscheme->tradedeal_type_id == 2){
			    		$free = $tradedealscheme->free * 12;
			    	}

			    	if($tradedealscheme->tradedeal_type_id == 3){
			    		$free = $tradedealscheme->free * $host_sku->pre_pcs_case;
			    	}
		    	}
		    	$first_row = true;
		    	$row = 2;
		    	foreach ($sub_types as $value) {
		    		if($first_row){
		    			$row_data = array($deal_id, '1', $host_sku->host_code,'Volume', $min_buy, 'PC',
				    		'O - AND', $host_sku->pre_code, $free, 'PC', '', $value->l5_code, '', '' );
				    	$first_row = false;
		    		}else{
		    			$row_data = array('', '', '','', '', '',
				    		'', '', '', '', '', $value->l5_code, '', '' );
				    	$first_row = false;
		    		}

		    		$sheet->row($row, $row_data);
		    		
					$row++;
		    	}
		    });
		})->store('csv', storage_path('le/'.$tradedealscheme->id));
	}

	private static function generateSiteAllocation($tradedealscheme, $tradedeal, $activity, $host_sku){
		Excel::create($tradedealscheme->dealType->tradedeal_type. ' - ' . $host_sku->host_desc. ' - 4 Site Allocation', function($excel) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    $excel->sheet('Sheet1', function($sheet) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    	

		    	$sub_types = TradedealSchemeChannel::getSelectedDetails($tradedealscheme);

		    	$header = array('Promotion ID', 'Disc Seq', 'Buy Product', 'Buy Type (Value/Volume)',
		    			'Min Buy', 'Min Buy  UoM/Curr', 'Get Type', 'Get Material', 
		    			'Get Qty.', 'Get UOM', 'Outlet Type', 'Outlet Sub Type', 'Promotion Id', 'Exclude outlet');

		    	$sheet->row(1, $header);

		    	
		    	$scheme_uom_abv;
		    	if($tradedealscheme->tradedeal_uom_id == 1){
		    		$scheme_uom_abv = 'P';
		    	}
		    	if($tradedealscheme->tradedeal_uom_id == 2){
		    		$scheme_uom_abv = 'D';
		    	}
		    	if($tradedealscheme->tradedeal_uom_id == 3){
		    		$scheme_uom_abv = 'C';
		    	}

		    	$brand = $host_sku->brand_shortcut;
		    	$deal_id = 'B'.date('ym').$scheme_uom_abv.$brand.$tradedealscheme->id;

		    	$min_buy = $tradedealscheme->buy;
		    	if($tradedealscheme->tradedeal_type_id == 2){
		    		$min_buy = $tradedealscheme->buy * 12;
		    	}

		    	if($tradedealscheme->tradedeal_type_id == 3){
		    		$min_buy = $tradedealscheme->buy * $host_sku->host_pcs_case;
		    	}

		    	if($tradedeal->nonUlpPremium()){
		    		$free = $tradedealscheme->free;
		    	}else{
		    		$free = $tradedealscheme->free;
			    		if($tradedealscheme->tradedeal_type_id == 2){
			    		$free = $tradedealscheme->free * 12;
			    	}

			    	if($tradedealscheme->tradedeal_type_id == 3){
			    		$free = $tradedealscheme->free * $host_sku->pre_pcs_case;
			    	}
		    	}
		    	$first_row = true;
		    	$row = 2;
		    	foreach ($sub_types as $value) {
		    		if($first_row){
		    			$row_data = array($deal_id, '1', $host_sku->host_code,'Volume', $min_buy, 'PC',
				    		'O - AND', $host_sku->pre_code, $free, 'PC', '', $value->l5_code, '', '' );
				    	$first_row = false;
		    		}else{
		    			$row_data = array('', '', '','', '', '',
				    		'', '', '', '', '', $value->l5_code, '', '' );
				    	$first_row = false;
		    		}

		    		$sheet->row($row, $row_data);
		    		
					$row++;
		    	}
		    });
		})->store('csv', storage_path('le/'.$tradedealscheme->id));
	}

	private static function generateCollectiveAnyHeader($tradedealscheme, $tradedeal, $activity, $host_sku){
		Excel::create($tradedealscheme->dealType->tradedeal_type. ' - ' . $host_sku->host_desc. ' - 1 Header', function($excel) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    $excel->sheet('Sheet1', function($sheet) use ($tradedealscheme, $tradedeal, $activity, $host_sku) {
		    	$allocations = TradedealSchemeAllocation::getCollectiveAllocation($tradedealscheme);

		    	$header = array('Promotion Number', 'Promotion Description', 'U2K2 I/O No', 'Start Date', 'End Date',
		    		'BUY Quota', 'Quota UOM', 'GET Quota', 'Quota UOM', 'Quantity Type', 'Header Qty', 'Site', 'Site BUY Quota',
		    		'Site GET Quota', 'Level', 'Cluster', 'outlet type', 'Outlet sub typ', 'Perf store',
		    		'Buy Mat 1', 'Buy Qty 1','Buy Mat 2', 'Buy Qty 2','Buy Mat 3', 'Buy Qty 3','Buy Mat 4', 'Buy Qty 4',
		    		'Buy Mat 5', 'Buy Qty 5','Buy Mat 6', 'Buy Qty 6','Buy Mat 7', 'Buy Qty 7','Buy Mat 8', 'Buy Qty 8',
		    		'Buy Mat 9', 'Buy Qty 9','Buy Mat 10', 'Buy Qty 10',
		    		'Get Mat 1', 'Get Qty 1','Get Mat 2', 'Get Qty 2','Get Mat 3', 'Get Qty 3','Get Mat 4', 'Get Qty 4',
		    		'Get Mat 5', 'Get Qty 5','Get Mat 6', 'Get Qty 6','Get Mat 7', 'Get Qty 7','Get Mat 8', 'Get Qty 8',
		    		'Get Mat 9', 'Get Qty 9','Get Mat 10', 'Get Qty 10');

		    	$sheet->row(1, $header);
 	
		    	$scheme_uom_abv;
		    	if($tradedealscheme->tradedeal_uom_id == 1){
		    		$scheme_uom_abv = 'P';
		    	}
		    	if($tradedealscheme->tradedeal_uom_id == 2){
		    		$scheme_uom_abv = 'D';
		    	}
		    	if($tradedealscheme->tradedeal_uom_id == 3){
		    		$scheme_uom_abv = 'C';
		    	}

		    	// $brand = $host_sku->brand_shortcut;
		    	$deal_id = date('ym').$scheme_uom_abv.$tradedealscheme->id;
		    	$pro_desc = 'C '. $tradedealscheme->buy.' + '.$tradedealscheme->free;

		    	$budgets = ActivityBudget::getBudgets($activity->id);
		    	$io_number = '';
		    	if(!empty($budgets)){
		    		if(isset($budgets[0])){
		    			$io_number = $budgets[0]->io_number;
		    		}
		    		
		    	}

		    	$start_date = date('d/m/Y', strtotime($activity->eimplementation_date));
		    	$end_date = date('d/m/Y', strtotime($activity->end_date));

		    	$total_deals = TradedealSchemeAllocation::where('tradedeal_scheme_id', $tradedealscheme->id)->sum('computed_pcs1');

		    	dd($total_deals);

				$header_qty = $tradedealscheme->buy;
				if($tradedealscheme->tradedeal_type_id == 2){
		    		$header_qty = $tradedealscheme->buy * 12;
		    	}

		    	if($tradedealscheme->tradedeal_type_id == 3){
		    		$header_qty = $tradedealscheme->buy * $host_sku->host_pcs_case;
		    	}

		    	$row = 2;
		    	foreach ($allocations as $value) {
		    		$row_data = array($deal_id, $pro_desc, $io_number, $start_date, $end_date, $total_deals * $tradedealscheme->free, 'PC', $total_deals, 'PC', 'C',
		    			$header_qty);

		    		$sheet->row($row, $row_data);
					$row++;
		    	}
		    });
		})->store('csv', storage_path('le/'.$tradedealscheme->id));
	}

}