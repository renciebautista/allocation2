<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeFieldTradedeal extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:fieldtradedeal';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
		$id = $this->argument('id');

		$activity = Activity::find($id);
		if(!empty($activity)){
			$tradedeal = Tradedeal::getActivityTradeDeal($activity);
			if(!empty($tradedeal)){
				$filepath = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
				Excel::create($activity->circular_name. ' BBFEE', function($excel) use($activity){
					$excel->sheet('SCHEME SUMMARY', function($sheet) use ($activity) {
						$sheet->setCellValueByColumnAndRow(0,1, 'Activity Title:');
						$sheet->setCellValueByColumnAndRow(1,1, $activity->circular_name);
						$sheet->setCellValueByColumnAndRow(0,2, 'Start Date:');
						$sheet->setCellValueByColumnAndRow(1,2, date('d/m/Y', strtotime($activity->eimplementation_date)));
						$sheet->setCellValueByColumnAndRow(0,3, 'End Date:');
						$sheet->setCellValueByColumnAndRow(1,3, date('d/m/Y', strtotime($activity->end_date)));

						$sheet->row(5, array('ACTIVITY', 'Scheme Code', 'Scheme Description', 'HOST CODE', 'HOST DESCRIPTION', 'PREMIUM CODE / PIMS CODE', 'Premium SKU', 'Master Outlet Subtype Name', 'Master Outlet Subtype Code'));
						$sheet->getStyle("A5:I5")->getFont()->setBold(true);
						$row = 6;

						$tradedeal = Tradedeal::getActivityTradeDeal($activity);
						$tradedealschemes = TradedealScheme::where('tradedeal_id',$tradedeal->id)
							->orderBy('tradedeal_type_id')
							->orderBy('tradedeal_uom_id')
							->get();
						foreach ($tradedealschemes as $scheme) {
								$sheet->setCellValueByColumnAndRow(0,$row, $scheme->name);
								if($scheme->tradedeal_type_id == 1){
									$host_skus = TradedealSchemeSku::getHostSku($scheme);
									$sku_cnt = count($host_skus);
									foreach ($host_skus as $key => $host_sku) {
										$deal_id = TradedealSchemeAllocation::getSchemeCode($scheme, $host_sku);
										$sheet->setCellValueByColumnAndRow(1,$row, $deal_id->scheme_code);
										$sheet->setCellValueByColumnAndRow(2,$row, $deal_id->scheme_desc);
										$sheet->setCellValueByColumnAndRow(3,$row, $host_sku->host_code);
										$sheet->setCellValueByColumnAndRow(4,$row, $host_sku->host_desc. ' '.$host_sku->variant);
										$sheet->setCellValueByColumnAndRow(5,$row, $host_sku->pre_code);
										$sheet->setCellValueByColumnAndRow(6,$row, $host_sku->pre_desc. ' '.$host_sku->pre_variant);	
										$row++;	
									}
									$row = $row - $sku_cnt;
									$channels = TradedealSchemeSubType::getSchemeSubtypes($scheme);
									$ch_cnt = count($channels);
									foreach ($channels as $channel) {
										$sheet->setCellValueByColumnAndRow(7,$row, $channel->sub_type_desc);
										$sheet->setCellValueByColumnAndRow(8,$row, $channel->sub_type);
										$row++;
									}

									if($ch_cnt < $sku_cnt){
										$x = $sku_cnt - $ch_cnt;
										$row = $row + $x;
									}
								}
								if($scheme->tradedeal_type_id == 2){
									
								}

								if($scheme->tradedeal_type_id == 3){
									$host_skus = TradedealSchemeSku::getHostSku($scheme);
									$deal_id = TradedealSchemeAllocation::getCollecttiveSchemeCode($scheme);
									$sheet->setCellValueByColumnAndRow(1,$row, $deal_id->scheme_code);
									$sheet->setCellValueByColumnAndRow(2,$row, $deal_id->scheme_desc);
									$host_skus = TradedealSchemeSku::getHostSku($scheme);
									$sku_cnt = count($host_skus);
									foreach ($host_skus as $key => $host_sku) {
										$sheet->setCellValueByColumnAndRow(3,$row, $host_sku->host_code);
										$sheet->setCellValueByColumnAndRow(4,$row, $host_sku->host_desc. ' '.$host_sku->variant);	
										$row++;	
									}
									$row = $row - $sku_cnt;

									if($tradedeal->non_ulp_premium){
										$sheet->setCellValueByColumnAndRow(5,$row, $scheme->pre_code);
										$sheet->setCellValueByColumnAndRow(6,$row, $scheme->pre_desc .' '.$scheme->pre_variant);
									}else{
										$part_sku = TradedealPartSku::find($scheme->pre_id);
										$sheet->setCellValueByColumnAndRow(5,$row, $part_sku->pre_code);
										$sheet->setCellValueByColumnAndRow(6,$row, $part_sku->pre_desc .' '.$part_sku->pre_variant);
									}

									$channels = TradedealSchemeSubType::getSchemeSubtypes($scheme);
									$ch_cnt = count($channels);
									foreach ($channels as $channel) {
										$sheet->setCellValueByColumnAndRow(7,$row, $channel->sub_type_desc);
										$sheet->setCellValueByColumnAndRow(8,$row, $channel->sub_type);
										$row++;
									}

									if($ch_cnt < $sku_cnt){
										$x = $sku_cnt - $ch_cnt;
										$row = $row + $x;
									}
									
								}					
						}
				    });

				    $excel->sheet('ALLOCATIONS', function($sheet) use ($activity) {
		    	
						$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->groupBy('pre_code')->get();
						$tradedeal = Tradedeal::getActivityTradeDeal($activity);
						$allocations = TradedealSchemeAllocation::exportAlloc($tradedeal);

						$sheet->setWidth('A', 16);
						$sheet->setWidth('B', 13);
						$sheet->setWidth('C', 20);
						$sheet->setWidth('D', 10);
						$sheet->setWidth('E', 30);
						$sheet->setWidth('F', 15);
						$sheet->setWidth('G', 20);
						$sheet->setWidth('H', 5);

						$row = 2;
						$sheet->row($row, array('AREA', 'Distributor Code', 'Distributor Name', 'Site Code', 'Site Name', 'Scheme Code', 'Scheme Description', 'UOM'));


						$sheet->getDefaultStyle()
						    ->getAlignment()
						    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

						// premuim
						$premiums = [];
						foreach ($tradedeal_skus as $sku) {
							$premiums[] = $sku->pre_desc. ' '. $sku->pre_variant;
						}

						$col = 8;
						$col_pre = [];
						$col_pre_x = [];
						$sheet->setCellValueByColumnAndRow($col,1, 'DEALS');
						foreach ($premiums as $premuim) {
							$sheet->setCellValueByColumnAndRow($col,2, $premuim);
							$sheet->setWidth(PHPExcel_Cell::stringFromColumnIndex($col), 10);
							$col_pre[$premuim] = $col;
							$col++;
						}
						$style = array(
					        'alignment' => array(
					            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					        )
					    );

					    

						$d_col = $col -1;
						$sheet->mergeCells(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1');
						$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1')
							->applyFromArray(array(
						    'fill' => array(
						        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '091462')
						    )
						));

						$sheet->cells(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1', function($cells) {
							$cells->setFontColor('#ffffff');
						});

						$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1')->applyFromArray($style);


						$sheet->setCellValueByColumnAndRow($col,1, 'PREMIUMS');
						foreach ($premiums as $premuim) {
							$sheet->setCellValueByColumnAndRow($col,2, $premuim);
							$col_pre_x[$premuim] = $col;
							$col++;
						}
						$d_col++;

						$p_col = $col - 1;

						$sheet->getStyle('A2:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'2')
							->getFont()
							->setBold(true);
						
						// Set background color for a specific cell
						$sheet->getStyle('A2:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'2')->applyFromArray(array(
						    'fill' => array(
						        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => 'DAEBF8')
						    )
						));

						$sheet->mergeCells(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1');
						$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1')
							->applyFromArray(array(
						    'fill' => array(
						        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => '7F00A1')
						    )
						));

						$sheet->cells(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1', function($cells) {
							$cells->setFontColor('#ffffff');
						});


						$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1')->applyFromArray($style);

						$sheet->getStyle('I2:R2')->getAlignment()
							->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER))
							->setWrapText(true);

						$sheet->setWidth(\PHPExcel_Cell::stringFromColumnIndex($col), 10);


						$last_area = '';
						$last_distributor = '';
						$last_site = '';
						$first_row = 3;
						foreach ($allocations as $alloc) {
							$row++;
							if($alloc->tradedeal_uom_id == 1){
								$pcs_deal = 1;
							}
							if($alloc->tradedeal_uom_id == 2){
								$pcs_deal = 12;
							}
							if($alloc->tradedeal_uom_id == 3){
								$pcs_deal = $alloc->pcs_case;
							}
							
							if($last_area == $alloc->area){
								if($last_distributor == $alloc->sold_to_code){

									if($last_site == $alloc->plant_code){
										$sheet->row($row, ['', '', '', '', '', $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
									}else{
										$sheet->row($row, ['', '', '', $last_site.' Total']);
										foreach ($col_pre as $col) {
											$last_row = $row - 1;
											$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
											$sheet->setCellValueByColumnAndRow($col,$row,$sum);
										}

										foreach ($col_pre_x as $col) {
											$last_row = $row - 1;
											$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
											$sheet->setCellValueByColumnAndRow($col,$row,$sum);
										}

										$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
										    'fill' => array(
										        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
										        'color' => array('rgb' => 'DAEBF8')
										    )
										));

										$row++;
										$first_row = $row;
										$sheet->row($row, ['', '', '', $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
									}
									$sheet->setCellValueByColumnAndRow($col_pre[$alloc->pre_desc_variant],$row, $alloc->final_pcs / $pcs_deal);
									$sheet->setCellValueByColumnAndRow($col_pre_x[$alloc->pre_desc_variant],$row, $alloc->final_pcs);
								}else{
									if($last_site != $alloc->plant_code){
										$sheet->row($row, ['', '', '', $last_site.' Total']);
										foreach ($col_pre as $col) {
											$last_row = $row - 1;
											$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
											$sheet->setCellValueByColumnAndRow($col,$row,$sum);
										}

										foreach ($col_pre_x as $col) {
											$last_row = $row - 1;
											$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
											$sheet->setCellValueByColumnAndRow($col,$row,$sum);
										}

										$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
										    'fill' => array(
										        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
										        'color' => array('rgb' => 'DAEBF8')
										    )
										));

										$row++;
										$first_row = $row;
									}

									if($last_distributor != $alloc->sold_to_code){
										if($alloc->plant_code == ''){
											$sheet->row($row, ['', '', '', $last_distributor.' Total']);
											foreach ($col_pre as $col) {
												$last_row = $row - 1;
												$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
												$sheet->setCellValueByColumnAndRow($col,$row,$sum);
											}

											foreach ($col_pre_x as $col) {
												$last_row = $row - 1;
												$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
												$sheet->setCellValueByColumnAndRow($col,$row,$sum);
											}

											$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
											    'fill' => array(
											        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
											        'color' => array('rgb' => 'DAEBF8')
											    )
											));

											$row++;
											$first_row = $row;
										}
									}

									$sheet->row($row, ['', $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
									$sheet->setCellValueByColumnAndRow($col_pre[$alloc->pre_desc_variant],$row, $alloc->final_pcs / $pcs_deal);
									$sheet->setCellValueByColumnAndRow($col_pre_x[$alloc->pre_desc_variant],$row, $alloc->final_pcs);



								}
							}else{
								if(($last_site != $alloc->plant_code) && ($last_site != '')){
									$sheet->row($row, ['', '', '', $last_site.' Total']);
									foreach ($col_pre as $col) {
										$last_row = $row - 1;
										$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
										$sheet->setCellValueByColumnAndRow($col,$row,$sum);
									}

									foreach ($col_pre_x as $col) {
										$last_row = $row - 1;
										$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
										$sheet->setCellValueByColumnAndRow($col,$row,$sum);
									}

									$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
									    'fill' => array(
									        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
									        'color' => array('rgb' => 'DAEBF8')
									    )
									));

									$row++;
									$first_row = $row;
								}else{
									if(!empty($last_distributor)){
										$sheet->row($row, ['', '', '', $last_distributor.' Total']);
										foreach ($col_pre as $col) {
											$last_row = $row - 1;
											$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
											$sheet->setCellValueByColumnAndRow($col,$row,$sum);
										}

										foreach ($col_pre_x as $col) {
											$last_row = $row - 1;
											$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
											$sheet->setCellValueByColumnAndRow($col,$row,$sum);
										}

										$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
										    'fill' => array(
										        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
										        'color' => array('rgb' => 'DAEBF8')
										    )
										));

										$row++;
										$first_row = $row;
									}
									
								}

								$sheet->row($row, [$alloc->area, $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
								$sheet->setCellValueByColumnAndRow($col_pre[$alloc->pre_desc_variant],$row, $alloc->final_pcs / $pcs_deal);
								$sheet->setCellValueByColumnAndRow($col_pre_x[$alloc->pre_desc_variant],$row, $alloc->final_pcs);		
							}

							$last_area = $alloc->area;
							$last_distributor = $alloc->sold_to_code;
							$last_site = $alloc->plant_code;
						}

						$row++;
						$sheet->row($row, ['', '', '', $last_site.' Total']);	
						
						foreach ($col_pre as $col) {
							$last_row = $row - 1;
							$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
							$sheet->setCellValueByColumnAndRow($col,$row,$sum);
						}	

						foreach ($col_pre_x as $col) {
							$last_row = $row - 1;
							$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
							$sheet->setCellValueByColumnAndRow($col,$row,$sum);
						}		

						$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
						    'fill' => array(
						        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
						        'color' => array('rgb' => 'DAEBF8')
						    )
						));
				    });
			
					$excel->sheet('OUTPUT FILE', function($sheet) use ($activity) {
						$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->groupBy('pre_code')->get();
						$tradedeal = Tradedeal::getActivityTradeDeal($activity);
						$allocations = TradedealSchemeAllocation::exportAlloc($tradedeal);

						$row = 1;
						$sheet->row($row, array('AREA', 'Distributor Code', 'Distributor Name', 'Site Code', 'Site Name', 'Scheme Code', 'Scheme Description',
							'Promo Description', 'Promo Type',
				    		'SKU Codes Involved', 'SKUs Involved', 'Premium Code', 'Premium',
				    		'Outlet Sub Types Involved', 'Outlet Codes', 'Allocation (Pieces)', 'UOM', 'Source of Premium', 
				    		'Start Date', 'End Date'));

						$sheet->getStyle("A1:V1")->getFont()->setBold(true);
						$sheet->getDefaultStyle()
						    ->getAlignment()
						    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					
						$last_area = '';
						$last_distributor = '';
						$last_site = '';
						$first_row = 3;
						foreach ($allocations as $alloc) {

							$scheme = TradedealScheme::find($alloc->tradedeal_scheme_id);

							$row++;
							if($alloc->tradedeal_uom_id == 1){
								$pcs_deal = 1;
							}
							if($alloc->tradedeal_uom_id == 2){
								$pcs_deal = 12;
							}
							if($alloc->tradedeal_uom_id == 3){
								$pcs_deal = $alloc->pcs_case;
							}

							$start_date = date('d/m/Y', strtotime($alloc->eimplementation_date));
				    		$end_date = date('d/m/Y', strtotime($alloc->end_date));

				    		$host_code = '';
					    	$host_desc = '';

					    	if($alloc->tradedeal_scheme_sku_id != 0){
					    		$host_sku = TradedealSchemeSku::getHost($alloc->tradedeal_scheme_sku_id);
					    		$host_code = $host_sku->host_code;
					    		$host_desc = $host_sku->host_desc;
					    	}else{

					    		$host_skus = TradedealSchemeSku::getHostSku($scheme);
					    		$code = [];
					    		$desc = [];
					    		foreach ($host_skus as $key => $value) {

					    			$code[] = $value->host_code;
					    			$desc[] = $value->host_desc;
					    		}

					    		$host_code = implode("; ", $code);
					    		$host_desc = implode("; ", $desc);

					    	}

					    	$pre_code = $alloc->pre_code;
					    	$pre_desc = $alloc->pre_desc;
					    	$source = 'Ex-DT';
					    	if(!$alloc->non_ulp_premium){
					    		$source = 'Ex-ULP';
					    	}else{
					    		
					    	}

					    	$channels = TradedealSchemeSubType::getSchemeSubtypes($scheme);
					    	$ch_code = [];
					    	$ch_desc = [];
					    	foreach ($channels as $channel) {
				    			$ch_code[] = $channel->sub_type;
				    			$ch_desc[] = $channel->sub_type_desc;
				    		}

				    		$channel_code = implode("; ", $ch_code);
					    	$channel_desc = implode("; ", $ch_desc);
							
							// if($last_area == $alloc->area){
							// 	if($last_distributor == $alloc->sold_to_code){
							// 		if($last_site == $alloc->plant_code){
							// 			$sheet->row($row, ['', '', '', '', '', $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type,$host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
							// 		}else{
							// 			// $row++;
							// 			$first_row = $row;
							// 			$sheet->row($row, ['', '', '', $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type, $host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
							// 		}
									
							// 	}else{
							// 		if($last_site != $alloc->plant_code){
							// 			// $row++;
							// 			$first_row = $row;
							// 		}
							// 		$sheet->row($row, ['', $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type, $host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
									
							// 	}
							// }else{
							// 	if(($last_site != $alloc->plant_code) && ($last_site != '')){
							// 		// $row++;
							// 		$first_row = $row;
							// 	}
								$sheet->row($row, [$alloc->area, $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type, $host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
						
							// }

							// $last_area = $alloc->area;
							// $last_distributor = $alloc->sold_to_code;
							// $last_site = $alloc->plant_code;
						}

				    });
				})->store('xls',storage_path().$filepath);

				$this->line(storage_path().$filepath);
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('id', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

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
