<?php 

class WordDoc {

	private $activity;
	public  function __construct($activity_id) {
		$this->activity = $activity_id;
	}

	public function download($filename){
		// download
		$phpWord = $this->generate($this->activity);
		$file = $filename;
		header("Content-Description: File Transfer");
		header('Content-Disposition: attachment; filename="' . $file . '"');
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');
		$xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$xmlWriter->save("php://output");
	}

	public function save($filename){
		// Save File
		$phpWord = $this->generate($this->activity);
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$objWriter->save($filename);
	}



	private function generate($activity_id){
		$activity = Activity::find($activity_id);
		$approvers = ActivityApprover::getNames($activity->id);

		$budgets = ActivityBudget::with('budgettype')
						->where('activity_id', $activity->id)
						->get();

		$sku_involves = ActivitySku::getInvolves($activity->id);
		$areas = ActivityCustomer::getSelectedAreas($activity->id);
		$channels = ActivityChannel2::getSelectecdChannels($activity->id);
		$schemes = Scheme::getList($activity->id);
		$skuinvolves = array();
		foreach ($schemes as $scheme) {
			$involves = SchemeHostSku::where('scheme_id',$scheme->id)
				->join('pricelists', 'scheme_host_skus.sap_code', '=', 'pricelists.sap_code')
				->get();

			$premiums = SchemePremuimSku::where('scheme_id',$scheme->id)
				->join('pricelists', 'scheme_premuim_skus.sap_code', '=', 'pricelists.sap_code')
				->get();
			
			$_involves = array();
			foreach ($involves as $value) {
				$_involves[] = $value;
			}
			$_premiums = array();
			foreach ($premiums as $premium) {
				$_premiums[] = $premium;
			}

			$scheme->allocations = SchemeAllocation::getAllocations($scheme->id);
			$non_ulp = explode(",", $scheme->ulp_premium);
			

			$skuinvolves[$scheme->id]['premiums'] = $_premiums;
			$skuinvolves[$scheme->id]['involves'] = $_involves;
			$skuinvolves[$scheme->id]['non_ulp'] = $non_ulp;
		}
		$networks = ActivityTiming::getTimings($activity->id,true);
		$activity_roles = ActivityRole::getListData($activity->id);
		$materials = ActivityMaterial::where('activity_id', $activity->id)
					->with('source')
					->get();

		$billing_date = "";
		if($activity->billing_date != ""){
			$billing_date = date_format(date_create($activity->billing_date),'M j, Y');
		}
		// $permit_no = "";
		$fdapermits = ActivityFdapermit::where('activity_id', $activity->id)->get();
		// if(!empty($fdapermit)){
		// 	$permit_no = $fdapermit->permit_no;
		// }
		
		$artworks = ActivityArtwork::getList($activity->id);
		$fdapermit = ActivityFdapermit::where('activity_id', $activity->id)->first();
		$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
		$pispermit = ActivityFis::where('activity_id', $activity->id)->first();
		$pis = array();
		if(!empty($pispermit)){
			$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
		}

		// New Word Document
		$phpWord = new \PhpOffice\PhpWord\PhpWord();


		// New portrait section
		$pageStyle = array('borderColor' => '00FF00','paperSize' => 'Letter', 'marginLeft' => 600, 'marginRight' => 600, 
			'marginTop' => 600, 'marginBottom' => 600);
		$section = $phpWord->addSection($pageStyle);
		$section->getStyle()->setPageNumberingStart(1);
		// Add header
		$header = $section->createHeader();
		$header->addText("Unilever Philippines, Inc.",array('bold'=>true,'size' => 13),array('spaceAfter' => 0));
		$header->addText("Customer Marketing Department",array('size' => 10),array('spaceAfter' => 0));
		$header->addImage(storage_path().'/uploads/tempfiles/logo.png',
			array(
				'width'            => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(3),
				'height'           => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(3),
				'positioning'      => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
				'posHorizontal'    => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
				'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
				'posVerticalRel'   => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
				'marginLeft'       => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(15.5),
				'marginTop'        => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(1.55),
			)
		);
		
		// Add footer
		$footer = $section->addFooter();
		$footer->addPreserveText(htmlspecialchars('Page {PAGE}/{NUMPAGES}', ENT_COMPAT, 'UTF-8'), null, array('align' => 'center'));

		// title
		$styleTable = array('cellMargin' => 30);
		$noSpace = array('spaceAfter' => 0);
		$phpWord->addTableStyle('Fancy Table', $styleTable);
		$table = $section->addTable('Fancy Table');
		$proponent = $activity->createdby->getFullname();
		if(!empty($activity->createdby->contact_no)){
			$proponent .= " / ".$activity->createdby->contact_no;
		}
		$planner = "";
		if(!empty($activity->pmog[0])){
			$planner = $activity->pmog[0]->getFullname();
			if(!empty($activity->pmog[0]->contact_no)){
				$planner .= " / ".$activity->pmog[0]->contact_no;
			}
		}

		$titleData = array(array('desc' => 'Circular Ref. No.', 'value' => $activity->id),
			array('desc' => 'Activity Name', 'value' => strip_tags($activity->activity_code)),
			array('desc' => 'TOP Cycle', 'value' => $activity->cycle->cycle_name),
			array('desc' => 'Proponent Name', 'value' => $proponent),
			array('desc' => 'PMOG Partner', 'value' => $planner),
			array('desc' => 'Approvers', 'value' => $approvers),
			);
		foreach ($titleData as $title) {
			if($title['desc'] != "Approvers"){
				$table->addRow();
				$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
				$table->addCell(9250)->addText(": ".htmlspecialchars($title['value']),array('size' => 8), $noSpace);
			}else{
				$first = false;
				if(count($title['value'])>0){
					foreach ($title['value'] as $approver) {
						$table->addRow();
						if(!$first){
							$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
							$first = true;
						}else{
							$table->addCell(1800)->addText("",array('bold'=>true,'size' => 8), $noSpace);
						}
						$table->addCell(9250)->addText(": ".$approver->first_name." ".$approver->last_name,array('size' => 8), $noSpace);
					}
				}else{
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$table->addCell(9250)->addText(": ",array('size' => 8), $noSpace);
				}
			}
			
		}
		$section->addTextBreak(1);

		// activity
		$styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 30);
		$fontStyle = array('bold' => true, 'align' => 'center');
		$phpWord->addTableStyle('Activity Table', $styleTable);
		$table = $section->addTable('Activity Table');
		
