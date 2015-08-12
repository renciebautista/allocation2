<?php
use Imagecow\Image;
Queue::getIron()->ssl_verifypeer = false;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/



Route::get('testword',function(){

	$activity = Activity::find(79);
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

	$fdapermit = ActivityFdapermit::where('activity_id', $activity->id)->first();
	$artworks = ActivityArtwork::getList($activity->id);

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
	$header->addImage(
	    'logo.png',
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
		array('desc' => 'Background', 'value' => nl2br($activity->background)),
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
		array('desc' => 'FDA Permit No.', 'value' => $fdapermit->permit_no),
		array('desc' => 'Billing Requirements', 'value' => $activity->billing_remarks),
		array('desc' => 'Billing Deadline', 'value' => $billing_date),
		array('desc' => 'Special Instructions', 'value' => $activity->instruction),
		);
	foreach ($activityData as $title) {
		if($title['desc'] == 'Objectives'){
			$first = false;
			foreach ($title['value'] as $objective) {
				$table->addRow();
				if(!$first){
					$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
				}else{
					$table->addCell(1800)->addText('',array('bold'=>true,'size' => 8), $noSpace);
				}
				$table->addCell(9250)->addText(htmlspecialchars($objective->objective),array('size' => 8), $noSpace);
			}
		}elseif($title['desc'] == 'Budget IO TTS'){
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
		}elseif($title['desc'] == 'Budget IO PE'){
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
			$table->addRow();
			$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
			$cell = $table->addCell(9250);
			foreach ($title['value'] as $area) {
				$innerCell = $cell->addTable('Areas Table');
				$innerCell->addRow();
				$innerCell->addCell(7350)->addText(htmlspecialchars($area),array('size' => 8), $noSpace);
				
			}
		}elseif($title['desc'] == 'DT Channel/s Involved'){
			$table->addRow();
			$table->addCell(1800)->addText($title['desc'],array('bold'=>true,'size' => 8), $noSpace);
			$cell = $table->addCell(9250);
			foreach ($title['value'] as $channel) {
				$innerCell = $cell->addTable('Areas Table');
				$innerCell->addRow();
				$innerCell->addCell(7350)->addText(htmlspecialchars($channel),array('size' => 8), $noSpace);
				
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
		}
		elseif($title['desc'] == 'Roles and Responsibilities'){
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
		}
		else{
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

	// Artworks
	$section->addTextBreak(1);
	$section->addText("Artworks",array('bold'=>true,'size' => 10));

	// Add table
	$arttable = $section->addTable(); 
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


	// Barcodes / Case Codes
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
				$barcodeCell->addImage('public/barcode/'.$scheme->item_barcode.'.png',array('align' => 'center'));
				$barcodeCell->addText($scheme->item_barcode,array('size' => 8,'align' => 'center'), $fontStyle);
			}

			$casecodeCell = $barcodeTable->addCell(5525);
			if($scheme->item_casecode  !== ""){
				DNS1D::getBarcodePNGPath($scheme->item_casecode, "I25",2,80);
				$casecodeCell->addText($scheme->name,array('size' => 8,'align' => 'center'), $fontStyle);
				$casecodeCell->addImage('public/barcode/'.$scheme->item_casecode.'.png',array('align' => 'center','spaceAfter' => 0));
				$casecodeCell->addText($scheme->item_casecode,array('size' => 8,'align' => 'center'), $fontStyle);
			}
			
		}
		
	}


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

	// Save File
	$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	$objWriter->save('HeaderFooter.docx');
							
});

Route::get('testrole', function(){
	// $filename = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', "SNOWBALL 2015 PREBANDED PACKS 470ML/700ML SCHEMES");
	// echo strtoupper(Helper::sanitize("SNOWBALL-2015-LADY’S-CHOICE-CATEGORY-EXPERTS"));
	$user = User::find(2);
	$cycles = Cycle::getByReleaseDate();
	$cycle_ids = array();
	$cycle_names = "";
	foreach ($cycles as $value) {
		$cycle_ids[] = $value->id;
		$cycle_names .= $value->cycle_name ." - ";
	}
	$data['cycles'] = $cycles;
	$data['user'] = $user->first_name;
	$data['email'] = $user->email;
	$data['fullname'] = $user->getFullname();
	$data['cycle_ids'] = $cycle_ids;
	$data['cycle_names'] = $cycle_names;
	
	$data['activities'] = Activity::Released($cycle_ids);

	return View::make('emails.mail4', $data);
	
});

Route::post('queue/push', function()
{
	return Queue::marshal();
});


Route::post('queue/pdf', function()
{
	return Queue::marshal();
});

Route::post('queue/allocreport', function()
{
	return Queue::marshal();
});


Route::get('/','LoginController@index');
Route::get('login','LoginController@index');
Route::get('logout','LoginController@logout');
Route::post('login', 'LoginController@dologin');

