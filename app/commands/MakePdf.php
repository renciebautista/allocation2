<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakePdf extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:pdf';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate activity PDF file.';

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
		set_time_limit(0);
		$id = $this->argument('id');
		$this->line("Generating PDF via comamnd line using TCPDF");
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		// echo $id;
		$activity = Activity::find($id);
		if(!empty($activity)){
			$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
			$approvers = ActivityApprover::getNames($activity->id);

			$objectives = ActivityObjective::where('activity_id', $activity->id)->get();
			
			$budgets = ActivityBudget::with('budgettype')->where('activity_id', $activity->id)->get();
			$nobudgets = ActivityNobudget::with('budgettype')->where('activity_id', $activity->id)->get();

			$schemes = Scheme::getList($activity->id);


			$tradedeal = Tradedeal::getActivityTradeDeal($activity);
			if($tradedeal != null){
				$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();
				$trade_allocations = TradedealSchemeAllocation::getSummary($tradedeal);
			}

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

			//Involved Area
			$areas = ActivityCutomerList::getSelectedAreas($activity->id);
			$channels = ActivityChannelList::getSelectecdChannels($activity->id);

			$materials = ActivityMaterial::where('activity_id', $activity->id)->get();

			$fdapermits = ActivityFdapermit::where('activity_id', $activity->id)->get();

			$networks = ActivityTiming::getTimings($activity->id,true);

			$activity_roles = ActivityRole::getListData($activity->id);

			$artworks = ActivityArtwork::getList($activity->id);
			
			$pispermit = ActivityFis::where('activity_id', $activity->id)->first();
			
			$sku_involves = ActivitySku::getInvolves($activity->id);

			// // Product Information Sheet
			$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			$pis = array();
			if(!empty($pispermit)){
				$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
			}

			// start of pdf
			// create new PDF document
			$pdf = new ActivityPDF($orientation='P', $unit='mm', $format='LETTER', $unicode=false, $encoding='ISO-8859-1', $diskcache=false, $pdfa=false);	
			// set document information
			$pdf->SetMargins(10, 32,10);
			$pdf->setListIndentWidth(0);	
			// set auto page breaks
			// $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			$pdf->AddPage();

			$pdf->SetFont('helvetica', '', 8);

			$header = "";
			$header .= View::make('pdf.style')->render();
			$header .= View::make('pdf.title',compact('activity','approvers', 'planner'))->render();
			$header .= View::make('pdf.activity',compact('activity','schemes','networks','materials', 'objectives',
				'budgets','nobudgets', 'skuinvolves', 'areas', 'channels','fdapermits', 'sku_involves', 'activity_roles'))->render();
			
			$pdf->writeHTML(iconv("UTF-8", "CP1252//TRANSLIT", $header) , $ln=true, $fill=false, $reset=false, $cell=false, $align='');

			$x = $pdf->getX();
			$y = $pdf->getY();

			$h = $pdf->getPageHeight();
			if($h-$y < 50){
				$pdf->AddPage();
			}

			if(count($artworks) > 0){
				$artwork = View::make('pdf.artwork')->render();
				$pdf->writeHTML($artwork , $ln=true, $fill=false, $reset=false, $cell=false, $align='');
				$x = $pdf->getX();
				$y = $pdf->getY();
				$cnt = 0;
				$max_h = 0;
				foreach($artworks as $artwork){
					$pdf->SetXY($x, $y);
					$cnt++;

					$image_file =  storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$artwork->hash_name;
					$pdf->Image($image_file, $x, $y, 30, 0, '', '', '', true, 150, '', false, false, 0, false, false, false);

					if($max_h < ($pdf->getImageRBY() - $y)){
						$max_h = $pdf->getImageRBY() - $y;
					}

					
					$x+=31;

					if($x > 165){
						$y+=$max_h;
						$y++;
						$x = 10;
						$max_h = 0;
					}else{
						$y = $pdf->getY();
					}

					if($h-$y < 40){
						$pdf->AddPage();
						$x = $pdf->getX();
						$y = $pdf->getY();
					}
				
				}

				$y = $pdf->getY();
				
				if($max_h < 31){
					$max_h += 31;
				}
				$max_h += 10;
				$y+=$max_h;
				$pdf->SetXY($x, $y);

				$h = $pdf->getPageHeight();
				if($h-$y < 60){
					$pdf->AddPage();
				}
			}
			
			//barcode
			if(count($schemes) > 0){
				$w_codes = false;
				foreach ($schemes as $scheme) {
					if($scheme->item_barcode !== ""){
						$w_codes = true;
					}
					if($scheme->item_casecode !== ""){
						$w_codes = true;
					}
				}
				if($w_codes){
					$barcodes = View::make('pdf.barcodes',compact('schemes'))->render();
					$pdf->writeHTML($barcodes, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
					// define barcode style

					$style = array(
				    'position' => '',
				    'align' => 'C',
				    'stretch' => false,
				    'fitwidth' => true,
				    'cellfitalign' => 'C',
				    'border' => false,
				    'hpadding' => 'auto',
				    'vpadding' => 'auto',
				    'fgcolor' => array(0,0,0),
				    'bgcolor' => false, //array(255,255,255),
				    'text' => true,
				    'font' => 'helvetica',
				    'fontsize' => 8,
				    'stretchtext' => 4
					);
					$str= "";
					$cnt= 1;
					// $style['cellfitalign'] = 'C';
					foreach ($schemes as $scheme) {
						$y = $pdf->GetY();
						if(($scheme->item_barcode  !== "") || ($scheme->item_casecode !== "")){
							if($scheme->item_barcode  !== ""){
								$barcode[$cnt] = $pdf->serializeTCPDFtagParameters(array($scheme->item_barcode, 'EAN13', '', '', '', 18, 0.4, $style, ''));       
							}

							if($scheme->item_casecode !== ""){
								$casecode[$cnt] = $pdf->serializeTCPDFtagParameters(array($scheme->item_casecode, 'I25', '', '', '', 18, 0.4, $style, '')); 
								
							}
							

							if($scheme->item_barcode !== ""){
								$str .='<tr nobr="true"><td align="center">'.$scheme->name.'<br>
								<tcpdf method="write1DBarcode" params="'.$barcode[$cnt] .'" />
								</td>';
							}else{
								$str .='<tr nobr="true"><td align="center"></td>';
							}
							if($scheme->item_casecode !== ""){
								$str .='<td align="center">'.$scheme->name.'<br>
								<tcpdf method="write1DBarcode" params="'.$casecode[$cnt] .'" />
								</td></tr>';
							}else{
								$str .='<td align="center"></td></tr>';
							}
						}
						$cnt++;
					}


					$str_table='<table cellspacing="0" cellpadding="2" border=".1px;">            
					<tr nobr="true">
						<td align="center" style="background-color: #000000;color: #FFFFFF;">Promo Item Barcode</td>
						<td align="center" style="background-color: #000000;color: #FFFFFF;">Promo Item Casecode</td>
					</tr>';
					$str_table .= $str;
					$str_table .='</table>';
					// echo $str_table;
					$pdf->writeHTML($str_table, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
				}
				
			}
			
			if(count($fdapermits) > 0){
				$artwork = View::make('pdf.fdapermit')->render();
				$pdf->writeHTML($artwork , $ln=true, $fill=false, $reset=false, $cell=false, $align='');
				$x = $pdf->getX();
				$y = $pdf->getY();
				$cnt = 0;
				$max_h = 0;
				foreach($fdapermits as $permit){
					$file = explode(".", $permit->file_desc);
					$file_ex = strtolower($file[1]);
					if(($file_ex  != "pdf") &&  ($file_ex != "xps")){
						$pdf->AddPage();
						$x = $pdf->getX();
						$y = $pdf->getY();
						$image_file = $path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$permit->hash_name;
						$pdf->Image($image_file, $x, $y, 0, 200, '', '', '', true, 150, '', false, false, 0, false, false, true,false);
					}
				}
				$pdf->AddPage();
			}
				
			if(count($pis) > 0){

				$pis_view = "";
				$pis_view .= View::make('pdf.style')->render();
				$pis_view .= View::make('pdf.pis',compact('activity','pis'))->render();
				$pdf->writeHTML($pis_view , $ln=true, $fill=false, $reset=false, $cell=false, $align='');
			}

			$required_budget_type = ActivityTypeBudgetRequired::required($activity->activity_type_id);

			if(!isset($tradedeal)){
				if(count($schemes) > 0){
					$pdf->SetFont('helvetica', '', 6);
					foreach ($schemes as $scheme) {
						$count = count($scheme->allocations);
						$loops = (int) ($count / 34);
						if($count %34 != 0) {
						  $loops = $loops+1;
						}
						$scheme_count  = count($schemes);
						$body ='';
						
						$cnt = 0;
						for ($i = 0; $i <= $loops; $i++) { 
							$allocs = array();
							$body ='';
							$last_count =  $cnt+34;
							for ($x=$cnt; $x <= $last_count; $x++) { 
								if($cnt == $count){
									break;
								}
								$num = $x + 1;
								$class = '';
								if((empty($scheme->allocations[$x]->customer_id)) && (empty($scheme->allocations[$x]->shipto_id))){
									$class = 'style="background-color: #d9edf7;"';
								}
								if((!empty($scheme->allocations[$x]->customer_id)) && (!empty($scheme->allocations[$x]->shipto_id))){
									$class = 'style="background-color: #fcf8e3;"';
								}

								$tts = '';
								$pe = '';
								if(in_array(1,$required_budget_type)){
									$tts = '<td style="width:50px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->tts_budget,2).'</td>';
								}
								
								if(in_array(2,$required_budget_type)){
									$pe = '<td style="width:50px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->pe_budget,2).'</td>';
								}

								$body .='<tr '.$class.'>
										<td style="width:20px;border: 1px solid #000000; text-align:right;">'.$num.'</td>
										<td style="width:30px;border: 1px solid #000000;">'.$scheme->allocations[$x]->group.'</td>
										<td style="width:80px;border: 1px solid #000000;">'.$scheme->allocations[$x]->area.'</td>
										<td style="width:100px;border: 1px solid #000000;">'.$scheme->allocations[$x]->sold_to.'</td>
										<td style="width:120px;border: 1px solid #000000;">'.$scheme->allocations[$x]->ship_to.'</td>
										<td style="width:60px;border: 1px solid #000000;;">'.$scheme->allocations[$x]->channel.'</td>
										<td style="width:130px;border: 1px solid #000000;">'.$scheme->allocations[$x]->outlet.'</td>
										<td style="width:50px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->in_deals).'</td>
										<td style="width:50px;border: 1px solid #000000; text-align:right;">'.number_format($scheme->allocations[$x]->in_cases).'</td>'.
										$tts.
										$pe.
									'</tr>';
								$cnt++;
							}
							if(!empty($body)){
								$x = $i +1;
								$table = '<h2>'.$scheme->name.'</h2>
								<h2>'.$x.' of '.$loops.'</h2>';

								if($scheme->compute == 2){
									$table .= '<h2>Allocation is not system generated. It is manually computed by the proponent.</h2>';
								}
								$tts_header = '';
								$pe_header = '';
								if(in_array(1,$required_budget_type)){
									$tts_header = '<th style="width:50px;border: 1px solid #000000; text-align:center;">TTS BUDGET</th>';
								}
								
								if(in_array(2,$required_budget_type)){
									$pe_header = '<th style="width:50px;border: 1px solid #000000; text-align:center;">PE BUDGET</th>';
								}

								$table .= '<table width="100%" style="padding:2px;">
									<thead>
										<tr>
											<th style="width:20px;border: 1px solid #000000; text-align:center;">#</th>
											<th style="width:30px;border: 1px solid #000000; text-align:center;">GROUP</th>
											<th style="width:80px;border: 1px solid #000000; text-align:center;">AREA NAME</th>
											<th style="width:100px;border: 1px solid #000000; text-align:center;">CUSTOMER SOLD TO</th>
											<th style="width:120px;border: 1px solid #000000; text-align:center;">CUSTOMER SHIP TO NAME</th>
											<th style="width:60px;border: 1px solid #000000; text-align:center;">CHANNEL</th>
											<th style="width:130px;border: 1px solid #000000; text-align:center;">ACCOUNT NAME</th> 
											<th style="width:50px;border: 1px solid #000000; text-align:center;">IN DEALS</th>
											<th style="width:50px;border: 1px solid #000000; text-align:center;">IN CASES</th>'.
											$tts_header.
											$pe_header.
										'</tr>
									</thead>
								  	<tbody>'.
								  		$body. 
								  	'</tbody>
								</table> ';
								$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
								$pdf->writeHTML($table, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
							}
						}
					}
				}
			}else{

				$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
				$pdf->SetFont('helvetica', '', 8);
				$tradedeal_view = "";
				$tradedeal_view .= View::make('pdf.style')->render();
				$tradedeal_view .= View::make('pdf.tradedeal_view',compact('tradedealschemes'))->render();
				// Helper::debug($tradedeal_view);
				$pdf->writeHTML($tradedeal_view , $ln=true, $fill=false, $reset=false, $cell=false, $align='');

				$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
				$pdf->SetFont('helvetica', '', 8);
				$tradedeal_view = "";
				$tradedeal_view .= View::make('pdf.style')->render();
				$tradedeal_view .= View::make('pdf.allocationsummary',compact('tradedeal_skus', 'trade_allocations'))->render();
				// Helper::debug($tradedeal_view);
				$pdf->writeHTML($tradedeal_view , $ln=true, $fill=false, $reset=false, $cell=false, $align='');
			}
			


			
			$pdf_name = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
			$filepath = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;

			// dd($filepath);

			$path = storage_path().$filepath;
			if(!File::exists($path)) {
				File::makeDirectory($path);
			}
			
			$pdf_path = storage_path().$filepath.'/'. str_replace(":","_", $pdf_name).'.pdf';
			$pdf->Output($pdf_path,'F');
			$this->line($pdf_path);
			$timeSecond = strtotime(date('Y-m-d H:i:s'));
			$differenceInSeconds = $timeSecond - $timeFirst;
			$this->line( 'Time used ' . $differenceInSeconds . " sec");

			$activity->pdf = 1;
			$activity->update();
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