		$activityData = array(array('desc' => 'Activity Type', 'value' => $activity->activitytype->activity_type),
			array('desc' => 'Activity Title', 'value' => $activity->circular_name),
			array('desc' => 'Background', 'value' => $activity->background),
			array('desc' => 'Objectives', 'value' => $activity->objectives),
			array('desc' => 'Budget IO TTS', 'value' => $budgets),
			array('desc' => 'Budget IO PE', 'value' => $budgets),
			array('desc' => 'SKU/s Involved', 'value' => $sku_involves),
			array('desc' => 'Area/s Involved', 'value' => $areas),
			array('desc' => 'DT Channel/s Involved', 'value' => $channels),
			array('desc' => 'Schemes', 'value' => $schemes),
			array('desc' => 'SKU/s Involved Per Scheme', 'value' => $skuinvolves),
			array('desc' => 'Timings', 'value' => $networks),
			array('desc' => 'Roles and Responsibilities', 'value' => $activity_roles),
			array('desc' => 'Material Sourcing', 'value' => $materials),
			array('desc' => 'FDA Permit No.', 'value' => $fdapermits),
			array('desc' => 'Billing Requirements', 'value' => $activity->billing_remarks),
			array('desc' => 'Billing Deadline', 'value' => $billing_date),
			array('desc' => 'Special Instructions', 'value' => $activity->instruction),
			);
		foreach ($activityData as $title) {
			if($title['desc'] == 'Objectives'){
				if(count($title['value'])>0){
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					foreach ($title['value'] as $objective) {
						$innerCell = $cell->addTable('Areas Table');
						$innerCell->addRow();
						$innerCell->addCell(7350)->addText(htmlspecialchars($objective->objective),array('size' => 8), $noSpace);
						
					}
				}
			}elseif($title['desc'] == 'Budget IO TTS'){
				if(count($title['value'])>0){
					$first = false;
					foreach ($title['value'] as $budget) {
						if($budget->budget_type_id == 1){
							$table->addRow();
							if(!$first){
								$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
							}else{
								$table->addCell(1800)->addText('',array('bold'=>true,'size' => 8), $noSpace);
							}
							if(!empty($budget->remarks)){
								$value = $budget->io_number.' - '.$budget->remarks;
							}else{
								$value = $budget->io_number;
							}
							$table->addCell(9250)->addText(htmlspecialchars($value),array('size' => 8), $noSpace);
						}
						
					}
				}
			}elseif($title['desc'] == 'Budget IO PE'){
				if(count($title['value'])>0){
					$first = false;
					foreach ($title['value'] as $budget) {
						if($budget->budget_type_id == 2){
							$table->addRow();
							if(!$first){
								$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
							}else{
								$table->addCell(1800)->addText('',array('bold'=>true,'size' => 8), $noSpace);
							}
							if(!empty($budget->remarks)){
								$value = $budget->io_number.' - '.$budget->remarks;
							}else{
								$value = $budget->io_number;
							}
							$table->addCell(9250)->addText(htmlspecialchars($value),array('size' => 8), $noSpace);
						}
						
					}
				}
			}elseif($title['desc'] == 'SKU/s Involved'){
				if(count($title['value'])>0){
					$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
					$phpWord->addTableStyle('Inner Table', $styleTable, $styleFirstRow);
					$innerCell = $cell->addTable('Inner Table');
					$innerCell->addRow();
					$innerCell->addCell(1800)->addText("SKU Code",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1800)->addText("Description",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					foreach($sku_involves as $sku_involve){
						$innerCell->addRow();
						$innerCell->addCell(1800)->addText($sku_involve->sap_code,array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(7350)->addText(htmlspecialchars($sku_involve->sap_desc),array('size' => 8,'align' => 'center'), $fontStyle);
					}
				}
			}elseif($title['desc'] == 'Area/s Involved'){
				if(count($title['value'])>0){
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					foreach ($title['value'] as $area) {
						$innerCell = $cell->addTable('Areas Table');
						$innerCell->addRow();
						$innerCell->addCell(7350)->addText(htmlspecialchars($area),array('size' => 8), $noSpace);
						
					}
				}
			}elseif($title['desc'] == 'DT Channel/s Involved'){
				if(count($title['value'])>0){
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					foreach ($title['value'] as $channel) {
						$innerCell = $cell->addTable('Areas Table');
						$innerCell->addRow();
						$innerCell->addCell(7350)->addText(htmlspecialchars($channel),array('size' => 8), $noSpace);
					}
				}
			}elseif($title['desc'] == 'Schemes'){
				if(count($title['value'])>0){
					$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
					$phpWord->addTableStyle('Inner Table', $styleTable, $styleFirstRow);
					$innerCell = $cell->addTable('Inner Table');
					$innerCell->addRow();
					$innerCell->addCell(300)->addText("",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(2450)->addText("Scheme Desc.",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1600)->addText("Item Code",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1600)->addText("Cost per Deal",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1600)->addText("Cost of Premium",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1600)->addText("Shopper Purchase Requirement",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$cnt = 1;
					foreach($title['value'] as $scheme){
						$innerCell->addRow();
						$innerCell->addCell(300)->addText($cnt,array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(2450)->addText(htmlspecialchars($scheme->name),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(1600)->addText(($scheme->item_code == '') ? 'N/A' :  $scheme->item_code,array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(1600)->addText(number_format($scheme->ulp,2),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(1600)->addText(number_format($scheme->srp_p,2),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(1600)->addText(number_format($scheme->pr,2),array('size' => 8,'align' => 'center'), $fontStyle);
						$cnt++;
					}
					
				}
			}elseif($title['desc'] == 'SKU/s Involved Per Scheme'){
				if(count($title['value'])>0){
					$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
					$phpWord->addTableStyle('Inner Table', $styleTable, $styleFirstRow);
					$innerCell = $cell->addTable('Inner Table');
					$innerCell->addRow();
					$innerCell->addCell(300)->addText("",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(2450)->addText("Host SKU Code - Description",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(3200)->addText("Premium SKU Code - Description",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(3200)->addText("Non ULP Premium",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$cnt = 1;
					foreach($title['value'] as $key => $sku){
						$innerCell->addRow();
						$innerCell->addCell(300)->addText($cnt,array('size' => 8,'align' => 'center'), $fontStyle);
						$_involves = "";
						foreach($sku['involves'] as $involve){
							$_involves .= $involve->sap_code.' - '.$involve->sap_desc;
						}
						$_premiums = "";
						foreach($sku['premiums'] as $premium){
							$_premiums .= $premium->sap_code.' - '.$premium->sap_desc;
						}
						$_nonulp = "";
						foreach($sku['non_ulp'] as $_non_ulp){
							$_nonulp .= $_non_ulp;
						}
						$innerCell->addCell(2450)->addText(htmlspecialchars($_involves),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(3200)->addText(htmlspecialchars($_premiums),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(3200)->addText(htmlspecialchars($_nonulp),array('size' => 8,'align' => 'center'), $fontStyle);
						$cnt++;
					}
					
				}
			}elseif($title['desc'] == 'Timings'){
				if(count($title['value'])>0){
					$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
					$phpWord->addTableStyle('Inner Table', $styleTable, $styleFirstRow);
					$innerCell = $cell->addTable('Inner Table');
					$innerCell->addRow();
					$innerCell->addCell(5950)->addText("Activity",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1600)->addText("Start Date",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1600)->addText("End Date",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					foreach($title['value'] as $network){
						$innerCell->addRow();
						$innerCell->addCell(5950)->addText(htmlspecialchars($network->task),array('size' => 8,'align' => 'center'), $fontStyle);
						$start_date = ($network->final_start_date != null) ?  date_format(date_create($network->final_start_date),'M j, Y') : '';
						$end_date = ($network->final_end_date != null) ?  date_format(date_create($network->final_end_date),'M j, Y') : '';
						$innerCell->addCell(1600)->addText($start_date,array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(1600)->addText($end_date,array('size' => 8,'align' => 'center'), $fontStyle);
					}
					$innerCell->addRow();
					$innerCell->addCell(5950)->addText('IMPLEMENTATION DATE',array('size' => 8,'align' => 'center'), $fontStyle);
					$start_date = date_format(date_create($activity->eimplementation_date),'M j, Y');
					$end_date = date_format(date_create($activity->end_date),'M j, Y');
					$innerCell->addCell(1600)->addText($start_date,array('size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(1600)->addText($end_date,array('size' => 8,'align' => 'center'), $fontStyle);
				}
			}elseif($title['desc'] == 'Roles and Responsibilities'){
				if(count($title['value'])>0){
					$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
					$phpWord->addTableStyle('Inner Table', $styleTable, $styleFirstRow);
					$innerCell = $cell->addTable('Inner Table');
					$innerCell->addRow();
					$innerCell->addCell(2750)->addText("Process Owner",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(3200)->addText("Action Points",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(3200)->addText("Timings",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					foreach($title['value'] as $activity_role){
						$innerCell->addRow();
						$innerCell->addCell(2750)->addText(htmlspecialchars($activity_role->owner),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(3200)->addText(htmlspecialchars($activity_role->point),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(3200)->addText(htmlspecialchars($activity_role->timing),array('size' => 8,'align' => 'center'), $fontStyle);
					}	
				}
			}elseif($title['desc'] == 'Material Sourcing'){
				if(count($title['value'])>0){
					$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
					$phpWord->addTableStyle('Inner Table', $styleTable, $styleFirstRow);
					$innerCell = $cell->addTable('Inner Table');
					$innerCell->addRow();
					$innerCell->addCell(5950)->addText("Source",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$innerCell->addCell(3200)->addText("Materials",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					foreach($title['value'] as $material){
						$innerCell->addRow();
						$innerCell->addCell(5950)->addText(htmlspecialchars($material->source->source),array('size' => 8,'align' => 'center'), $fontStyle);
						$innerCell->addCell(3200)->addText(htmlspecialchars($material->material),array('size' => 8,'align' => 'center'), $fontStyle);
					}
				}
			}elseif($title['desc'] == 'FDA Permit No.'){
				if(count($title['value'])>0){
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					foreach ($title['value'] as $permit) {
						$innerCell = $cell->addTable('Permit Table');
						$innerCell->addRow();
						$innerCell->addCell(7350)->addText(htmlspecialchars($permit->permit_no),array('size' => 8), $noSpace);
						
					}
				}
			}else{
				if(!empty($title['value'])){
					$table->addRow();
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
					$cell = $table->addCell(9250);
					$text = explode("\r\n", $title['value']);
					foreach($text as $line) {
						$innerCell = $cell->addTable();
						$innerCell->addRow();
						$innerCell->addCell(7350)->addText(htmlspecialchars($line),array('size' => 8), $noSpace);
						
					}
				}
			}
		}

		// Artworks
		if(count($artworks) > 0){
			$section->addTextBreak(1);
			$section->addText("Artworks",array('bold'=>true,'size' => 10));

			// Add table
			$arttable = $section->addTable('Artwork Table'); 
			$cnt = 0;
			
			foreach($artworks as $artwork) { // Loop through cells
				if($cnt == 0){
					$arttable->addRow();
				}
				$cell = $arttable->addCell(900);
				$textrun = $cell->createTextRun();
				$textrun->addImage(storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$artwork->hash_name,array('width'=>150));
				$cnt++;
				if($cnt == 4){
					$cnt=0;
				}
			}
		}

		// Barcodes / Case Codes
		if(count($schemes) > 0){
			$section->addTextBreak(1);
			$section->addText("Barcodes / Case Codes Per Scheme",array('bold'=>true,'size' => 10));
			$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
			$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
			$phpWord->addTableStyle('Barcode Table', $styleTable, $styleFirstRow);
			$barcodeTable = $section->addTable('Barcode Table');
			$barcodeTable->addRow();
			$barcodeTable->addCell(5525)->addText("Source",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$barcodeTable->addCell(5525)->addText("Materials",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			foreach ($schemes as $scheme) {
				if(($scheme->item_barcode  !== "") || ($scheme->item_casecode !== "")){
					$barcodeTable->addRow();
					$innerCell = $cell->addTable('Inner Table');
					$barcodeCell = $barcodeTable->addCell(5525);
					if($scheme->item_barcode  !== ""){
						DNS1D::getBarcodePNGPath($scheme->item_barcode, "EAN13",2,80);
						
						$barcodeCell->addText($scheme->name,array('size' => 8,'align' => 'center'), $fontStyle);
						$barcodeCell->addImage(public_path().'/barcode/'.$scheme->item_barcode.'.png',array('align' => 'center'));
						$barcodeCell->addText($scheme->item_barcode,array('size' => 8,'align' => 'center'), $fontStyle);
					}

					$casecodeCell = $barcodeTable->addCell(5525);
					if($scheme->item_casecode  !== ""){
						DNS1D::getBarcodePNGPath($scheme->item_casecode, "I25",2,80);
						
						$casecodeCell->addText($scheme->name,array('size' => 8,'align' => 'center'), $fontStyle);
						$casecodeCell->addImage(public_path().'/barcode/'.$scheme->item_casecode.'.png',array('align' => 'center','spaceAfter' => 0));
						$casecodeCell->addText($scheme->item_casecode,array('size' => 8,'align' => 'center'), $fontStyle);
					}	
				}
			}
		}
		
	    // FDA Permit
		if(!empty($fdapermits)){
			$section->addTextBreak(1);
			$section->addText("FDA Permit",array('bold'=>true,'size' => 10));

			// Add table
			$permittable = $section->addTable('Permit Table'); 
			$cnt = 0;
			
			foreach($fdapermits as $permit) { // Loop through cells
				$permittable->addRow();
				$file = explode(".", $permit->file_desc);
				$file_ex = strtolower($file[1]);
				if(($file_ex != "pdf") &&  ($file_ex != "xps")){
					$cell = $permittable->addCell(900);
					$textrun = $cell->createTextRun();
					$textrun->addImage(storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$permit->hash_name,array('width'=>800));
				}
				
			}

			// foreach ($fdapermits as $permit) {
			// 	$file = explode(".", $permit->file_desc);
			// 	if(($file[1] != "pdf") &&  ($file[1] != "xps")){
			// 		$section->addPageBreak();
			// 		$section->addText("FDA Permit",array('bold'=>true,'size' => 10));
			// 		$section->addImage(storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$permit->hash_name,array('height'=>800));
			// 	}
			// }
			
		}

		

		// Product Information Sheet
		if(count($pis) > 0){
			$section->addPageBreak();
			$section->addText("Product Information Sheet",array('bold'=>true,'size' => 10));

			$styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 30);
			$fontStyle = array('bold' => true, 'align' => 'center');
			$phpWord->addTableStyle('PIS Table', $styleTable);
			$pistable = $section->addTable('PIS Table');

			$pisTitleData = array(array('desc' => 'Product Category', 'value' => htmlspecialchars($pis[2][1])),
				array('desc' => 'Sub Category', 'value' => htmlspecialchars($pis[3][1])),
				array('desc' => 'Brand / Scheme', 'value' => htmlspecialchars($pis[4][1])),
				array('desc' => 'Target Market', 'value' => htmlspecialchars($pis[5][1])),
				array('desc' => 'Product Features', 'value' => htmlspecialchars($pis[6][1])),
				array('desc' => 'Major Competitors', 'value' => htmlspecialchars($pis[7][1])),
				array('desc' => 'Minor Competitors', 'value' => htmlspecialchars($pis[8][1])),
				);
			foreach ($pisTitleData as $title) {
				$pistable->addRow();
				$pistable->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
				$pistable->addCell(9250)->addText($title['value'],array('size' => 8), $noSpace);
			}
			$pis_x = 0; 
			for ($i=8; $i < count($pis); $i++) { 
				if($pis[$i][7] == "Case Dimensions (MM)"){
					$pis_x = $i+2;
					break;
				}
			}

			$pistable = $section->addTable('PIS Table');
			$pistable->addRow();
			$piscell =  $pistable->addCell(11050);
			$fontStyle = array('bold' => true, 'align' => 'center','spaceAfter' => 0);
			$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '000000','color' => 'ffffff');
			$phpWord->addTableStyle('table 1_h', $styleTable,$styleFirstRow);
			$t1_h = $piscell->addTable('table 1_h');
			$t1_h->addRow();
			$t1_h->addCell(9100)->addText("",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1_h->addCell(2000)->addText("Case Dimensions (MM)",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$phpWord->addTableStyle('table 1', $styleTable,$styleFirstRow);
			$t1 = $piscell->addTable('table 1');
			$t1->addRow();
			$t1->addCell(3000)->addText("Item Description",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(1000)->addText("Pack Size",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(1000)->addText("Pack Color",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(1000)->addText("Units/Case",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(1500)->addText("Product Barcode",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(1500)->addText("Product Code",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(600)->addText("Case/Ton",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(500)->addText("L",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(500)->addText("W",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t1->addCell(500)->addText("H",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);

			for($x1 = $pis_x; $x1 < count($pis); $x1++){
				if($pis[$x1][1] == "Product Dimension (MM)"){
					break;
				}
				if($pis[$x1][0] != ""){
					$t1->addRow();
					$t1->addCell(3000)->addText(htmlspecialchars($pis[$x1][0]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(1000)->addText(htmlspecialchars($pis[$x1][1]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(1000)->addText(htmlspecialchars($pis[$x1][2]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(1000)->addText(htmlspecialchars($pis[$x1][3]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(1500)->addText(htmlspecialchars($pis[$x1][4]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(1500)->addText(htmlspecialchars($pis[$x1][5]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(600)->addText(number_format($pis[$x1][6],2),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(500)->addText($pis[$x1][7],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(500)->addText($pis[$x1][8],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t1->addCell(500)->addText($pis[$x1][9],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
				}
				$pis_x = $x1;
			}

			$phpWord->addTableStyle('table 2_h', $styleTable,$styleFirstRow);
			$t2_h = $piscell->addTable('table 2_h');
			$t2_h->addRow();
			$t2_h->addCell(3000)->addText("",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2_h->addCell(1500)->addText("Product Dimensions (MM)",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2_h->addCell(3700)->addText("",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2_h->addCell(2900)->addText("Maximum Case Stocking",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$phpWord->addTableStyle('table 2', $styleTable,$styleFirstRow);
			$t2 = $piscell->addTable('table 2');
			$t2->addRow();
			$t2->addCell(3000)->addText("Item Description",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(500)->addText("L",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(500)->addText("W",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(500)->addText("H",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(1500)->addText("Product Casecode",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(1000)->addText("Net Wgt Kg",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(1200)->addText("Gross Wgt KG",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(900)->addText("CS/Layer",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(1000)->addText("Layer/Pallet",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t2->addCell(1000)->addText("Pallets/Tier",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);

			$pis_x = $pis_x+3;
			for($x2 = $pis_x; $x2 < count($pis); $x2++){
				if($pis[$x2][8] == "Trade margins"){
					break;
					$pis_x = $x2;
				}
				if($pis[$x2][0] != ""){
					$t2->addRow();
					$t2->addCell(3000)->addText(htmlspecialchars($pis[$x2][0]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(500)->addText($pis[$x2][1],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(500)->addText($pis[$x2][2],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(500)->addText($pis[$x2][3],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(1500)->addText(htmlspecialchars($pis[$x2][4]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(1000)->addText(number_format($pis[$x2][5],2),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(1200)->addText(number_format($pis[$x2][6],2),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(1000)->addText($pis[$x2][7],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(900)->addText($pis[$x2][8],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t2->addCell(1000)->addText($pis[$x2][9],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
				}
				$pis_x = $x2;
			}

			$phpWord->addTableStyle('table 3', $styleTable,$styleFirstRow);
			$t3 = $piscell->addTable('table 3');
			$t3->addRow();
			$t3->addCell(3000)->addText("Item Description",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(500)->addText("Total Shelf Life (SLED in Days)",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(500)->addText("Pieces/Inner Pack (regular SKU with inner pack/carton)",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(1500)->addText("Product Barcode",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(1500)->addText("Product Code",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(1000)->addText("LPAT/CS",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(800)->addText("LPAT per PC/MP",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(800)->addText("SRP Per PC/MP",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(500)->addText("%",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
			$t3->addCell(800)->addText("Absolute",array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);

			$pis_x = $pis_x+3;
			for($x3 = $pis_x; $x3 < count($pis); $x3++){
				if($pis[$x3][0] != ""){
					$t3->addRow();
					$t3->addCell(3000)->addText(htmlspecialchars($pis[$x3][0]),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(500)->addText($pis[$x3][1],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(500)->addText($pis[$x3][2],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(1500)->addText($pis[$x3][3],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(1500)->addText($pis[$x3][4],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(1000)->addText($pis[$x3][5],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(800)->addText($pis[$x3][6],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(800)->addText($pis[$x3][7],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(500)->addText(round(number_format($pis[$x3][8],2)),array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
					$t3->addCell(800)->addText($pis[$x3][9],array('bold'=>true,'size' => 8,'align' => 'center'), $fontStyle);
				}
			}
		}
		
		// schemes
		if(count($schemes) > 0){
			$section->addPageBreak();
			// New landscape section
			$section = $phpWord->addSection(
			    array('paperSize' => 'Letter','orientation' => 'landscape','marginLeft' => 600, 'marginRight' => 600, 'marginTop' => 600, 'marginBottom' => 600)
			);
			$_ap = 0;
			foreach ($schemes as $scheme) {

				$count = count($scheme->allocations);
				$_all = $count * count($scheme);
				$loops = (int) ($count / 20);
				
				$cnt = 0;
				for ($i = 0; $i < $loops; $i++) { 
					$last_count =  $cnt+20;
					$x = $i +1;
					$_ap++;
					$section->addText($scheme->name,array('bold'=>true,'size' => 10));
					$section->addText($x.' of '.$loops,array('bold'=>true,'size' => 10));

					$styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 60);
					$fontStyle = array('bold' => true, 'align' => 'center');
					$phpWord->addTableStyle('Schemes Table', $styleTable);
					$table = $section->addTable('Schemes Table');
					$scheme_style = array('size' => 6);
					$headStyle = array('spaceAfter' => 0,'align' => 'center');

					$table->addRow();
					$table->addCell(500)->addText('#',$scheme_style, $headStyle );
					$table->addCell(1000)->addText('GROUP',$scheme_style, $headStyle);
					$table->addCell(2000)->addText('AREA NAME',$scheme_style, $headStyle);
					$table->addCell(2000)->addText('CUSTOMER SOLD TO',$scheme_style, $headStyle );
					$table->addCell(2500)->addText('CUSTOMER SHIP TO NAME',$scheme_style, $headStyle);
					$table->addCell(1000)->addText('CHANNEL',$scheme_style, $headStyle);
					$table->addCell(2500)->addText('ACCOUNT NAME',$scheme_style, $headStyle);
					$table->addCell(1000)->addText('IN DEALS',$scheme_style, $headStyle);
					$table->addCell(1000)->addText('IN CASES',$scheme_style, $headStyle);
					$table->addCell(1000)->addText('TTS BUDGET',$scheme_style, $headStyle);
					$table->addCell(1000)->addText('PE BUDGET',$scheme_style, $headStyle);

					for ($x=$cnt; $x <= $last_count; $x++) { 
						if($cnt == $count){
							break;
						}
						$num = $x + 1;
						$rowstyle = array('bgColor' => 'FFFFFF');
						if((empty($scheme->allocations[$x]->customer_id)) && (empty($scheme->allocations[$x]->shipto_id))){
							$rowstyle = array('bgColor' => 'd9edf7');
						}
						if((!empty($scheme->allocations[$x]->customer_id)) && (!empty($scheme->allocations[$x]->shipto_id))){

							$rowstyle = array('bgColor' => 'fcf8e3');
						}

						$table->addRow();
						$table->addCell(500,$rowstyle)->addText($num,$scheme_style, array('spaceAfter' => 0,'align' => 'right'));
						$table->addCell(1000,$rowstyle)->addText(htmlspecialchars($scheme->allocations[$x]->group),$scheme_style, array('spaceAfter' => 0));
						$table->addCell(2000,$rowstyle)->addText(htmlspecialchars($scheme->allocations[$x]->area),$scheme_style,array('spaceAfter' => 0));
						$table->addCell(2000,$rowstyle)->addText(htmlspecialchars($scheme->allocations[$x]->sold_to),$scheme_style, array('spaceAfter' => 0));
						$table->addCell(2500,$rowstyle)->addText(htmlspecialchars($scheme->allocations[$x]->ship_to),$scheme_style,array('spaceAfter' => 0));
						$table->addCell(1000,$rowstyle)->addText(htmlspecialchars($scheme->allocations[$x]->channel),$scheme_style, array('spaceAfter' => 0));
						$table->addCell(2500,$rowstyle)->addText(htmlspecialchars($scheme->allocations[$x]->outlet),$scheme_style,array('spaceAfter' => 0));
						$table->addCell(1000,$rowstyle)->addText(number_format($scheme->allocations[$x]->in_deals),$scheme_style, array('spaceAfter' => 0,'align' => 'right'));
						$table->addCell(1000,$rowstyle)->addText(number_format($scheme->allocations[$x]->in_cases),$scheme_style, array('spaceAfter' => 0,'align' => 'right'));
						$table->addCell(1000,$rowstyle)->addText(number_format($scheme->allocations[$x]->tts_budget,2),$scheme_style, array('spaceAfter' => 0,'align' => 'right'));
						$table->addCell(1000,$rowstyle)->addText(number_format($scheme->allocations[$x]->pe_budget,2),$scheme_style, array('spaceAfter' => 0,'align' => 'right'));
						$cnt++;
					}
					if($_ap < ($loops * count($schemes)) ){
						$section->addPageBreak();
					}
				}
			}
		}

		return $phpWord;
		
	}

}