Route::get('forgotpassword','LoginController@forgotpassword');
Route::post('forgotpassword','LoginController@doforgotpassword');
Route::get('reset_password/{token}','LoginController@resetpassword');
Route::post('reset_password', 'LoginController@doResetPassword');

Route::get('downloadcycle/{id}', 'DownloadsController@downloadcycle');
Route::get('ar/{token}','AllocationReportController@download');


Route::group(array('before' => 'auth'), function()
{	
	Route::pattern('id', '[0-9]+');

	Route::get('help', 'HelpController@index');

	Route::get('activity/{id}/timings', 'ActivityController@timings');
	Route::get('activity/{id}/activityroles', 'ActivityController@activityroles');

	Route::post('activity/{id}/updatetimings', 'ActivityController@updatetimings');
	
	Route::post('activity/{id}/updateactivity', 'ActivityController@updateactivity');
	Route::get('activity/{id}/recall', 'ActivityController@recall');

	Route::post('activity/{id}/addbudget', 'ActivityController@addbudget');
	Route::delete('activity/deletebudget', 'ActivityController@deletebudget');
	Route::put('activity/updatebudget', 'ActivityController@updatebudget');

	Route::post('activity/{id}/addnobudget', 'ActivityController@addnobudget');
	Route::delete('activity/deletenobudget', 'ActivityController@deletenobudget');
	Route::put('activity/updatenobudget', 'ActivityController@updatenobudget');

	Route::post('activity/{id}/addmaterial', 'ActivityController@addmaterial');
	Route::delete('activity/deletematerial', 'ActivityController@deletematerial');
	Route::put('activity/updatematerial', 'ActivityController@updatematerial');

	Route::put('activity/{id}/updatecustomer', 'ActivityController@updatecustomer');
	Route::put('activity/{id}/updatebilling', 'ActivityController@updatebilling');
	Route::put('activity/updateforcealloc', 'ActivityController@updateforcealloc');

	Route::get('activity/{id}/scheme', 'SchemeController@index');
	Route::get('activity/{id}/scheme/create', 'SchemeController@create');
	Route::post('activity/{id}/scheme', 'SchemeController@store');

	Route::post('activity/{id}/fdaupload', 'ActivityController@fdaupload');
	Route::delete('activity/{id}/fdadelete', 'ActivityController@fdadelete');
	Route::get('activity/{id}/fdadownload', 'ActivityController@fdadownload');

	Route::post('activity/{id}/fisupload', 'ActivityController@fisupload');
	Route::delete('activity/{id}/fisdelete', 'ActivityController@fisdelete');
	Route::get('activity/{id}/fisdownload', 'ActivityController@fisdownload');
	
	Route::post('activity/{id}/artworkupload', 'ActivityController@artworkupload');
	Route::post('activity/artworkdelete', 'ActivityController@artworkdelete');
	Route::get('activity/{id}/artworkdownload', 'ActivityController@artworkdownload');

	Route::post('activity/{id}/backgroundupload', 'ActivityController@backgroundupload');
	Route::post('activity/backgrounddelete', 'ActivityController@backgrounddelete');
	Route::get('activity/{id}/backgrounddownload', 'ActivityController@backgrounddownload');

	Route::post('activity/{id}/bandingupload', 'ActivityController@bandingupload');
	Route::post('activity/bandingdelete', 'ActivityController@bandingdelete');
	Route::get('activity/{id}/bandingdownload', 'ActivityController@bandingdownload');

	Route::get('activity/{id}/channels', 'ActivityController@channels');

	Route::post('activity/{id}/submittogcm', 'ActivityController@submittogcm');
	
	Route::get('activity/{id}/allocsummary', 'ActivityController@allocsummary');
	Route::get('activity/pistemplate', 'ActivityController@pistemplate');

	Route::post('activity/{id}/duplicate','ActivityController@duplicate');
	Route::get('activity/{id}/summary','ActivityController@summary');
	
	Route::resource('activity', 'ActivityController');
	

	Route::get('scheme/{id}/export', 'SchemeController@export');
	Route::get('scheme/{id}/allocation', 'SchemeController@allocation');
	Route::get('scheme/{id}', 'SchemeController@show');
	Route::get('scheme/{id}/edit', 'SchemeController@edit');
	Route::delete('scheme/{id}', 'SchemeController@destroy');
	Route::put('scheme/{id}', 'SchemeController@update');
	Route::post('scheme/{id}/duplicate','SchemeController@duplicate');
	Route::put('scheme/updatealloc', 'SchemeController@updateallocation');
	Route::post('scheme/{id}/duplicatescheme', 'SchemeController@duplicatescheme');

	// Route::post('downloadedactivity/{id}/submittogcm', 'DownloadedActivityController@submittogcm');
	// Route::resource('downloadedactivity', 'DownloadedActivityController');


	Route::post('submittedactivity/{id}/updateactivity', 'SubmittedActivityController@updateactivity');
	Route::resource('submittedactivity', 'SubmittedActivityController');

	Route::get('downloads/cycles', 'DownloadsController@cycles');
	Route::get('downloads/{id}/download', 'DownloadsController@download');

	Route::resource('group', 'GroupController');

	Route::get('dashboard', 'DashboardController@index');
	Route::get('dashboard/filters', 'DashboardController@filters');
	Route::post('dashboard/filters', 'DashboardController@savefilters');

	Route::get('dashboard/categoryselected', 'DashboardController@categoryselected');
	Route::get('dashboard/brandselected', 'DashboardController@brandselected');
	Route::get('dashboard/customerselected', 'DashboardController@customerselected');

	Route::get('profile','ProfileController@index');
	Route::post('profile','ProfileController@update');


	Route::get('changepassword', 'ProfileController@changepassword');
	Route::post('updatepassword', 'ProfileController@updatepassword');

	Route::get('users/exportuser', 'UsersController@exportuser');
	Route::resource('users', 'UsersController');
	
	Route::post('cycle/release', 'CycleController@release');
	Route::post('cycle/{id}/rerun', 'CycleController@rerun');
	Route::get('cycle/calendar', 'CycleController@calendar');
	Route::resource('cycle', 'CycleController');

	Route::get('activitytype/{id}/network', 'NetworkController@index');
	Route::get('activitytype/{id}/network/list', 'NetworkController@show');
	Route::get('activitytype/{id}/network/dependon', 'NetworkController@dependOn');
	Route::get('activitytype/{id}/network/totalduration', 'NetworkController@totalduration');
	Route::post('activitytype/{id}/network/create', 'NetworkController@store');
	Route::post('network/delete', 'NetworkController@destroy');
	Route::get('network/edit', 'NetworkController@edit');
	Route::post('network/update', 'NetworkController@update');



	
	Route::resource('activitytype', 'ActivityTypeController');

	Route::get('holidays/getlist', 'HolidaysController@getlist');
	Route::resource('holidays', 'HolidaysController');

	// Route::resource('job','JobController');	

	Route::get('reports/allocation', 'AllocationReportController@index');
	Route::get('reports/allocation/create', 'AllocationReportController@create');
	Route::post('reports/allocation/create', 'AllocationReportController@store');
	Route::get('reports/allocation/{id}/generate', 'AllocationReportController@show');
	Route::post('reports/allocation/{id}/generate', 'AllocationReportController@generate');
	Route::get('reports/allocation/{id}', 'AllocationReportController@edit');
	Route::delete('reports/allocation/{id}', 'AllocationReportController@destroy');

	Route::get('reports/activities', 'ReportController@activities');
	Route::get('reports/{id}/preview', 'ReportController@preview');
	Route::get('reports/{id}/download', 'ReportController@download');



	Route::get('images/{cycle_id}/{type_id}/{activity_id}/{name}', function($cycle_id = null,$type_id = null,$activity_id = null,$name = null)
	{
		$path = storage_path().'/uploads/'.$cycle_id.'/'. $type_id.'/'. $activity_id.'/'. $name;
		if (file_exists($path)) { 

			$image = Image::create($path);
			$image->resize(300, 200, 1);
			return $image->show();
		}
	});

	Route::get('fdapermit/{cycle_id}/{type_id}/{activity_id}/{name}', function($cycle_id = null,$type_id = null,$activity_id = null,$name = null)
	{
		$path = storage_path().'/uploads/'.$cycle_id.'/'. $type_id.'/'. $activity_id.'/'. $name;
		if (file_exists($path)) { 
			$image = Image::create($path);
			$image->resize(1000);
			return $image->show();
		}
	});

	Route::group(array('prefix' => 'api'), function()
	{
		Route::get('customerselected', 'api\CustomerController@customerselected');
		Route::get('getcustomers', 'api\CustomerController@getselectedcustomer');
		Route::get('customers', 'api\CustomerController@index');
		Route::get('cycles', 'CycleController@availableCycle');

		Route::get('channelselected', 'api\ChannelController@channelselected');
		Route::get('channels', 'api\ChannelController@index');

		Route::post('category/getselected', 'api\SkuController@categoryselected');
		Route::post('category', 'api\SkuController@category');
		Route::post('categories', 'api\SkuController@categories');
		Route::post('brand', 'api\SkuController@brand');
		Route::post('brand/getselected', 'api\SkuController@brandselected');
		Route::resource('network', 'api\NetworkController');
		Route::get('budgettype', 'api\BudgetTypeController@gettype');
		Route::get('materialsource', 'api\MaterialController@getsource');
	});//

});
