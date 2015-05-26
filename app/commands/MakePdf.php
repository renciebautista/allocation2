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
		echo "Generating PDF via comamnd line using TCPDF";
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		
		$activity = Activity::find(1);
		if(!empty($activity)){
			$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
			$budgets = ActivityBudget::with('budgettype')
					->where('activity_id', $activity->id)
					->get();

			$nobudgets = ActivityNobudget::with('budgettype')
				->where('activity_id', $activity->id)
				->get();

			$schemes = Scheme::getList($activity->id);
			// $schemes = Scheme::where('id', 1)->get();

			$skuinvolves = array();
			foreach ($schemes as $scheme) {
				$involves = SchemeHostSku::where('scheme_id',$scheme->id)
					->join('pricelists', 'scheme_host_skus.sap_code', '=', 'pricelists.sap_code')
					->get();
				foreach ($involves as $value) {
					$skuinvolves[] = $value;
				}

				$scheme->allocations = SchemeAllocation::getAllocations($scheme->id);
				
			}

			$materials = ActivityMaterial::where('activity_id', $activity->id)
				->with('source')
				->get();

			$fdapermit = ActivityFdapermit::where('activity_id', $activity->id)->first();
			$networks = ActivityTiming::getTimings($activity->id,true);
			$artworks = ActivityArtwork::getList($activity->id);
			$pispermit = ActivityFis::where('activity_id', $activity->id)->first();

			//Involved Area
			$areas = ActivityCustomer::getSelectedAreas($activity->id);
			$channels = ActivityChannel::getSelectecdChannels($activity->id);
			
			// // Product Information Sheet
			$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			if(!empty($pispermit)){
				$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
			}else{
				$pis = array();
			}

			// start of pdf
			// create new PDF document
		$pdf = new ActivityPDF($orientation='P', $unit='mm', $format='LETTER', $unicode=false, $encoding='ISO-8859-1', $diskcache=false, $pdfa=false);	
		// set document information
		$pdf->SetMargins(13, 35,13);

		$pdf->AddPage();
		$header = '<div style="border-bottom: .5px solid black;padding-bottom:10px;">
					<table >
						<tr>
							<td style="font-weight: bold;width: 130px;">Circular Reference No.</td>
							<td>: 1185</td>
						</tr>
						<tr>
							<td style="font-weight: bold;width: 130px;">Activity Name</td>
							<td>: 2015-1185-ISB/IWB-HAIR-DOVE</td>
						</tr>
						<tr>
							<td style="font-weight: bold;width: 130px;">Proponent Name</td>
							<td>: Rosarah Reyes</td>
						</tr>
						<tr>
							<td style="font-weight: bold;width: 130px;">Creation Date</td>
							<td>: Feb 24, 2015</td>
						</tr>
					</table>
				</div>';
		$header .= '<div id="activity">
					<table class="bordered">
						<tr>
							<td>Activity Type</td>
							<td>ISB/IWB</td>
						</tr>
						<tr>
							<td>Activity Title</td>
							<td>ISB/IWB: Dove DTC 180ml + SH 90ml</td>
						</tr>
						<tr>
							<td>Background</td>
							<td>Dove Hair continues to get more users into the damage/premium segment of
		the hair category. With the goal of influencing uptrade and increasing
		basket size, Dove will continue to activate in store to get more users and
		influence regimen use.</td>
						</tr>
						<tr>
							<td>Objectives</td>
							<td>Increase offtake</td>
						</tr>
						<tr>
							<td>Budget IO TTS</td>
							<td>FA40321225</td>
						</tr>
						<tr>
							<td>Budget IO PE</td>
							<td>PD40321225</td>
						</tr>
						<tr>
							<td>SKU/s Involved</td>
							<td>
								<table class="sub-table">
									<tr>
										<th>Material Code</th>
										<th>Material Description</th>
									</tr>
									<tr>
										<td>21141274</td>
										<td>DOVE HC HAIR FALL PLUS GRN TOT 24X180ML</td>
									</tr>
									<tr>
										<td>21141222</td>
										<td>DOVE HC INTENSE REPAIR BLUE TOT 24X180ML</td>
									</tr>
									<tr>
										<td>21141199</td>
										<td>DOVE HC NRSHNG OIL CARE GLD TOT 24X180ML</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Channel/s Involved</td>
							<td>
								<ul>
									<li>MAG EC</li>
									<li>MAG RTM</li>
									<li>DT-MAG</li>
									<li>DRUG BIG 10</li>
									<li>MT GOLD - SM GROUP</li>
									<li>MT GOLD - PUREGOLD</li>
									<li>MT GOLD - RSC</li>
									<li>MT GOLD - SHOPWISE / RUSTANS</li>
									<li>MT GOLD - MERCURY DRUG</li>
									<li>MT GOLD - WATSONS</li>
								</ul>
							</td>
						</tr>
						<tr>
							<td>Schemes</td>
							<td>
								<table class="sub-table">
									<tr>
										<th>Scheme Desc.</th>
										<th>Item Code</th>
										<th>Cost per Deal</th>
										<th>Cost of Premium</th>
										<th>Shopper Purchase Reuirement</th>
									</tr>
									<tr>
										<td>Buy Dove DTC Intense Repair 180ml, Get FREE Dove Intense Repair Sh 90ml</td>
										<td>N/A</td>
										<td>70</td>
										<td>60.50</td>
										<td>108.90</td>
									</tr>
									<tr>
										<td>Buy Dove DTC Nourishing Oil Care 180ml, Get FREE Dove Nourishing Oil Care Sh 90ml</td>
										<td>N/A</td>
										<td>70</td>
										<td>60.50</td>
										<td>108.90</td>
									</tr>
									<tr>
										<td>Buy Dove DTC Hairfall Rescue 180ml, Get FREE Dove Hairfall Rescue Sh 90ml</td>
										<td>N/A</td>
										<td>70</td>
										<td>60.50</td>
										<td>108.90</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Timings</td>
							<td>
								<table class="sub-table timing">
									<tr>
										<th>Activity</th>
										<th>Start Date</th>
										<th>End Date</th>
									</tr>
									<tr>
										<td>Implementation Start Date</td>
										<td>Apr 13, 2015</td>
										<td>Apr 13, 2015</td>
									</tr>
									<tr>
										<td>Implementation Start Date</td>
										<td>Apr 13, 2015</td>
										<td>Apr 13, 2015</td>
									</tr>
									<tr>
										<td>Implementation Start Date</td>
										<td>Apr 13, 2015</td>
										<td>Apr 13, 2015</td>
									</tr>
									<tr>
										<td>Implementation Start Date</td>
										<td>Apr 13, 2015</td>
										<td>Apr 13, 2015</td>
									</tr>
									<tr>
										<td>Implementation Start Date</td>
										<td>Apr 13, 2015</td>
										<td>Apr 13, 2015</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Material Sourcing</td>
							<td>
								<table class="sub-table source">
									<tr>
										<th>Source</th>
										<th>Materials</th>
									</tr>
									<tr>
										<td>Ex-ULP</td>
										<td>Stickers</td>
									</tr>
									<tr>
										<td>Ex-ULP</td>
										<td>Stickers</td>
									</tr>
									<tr>
										<td>Ex-ULP</td>
										<td>Stickers</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>FDA Permit No.</td>
							<td>DOH-FDA-CCRR Permit No. 665 s. 2014</td>
						</tr>
						<tr>
							<td>Billing Deadline</td>
							<td>Jun 15, 2015</td>
						</tr>
						<tr>
							<td>Billing Requirements</td>
							<td>AAA should submit the following:
								<ol>
									<li>Banders accomplishment report</li>
									<li>Number of deals banded</li>
									<li>Manpower rate breakdown</li>
								</ol>
							</td>
						</tr>
						<tr>
							<td>Special Instructions</td>
							<td>
								<ol>
									<li> Accounts and Distributors to follow TTS budget allocation. If there will
		be savings on TTS, please declare to CMD and PMOG.</li>
		<li>Please band only with Dove DTC Blue, Gold and Hair Fall. Same variant
		banding please (Blue to Blue, Gold to Gold and HF to HF)</li>
		<li>(For MAG Accounts) Place at grab level.</li>
		<li>(For MAG accounts) Place at promotional tac bins.</li>
								</ol>
							</td>
						</tr>
					</table>
				</div>';

			$pdf->SetFont('helvetica', '', 10);
			$pdf->writeHTML($header, $ln=true, $fill=false, $reset=false, $cell=false, $align='');

			$pdf->SetFont('helvetica', '', 6);
			// create allocation table
			foreach ($schemes as $scheme) {
				$count = count($scheme->allocations);
				$loops = (int) ($count / 34);
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
						$final_alloc = $scheme->allocations[$x]->final_alloc;
						$case = 0;
						$deals = 0;
						if($activity->activitytype->uom == "CASES"){
							$case = $final_alloc;
							$deals = $final_alloc * $scheme->deals;
						}else{
							if($final_alloc > 0){
								$case = round($final_alloc / $scheme->deals);
								$deals = $final_alloc;
							}
							
						}
						$class = '';
						if((empty($scheme->allocations[$x]->customer_id)) && (empty($scheme->allocations[$x]->shipto_id))){
							$class = 'style="background-color: #d9edf7;"';
						}
						if((!empty($scheme->allocations[$x]->customer_id)) && (!empty($scheme->allocations[$x]->shipto_id))){
							$class = 'style="background-color: #fcf8e3;"';
						}

						$body .='<tr '.$class.'>
								<td style="width:20px;border: 1px solid #000000; text-align:right;">'.$num.'</td>
								<td style="width:35px;border: 1px solid #000000;">'.$scheme->allocations[$x]->group.'</td>
								<td style="width:85px;border: 1px solid #000000;">'.$scheme->allocations[$x]->area.'</td>
								<td style="width:90px;border: 1px solid #000000;">'.$scheme->allocations[$x]->sold_to.'</td>
								<td style="width:130px;border: 1px solid #000000;">'.$scheme->allocations[$x]->ship_to.'</td>
								<td style="width:50px;border: 1px solid #000000;;">'.$scheme->allocations[$x]->channel.'</td>
								<td style="width:130px;border: 1px solid #000000;">'.$scheme->allocations[$x]->outlet.'</td>
								<td style="width:40px;border: 1px solid #000000; text-align:right;">'.number_format($deals).'</td>
								<td style="width:40px;border: 1px solid #000000; text-align:right;">'.number_format($case).'</td>
								<td style="width:50px;border: 1px solid #000000;"></td>
								<td style="width:50px;border: 1px solid #000000;"></td>
							</tr>';
						$cnt++;
					}
					if(!empty($body)){
						
						$x = $i +1;
						$table = '<h2>'.$scheme->name.'</h2>
						<h2>'.$x.' of '.$loops.'</h2>
						<table width="100%" style="padding:2px;">
							<thead>
								<tr>
									<th style="width:20px;border: 1px solid #000000; text-align:center;">#</th>
									<th style="width:35px;border: 1px solid #000000; text-align:center;">GROUP</th>
									<th style="width:85px;border: 1px solid #000000; text-align:center;">AREA NAME</th>
									<th style="width:90px;border: 1px solid #000000; text-align:center;">CUSTOMER SOLD TO</th>
									<th style="width:130px;border: 1px solid #000000; text-align:center;">CUSTOMER SHIP TO NAME</th>
									<th style="width:50px;border: 1px solid #000000; text-align:center;">CHANNEL</th>
									<th style="width:130px;border: 1px solid #000000; text-align:center;">ACCOUNT NAME</th> 
									<th style="width:40px;border: 1px solid #000000; text-align:center;">IN DEALS</th>
									<th style="width:40px;border: 1px solid #000000; text-align:center;">IN CASES</th>
									<th style="width:50px;border: 1px solid #000000; text-align:center;">TTS BUDGET</th>
									<th style="width:50px;border: 1px solid #000000; text-align:center;">PE BUDGET</th>
								</tr>
							</thead>
						  	<tbody>'.
						  		$body. 
						  	'</tbody>
						</table> ';
						// echo $table;
						$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
						$pdf->writeHTML($table, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
					}
				}
			}
			// end of allocation table
			// end of pdf
		}
		$pdf->lastPage();
		$pdf->lastPage();
		$pdf_path = storage_path().$path.'/hello_world.pdf';
		$pdf->Output($pdf_path,'F');
		$this->line($pdf_path);
		$timeSecond = strtotime(date('Y-m-d H:i:s'));
		$differenceInSeconds = $timeSecond - $timeFirst;
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
