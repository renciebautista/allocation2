<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class PdfTableSeeder extends Seeder {

	public function run()
	{
			$activity = Activity::find(68);
	$scheme_customers = SchemeAllocation::getCustomers($activity->id);
	$schemes = Scheme::where('activity_id', $activity->id)
				->orderBy('created_at', 'desc')
				->get();
	$scheme_allcations = SchemeAllocation::getAllocation($activity->id);


	// create new PDF document
	$pdf = new ActivityPDF($orientation='P', $unit='mm', $format='LETTER', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false);	
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

	$pdf->SetFont('helvetica', '', 8);
	
	$count = count($scheme_customers);
	$loops = (int) ($count / 29);
	$scheme_count  = count($schemes);
	$scheme_loops = (int) ($scheme_count / 3);
	//echo $scheme_loops;
	$body ='';
	

	$cnt = 0;
	for($i = 0; $i <= $loops; $i++){
		$allocs = array();
		$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
		$body ='';
		$last_count =  $cnt+29;
		for ($x=$cnt; $x <= $last_count; $x++) { 
			if($cnt == $count){
				break;
			}
			$allocs[] = md5($scheme_customers[$x]->group.'.'.$scheme_customers[$x]->area.'.'.$scheme_customers[$x]->sold_to.'.'.$scheme_customers[$x]->ship_to.'.'.$scheme_customers[$x]->channel.'.'.$scheme_customers[$x]->outlet);
			$body .='<tr>
				<td style="width:40px;border: 1px solid #000000">'.$scheme_customers[$x]->group.'</td>
				<td style="width:120px;border: 1px solid #000000">'.$scheme_customers[$x]->area.'</td>
				<td style="width:150px;border: 1px solid #000000">'.$scheme_customers[$x]->sold_to.'</td>
				<td style="width:150px;border: 1px solid #000000">'.$scheme_customers[$x]->ship_to.'</td>
				<td style="width:60px;border: 1px solid #000000">'.$scheme_customers[$x]->channel.'</td>
				<td style="width:200px;border: 1px solid #000000">'.$scheme_customers[$x]->outlet.'</td>
			</tr>';
			$cnt++;
		}

		$alloc = '<table width="100%" style="padding:2px;">
					<thead>
						<tr>
							<th style="width:720px;border: 1px solid #000000" colspan="6">Customers</th>
						</tr>
						<tr>
							<th style="width:40px;border: 1px solid #000000">Group</th>
							<th style="width:120px;border: 1px solid #000000">Area</th>
							<th style="width:150px;border: 1px solid #000000">Sold To</th>
							<th style="width:150px;border: 1px solid #000000">Ship To</th>
							<th style="width:60px;border: 1px solid #000000">Channel</th>
							<th style="width:200px;border: 1px solid #000000">Outlet</th> 
						</tr>
					</thead>
				  	<tbody>'.$body.'
				  	</tbody>
				</table> ';

		$pdf->writeHTML($alloc, $ln=true, $fill=false, $reset=false, $cell=false, $align='');

		
		$a_count = 0;
		for($s = 0; $s <= $scheme_loops; $s++){
			$pdf->AddPage($orientation = 'L',$format = '',$keepmargins = false,$tocpage = false );
			$scheme_head ='';
			$scheme_body ='';
			$scheme_alloc ='';
			$last_acount =  $a_count+3;
			$scheme_alloc ='';

			for($a = $a_count; $a < $last_acount; $a++){
				if($a_count == $scheme_count){
					break;
				}
				$scheme_head .= '<th style="width:240px;border: 1px solid #000000" colspan="4">'.$schemes[$a]->name.'</th>';


				$scheme_body .= '<th style="width:40px;border: 1px solid #000000">Deals</th>
							<th style="width:40px;border: 1px solid #000000">Cases</th>
							<th style="width:80px;border: 1px solid #000000">TTS Budget</th>
							<th style="width:80px;border: 1px solid #000000">PE Budget</th>';
				
				// $scheme_allcations[$schemes[$a]->id];


				$scheme_alloc .= '<td style="width:40px;border: 1px solid #000000"></td>
				<td style="width:40px;border: 1px solid #000000">1</td>
				<td style="width:80px;border: 1px solid #000000">1</td>
				<td style="width:80px;border: 1px solid #000000">1</td>';
				$a_count++;
			}

			$scheme_allochead ='';
			foreach ($allocs as $value) {
					// echo $scheme_allcations[$schemes[$a]->id][$value];
					$scheme_allochead .= '<tr>'.$scheme_alloc.'</tr>';
				}

			$_scheme = '<table width="100%" style="padding:2px;">
					<thead>
						<tr>'.$scheme_head.'
							
						</tr>
						<tr>
							'.$scheme_body.'
						</tr>
					</thead>
				  	<tbody>'.$scheme_allochead.'
				  	</tbody>
				</table> ';


			$pdf->writeHTML($_scheme, $ln=true, $fill=false, $reset=false, $cell=false, $align='');
			
		}
		
	}

	

	$pdf->lastPage();
	$pdf->Output('hello_world.pdf','D');
	}

}