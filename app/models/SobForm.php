<?php

class SobForm extends \Eloquent {
	protected $fillable = [];

	public static function download($year, $weekno, $filename){
		self::generate($year, $weekno, $filename)->export('xls');
	}

	public static function generate($year, $weekno, $filename){
		$schemes = AllocationSob::select('schemes.item_code', 'schemes.item_desc', 'allocation_sobs.scheme_id')
			->join('schemes', 'schemes.id', '=', 'allocation_sobs.scheme_id')
			->where('weekno', $weekno)
			->where('year', $year)
			->groupBy('scheme_id')
			->orderBy('allocation_sobs.scheme_id')
			->get();

		$soldtos = AllocationSob::select('sold_to_code', 'sold_to', 'allocation_sobs.ship_to_code', 'ship_to', 
			DB::raw('sum(allocation_sobs.allocation) as allocations'),
			'allocation_sobs.loading_date', 'allocation_sobs.receipt_date' ,'allocation_sobs.year',
			'allocation_sobs.weekno'
			)
			->join('allocations', 'allocations.id', '=', 'allocation_sobs.allocation_id')
			->where('weekno', $weekno)
			->where('year', $year)
			->groupBy('allocation_sobs.ship_to_code')
			->orderBy('allocation_sobs.id')
			->get();

		$allocations = array();
		foreach ($schemes as $scheme) {
		 	$allocations[$scheme->scheme_id] = AllocationSob::select(DB::raw('sum(allocation_sobs.allocation) as allocations'))
					->join('allocations', 'allocations.id', '=', 'allocation_sobs.allocation_id')
					->where('weekno', $weekno)
					->where('year', $year)
					->where('allocation_sobs.scheme_id', $scheme->scheme_id)
					->groupBy('allocation_sobs.ship_to_code')
					->orderBy('allocation_sobs.id')
					->get();
		 } 
		// Helper::print_r($allocations);
		// dd($allocations);

		return  Excel::create($filename, function($excel) use ($schemes,$soldtos, $weekno, $year, $allocations) {
			$excel->sheet('Sales Order', function($sheet)  use ($schemes, $soldtos, $weekno, $year, $allocations) {

				$sheet->setWidth('A', 30);
				$sheet->setWidth('B', 15);
				$sheet->setWidth('C', 16);
				$sheet->setWidth('D', 13);
				$sheet->setWidth('E', 13);
				$sheet->setWidth('F', 13);
				$sheet->setWidth('G', 13);
				$sheet->setWidth('H', 60);
				$sheet->setWidth('I', 13);

				$sheet->getStyle('K4:M4')->getAlignment()->setWrapText(true); 

				$sheet->setStyle(array(
				    'font' => array(
				        'name'      =>  'Times New Roman',
				        'size'      =>  11,
				    )
				));
				$sheet->cell('A1', function($cell) {
					$cell->setFont(array(
					    'size'       => '14',
					    'bold'       =>  true
					));
				});

				$sheet->cell('A2', function($cell) {
					$cell->setFontWeight('bold');
				});

				$sheet->cell('I1', function($cell) {
					$cell->setFontColor('#ff0000');
					$cell->setFontSize(12);
				});

				$sheet->cell('I2:I3', function($cell) {
					$cell->setFontSize(12);
				});

				$sheet->setHeight(4, 88);

		        $sheet->row(1, array('SOB FORM', '', '', '', '', '', '', '','Row 5 should include the word SPLIT, all item codes and TOTAL'));
		       	$sheet->row(2, array('Rev. 6/30/2006', '', '', '', '', '', '', '','ROW 8 is the start of SOB DATA;  ONE WORKSHEET : ONE FILE ONLY'));
		       	$sheet->row(3, array('', '', '', '', '', '', '', '','TOTAL AT THE BOTTOM SHOULD BE AT COLUMN D'));
		       	$sheet->row(4, array('Sales Org', 'RT', '', '', 'REMARKS'));
		       	$sheet->mergeCells('E4:G4');
		       	$sheet->row(5, array('PURCHASE', 'LOADING', 'RECEIPT', '', '', '', '', '', 'SHIP', 'SPLIT'));
		       	$sheet->row(6, array('ORDER#', 'DATE', 'DATE', 'WHSE', 'AREA', 'DIST', 'CUST#', 'CUSTOMER NAME', 'TO'));
		       	$sheet->row(7, array('', '', '',  'no receiveing / holiday'));
		       	$sheet->mergeCells('D7:E7');

		       	$sheet->cells('A4:J4', function($cells) {
					$cells->setFontWeight('bold');
					$cells->setAlignment('center');
					$cells->setValignment('center');
				});
				$sheet->cells('A5:J5', function($cells) {
					$cells->setFontSize('12');
					$cells->setAlignment('center');
					$cells->setValignment('center');
					$cells->setBorder('solid', 'solid', 'solid', 'solid');
				});

				$sheet->cells('A6:J6', function($cells) {
					$cells->setFontSize('12');
					$cells->setAlignment('center');
					$cells->setValignment('center');
				});



				$sheet->cells('D7:E7', function($cells) {
					$cells->setAlignment('center');
					$cells->setValignment('center');
				});

				$sheet->cells('G', function($cells) {
					$cells->setAlignment('center');
				});

				$sheet->cells('I', function($cells) {
					$cells->setAlignment('center');
				});

		       	// add schems
		       	$col = 10;
		       	$first_row = 8;
		       	$last_row = 7;
		       	$first_column;
		       	$last_column;

		       	$sum_col = 	$col + count($schemes);
		       	if(count($schemes) > 0){
		       		foreach ($schemes as $scheme) {
			       		$sheet->setWidth(PHPExcel_Cell::stringFromColumnIndex($col), 20);
			       		$sheet->setCellValueByColumnAndRow($col,4, $scheme->item_desc);
			       		$sheet->setCellValueByColumnAndRow($col,5, $scheme->item_code);
			       		// add scheme allocation
			       		$row_num = 	$first_row;
			       		$first_column = PHPExcel_Cell::stringFromColumnIndex(10);
			       		$last_column = PHPExcel_Cell::stringFromColumnIndex($sum_col-1);
			       		foreach ($allocations[$scheme->scheme_id] as $value) {
			       			$sheet->setCellValueByColumnAndRow($col,$row_num, $value->allocations);
			       			$sum = "=SUM(".$first_column.$row_num.":".$last_column.$row_num.")";
			       			// dd($sum);
			       			$sheet->setCellValueByColumnAndRow($sum_col,$row_num, $sum);

			       			$row_num++;
			       		}
			       		// end schem allocation
			       		$col++;
			       		$last_row = $row_num -1;
			       		$total = "=SUM(".PHPExcel_Cell::stringFromColumnIndex($col-1).$first_row.":".PHPExcel_Cell::stringFromColumnIndex($sum_col-1).$last_row.")";
			       		$sheet->setCellValueByColumnAndRow($col-1, $row_num+1,$total);
			       	}

			       	$sheet->cells(PHPExcel_Cell::stringFromColumnIndex(8).'4:'.PHPExcel_Cell::stringFromColumnIndex($col-1).'5', function($cells) {
						$cells->setFontWeight('bold');
						$cells->setValignment('center');
					});
					$sheet->cells(PHPExcel_Cell::stringFromColumnIndex(8).'4:'.PHPExcel_Cell::stringFromColumnIndex($col).'5', function($cells) {
						$cells->setAlignment('center');

					});

			       	$grand_total = "=SUM(".PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".PHPExcel_Cell::stringFromColumnIndex($sum_col).$last_row.")";

			       	$sheet->setCellValueByColumnAndRow($col, $row_num+1,$grand_total);
			       	// dd($schemes);
			       	$sheet->setCellValueByColumnAndRow($col,4, "TOTAL");
			       	$sheet->setCellValueByColumnAndRow($col,5, "ORDER");
			       	$sheet->setWidth(PHPExcel_Cell::stringFromColumnIndex($col), 15);

			       	$sheet->cells('B8:C'.$last_row, function($cells) {
						$cells->setAlignment('right');
					});

			       	// end scheme
		       	}
		       	

		       	// add ship to
		       	$row = 8;
		       	foreach ($soldtos as $soldto) {

		       		$shipTo = ShipTo::where('ship_to_code',$soldto->ship_to_code)->first();
					// $week_start = new DateTime();
					// $week_start->setISODate($soldto->year,$soldto->weekno,$shipTo->dayofweek);
					// $loading_date = $week_start->format('Y-m-d');
					$date = date_create();
					date_isodate_set($date, $soldto->year, $soldto->weekno,$shipTo->dayofweek);
					$loading_date = date_format(date_create($date), 'Y-m-d');
					// dD($loading_date);
					
					$receipt_date = date('Y-m-d', strtotime($loading_date . '+ 1 days'));
					// dd($loading_date.'=>'.$receipt_date);s
					AllocationSob::where('weekno',$weekno)
						->where('year', $year)
						->where('ship_to_code', $soldto->ship_to_code)
						->update(array('loading_date' => $loading_date, 'receipt_date' => $receipt_date));

		       		$sheet->setCellValueByColumnAndRow(1,$row, date_format(date_create($loading_date),'m/d/Y'));
		       		$sheet->setCellValueByColumnAndRow(2,$row, date_format(date_create($receipt_date),'m/d/Y'));
		       		$sheet->setCellValueByColumnAndRow(6,$row, $soldto->sold_to_code);
		       		$sheet->setCellValueByColumnAndRow(7,$row, $soldto->ship_to);
		       		$sheet->setCellValueByColumnAndRow(8,$row, $soldto->ship_to_code);
		       		$row++;
		       	}
		       	// end ship to

		       	$sheet->setCellValueByColumnAndRow(4,$row + 2, "Grand Total");


		       	$sheet->setCellValueByColumnAndRow(3,$row + 4, "TOTAL CDC WAREHOUSE");

		       	$final_row = $last_row+5;
		       	$string2 = 'A4:'.PHPExcel_Cell::stringFromColumnIndex($sum_col).$final_row;
		       	$sheet->setBorder($string2, 'thin');



		       	$sheet->freezeFirstRow();
		    });
		});
	}
}