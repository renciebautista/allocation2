<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<style type="text/css">
			body{
				font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    			font-size: 12px;
				padding: 0px;
				margin: 0px;
			}
			h2{
				margin-bottom: 2px;
			}
			p{
				margin: 0px;
			}
			table {
				width: 100%;
			    border-collapse: collapse;
			    border-spacing: 0;
			}
			#container{
				width: 1000px;
    			margin-right: auto;
				margin-left: auto;
			/*	border-left: 1px solid #ddd;
				border-right: 1px solid #ddd;*/
				padding-left: 40px;
				padding-right: 40px;
			}
			#header{
				padding-top: 20px;
				margin-bottom: 30px;
			}
			#header p{
				font-size: 13px;
			}
			#title{
				padding-bottom: 15px;
				border-bottom: .8px solid #000000;
			}
			#title table tr > td:first-child {
				font-weight: bold;
				vertical-align: top;
				width: 150px;

			}
			#activity{
				padding-top: 15px;
			}
			#activity ul {
				list-style-type: none;
				margin-top: 2px;
				margin-bottom: 2px;
			}
			#activity ul li {
				margin-left: -39px;
			}
			#activity table tr > td:first-child {
				font-weight: bold;
				width: 150px;
				vertical-align: top;
			}
			.bordered{
				border: 1px solid #000000;

			}
			.bordered tr > th{
				border: 1px solid #000000;
				padding: 5px;
			}
			.bordered tr > td{
				border: 1px solid #000000;
				padding: 5px;
			}
			.sub-table{
				width: 100%;
			}
			.sub-table tr > th {
				background-color: #000000;
				color: #FFFFFF;
			}

			#activity table .sub-table tr > td {
				text-align: center;
				font-weight: normal;
			}
			#activity table .sub-table.timing tr > td:first-child {
				font-weight: normal;
				text-align: left;
				width: 60%;
			}
			

			#activity table .sub-table.role tr > td:first-child {
				font-weight: normal;
				text-align: left;
				width: 40%;
			}
			#activity table .sub-table.role tr > td:last-child {
				font-weight: normal;
				width: 20%;
			}

			#activity table .sub-table.source tr > td:first-child {
				font-weight: normal;
				text-align: left;
				width: 40%;
			}


			#logo {
			    position: relative;
			}
			#logo img {
			    position: absolute;
			    top: 0px;
			    right: 0px;
			}
			#artworks ul {
				margin: 0;
				padding: 0;
				list-style-type: none; 
			}
			#artworks ul li { display: inline; }
			#artworks ul li a { text-decoration: none; padding: .2em 1em;}

			#fdapermit ul {
				margin: 0;
				padding: 0;
				list-style-type: none; 
			}
			#fdapermit ul li { display: inline; }
			#fdapermit ul li a { text-decoration: none; padding: .2em 1em;}
			#codes{
				margin-top: 20px;
			}
			#codes table {
				width: 100%;
			}
			#codes table  tr > th {
			  	border: 1px solid #000000;
			  	padding: 5px;
			 	background-color: #000000;
  				color: #FFFFFF;

			}
			#codes table  tr > td {
			  	border: 1px solid #000000;
			  	padding: 5px;
			  	text-align: center;
			}
			#allocations {
				margin-top: 20px;
			}
			#allocations table {
				margin-top: 5px;
				margin-bottom: 10px;
			}

			#allocations table  tr > th {
			  	border: 1px solid #000000;
			  	padding: 5px;
			 	background-color: #000000;
  				color: #FFFFFF;
  				text-align: left;
  				font-size: 11px;
  				text-align: center;
			}
			#allocations table  tr > td {
			  	border: 1px solid #000000;
			  	padding: 5px;
			  	font-size: 10px;
			}
			#allocations table  tr > td:first-child {
				width: 5%;
			}
			.pis-table{
				margin-top: 10px;
			}
			#product{
				font-size: 11px;
			}
			.pis-table  tr > th {
			  	border: 1px solid #000000;
			  	padding: 5px;
			 	background-color: #000000;
  				color: #FFFFFF;
  				text-align: center;
			}

			.pis-table  tr > td {
			  	border: 1px solid #000000;
			  	padding: 5px;
			  	font-size: 10px;
			}
			.p-head  tr > td:first-child{
				width: 200px;
			}
			.alloc-header {
				margin-bottom: 5px;
			}
		</style>
	</head>

	<body>
		<div id="container">
			<div id="logo">
			{{ HTML::image('assets/images/logo.png' ,'Uniliver Logo') }}
			</div>

			<div id="header">
				<h2>Unilever Philippines, Inc.</h2>
				<p>Customer Marketing Department</p>
			</div>
		
			<div id="title">
				<table>
					<tr>
						<td>Circular Reference No.</td>
						<td>: {{ $activity->id }}</td>
					</tr>
					<tr>
						<td>Activity Name</td>
						<td>: {{ $activity->activity_code }}</td>
					</tr>
					<tr>
						<td>TOP Cycle</td>
						<td>: {{ $activity->cycle->cycle_name }}</td>
					</tr>
					<tr>
						<td>Proponent Name</td>
						<td>: {{ $activity->createdby->getFullname() }} 
							@if(!empty($activity->createdby->contact_no))
							/ {{ $activity->createdby->contact_no }}
							@endif
						</td>
					</tr>
					<tr>
						<td>PMOG Partner</td>

						@if(!empty($activity->pmog[0]))
						<td>: {{  $activity->pmog[0]->getFullname() }} 
							@if(!empty($activity->pmog[0]->contact_no))
							/ {{ $activity->pmog[0]->contact_no }}
							@endif
						</td>
						@else
						<td>:</td>
						@endif
					</tr>

					<tr>
						<td>Approvers</td>
						@if(!empty($approvers))
						<td>
							<?php $first = false; ?>
							@foreach($approvers as $approver)
							@if(!$first)
							:
							<?php $first = true; ?>
							@else
							&nbsp
							@endif
							 {{$approver->first_name}} {{$approver->last_name}}</br>
							@endforeach
						</td>
						@else
						<td>:</td>
						@endif
					</tr>
					
				</table>
			</div>
		
			<div id="activity">
				<table class="bordered">
					<tr>
						<td>Activity Type</td>
						<td>{{ $activity->activitytype-> activity_type }}</td>
					</tr>
					<tr>
						<td>Activity Title</td>
						<td>{{ $activity->circular_name }}</td>
					</tr>
					<tr>
						<td>Background</td>
						<td>{{ nl2br($activity->background) }}</td>
					</tr>
					<tr>
						<td>Objectives</td>
						<td>
							<ul>
							@foreach($activity->objectives as $objective)
							<li>{{ $objective->objective }}</li>
							@endforeach
							</ul>
						</td>
					</tr>
					<tr>
						<td>Budget IO TTS</td>
						<td>
							@if(!empty($budgets))
							<ul>
							@foreach($budgets as $budget)
							@if($budget->budget_type_id == 1)
							<li>{{ $budget->io_number }} - {{ $budget->remarks}}</li>
							@endif
							@endforeach
							
							</ul>
							@endif
						</td>
					</tr>
					<tr>
						<td>Budget IO PE</td>
						<td>
							@if(!empty($budgets))
							<ul>
							@foreach($budgets as $budget)
							@if($budget->budget_type_id == 2)
							<li>{{ $budget->io_number }}  - {{ $budget->remarks}}</li>
							@endif
							@endforeach
							
							</ul>
							@endif
						</td>
					</tr>

					<tr>
						<td>SKU/s Involved</td>
						<td>
							@if(count($sku_involves)> 0)
							<table class="sub-table">
								<tr>
									<th style="width:16%">SKU Code</th>
									<th >Description</th>
								</tr>
								@foreach($sku_involves as $sku_involve)
								<tr>
									<td>{{ $sku_involve->sap_code }}</td>
									<td>{{ $sku_involve->sap_desc }}</td>
								</tr>
								@endforeach
							</table>
							@endif
						</td>
					</tr>
					
					<tr>
						<td>Area/s Involved</td>
						<td>
							@if(!empty($areas))
							<ul>
								@foreach($areas as $area)
								<li>{{ $area}}</li>
								@endforeach
							</ul>
							@endif
						</td>
					</tr>
					<tr>
						<td>DT Channel/s Involved</td>
						<td>
							@if(!empty($areas))
							<ul>
								@foreach($channels as $channel)
								<li>{{ $channel }}</li>
								@endforeach
							</ul>
							@endif
						</td>
					</tr>
					<tr>
						<td>Schemes</td>
						<td>
							@if(count($schemes)> 0)
							<table class="sub-table">
								<tr>
									<th style="width:3%"></th>
									<th>Scheme Desc.</th>
									<th style="width:16%">Item Code</th>
									<th style="width:16%">Cost per Deal</th>
									<th style="width:16%">Cost of Premium</th>
									<th style="width:16%">Shopper Purchase Requirement</th>
								</tr>
								<?php $cnt =1; ?>
								@foreach($schemes as $scheme)
								<tr>
									<td>{{ $cnt++ }}</td>
									<td>{{ $scheme->name }}</td>
									<td>{{ ($scheme->item_code == '') ? 'N/A' :  $scheme->item_code }}</td>
									<td>{{ number_format($scheme->ulp,2) }}</td>
									<td>{{ number_format($scheme->srp_p,2) }}</td>
									<td>{{ number_format($scheme->pr,2) }}</td>
								</tr>
								@endforeach
							</table>
							@endif
						</td>
					</tr>
					<tr>
						<td>SKU/s Involved Per Scheme</td>
						<td>
							@if(!empty($skuinvolves))
							<table class="sub-table">
								<tr>
									<th style="width:3%"></th>
									<th>Host SKU Code - Description</th>
									<th style="width:32%">Premium SKU Code - Description</th>
									<th style="width:32%">Non ULP Premium</th>
								</tr>
								<?php $cnt =1; ?>
								@foreach($skuinvolves as $key => $sku)
								<tr>
									<td>{{ $cnt++ }}</td>
									<td>
										@foreach($sku['involves'] as $involve)
										{{ $involve->sap_code}} - {{ $involve->sap_desc}}
										@endforeach
									</td>
									<td>
										@foreach($sku['premiums'] as $premium)
										{{ $premium->sap_code}} - {{ $premium->sap_desc}}
										@endforeach
									</td>
									<td>
										@foreach($sku['non_ulp'] as $non_ulp)
										{{ $non_ulp }}
										@endforeach
									</td>
								</tr>
								@endforeach
							</table>
							@endif

							
						</td>
					</tr>
					<tr>
						<td>Timings</td>
						<td>
							@if(count($networks)> 0)
							<table class="sub-table timing">
								<tr>
									<th>Activity</th>
									<th>Start Date</th>
									<th>End Date</th>
								</tr>
								<?php $last_date; ?>
								@foreach($networks as $network)
								<tr>
									<td>{{ $network->task }}</td>
									<td>{{ date_format(date_create($network->final_start_date),'M j, Y') }}</td>
									<td>{{ date_format(date_create($network->final_end_date),'M j, Y') }}</td>
									
								</tr>
								@endforeach
								<tr>
									<td>IMPLEMENTATION DATE</td>
									<td>{{ date_format(date_create($activity->eimplementation_date),'M j, Y') }}</td>
									<td>{{ date_format(date_create($activity->end_date),'M j, Y') }}</td>
								</tr>
							</table>
							@endif
						</td>
					</tr>
					<tr>
						<td>Roles and Responsibilities</td>
						<td>
							@if(count($activity_roles)> 0)
							<table class="sub-table role">
								<tr>
									<th>Process Owner</th>
									<th>Action Points</th>
									<th>Timings</th>
								</tr>
								@foreach($activity_roles as $activity_role)
								<tr>
									<td>{{ $activity_role->owner }}</td>
									<td>{{ $activity_role->point }}</td>
									<td>{{ date_format(date_create($activity_role->timing),'M j, Y') }}</td>
									
								</tr>
								@endforeach

							</table>
							@endif
						</td>
					</tr>
					<tr>
						<td>Material Sourcing</td>
						<td>
							@if(count($materials)> 0)
							<table class="sub-table source">
								<tr>
									<th>Source</th>
									<th>Materials</th>
								</tr>
								@foreach($materials as $material)
								<tr>
									<td>{{ $material->source->source }}</td>
									<td>{{ $material->material }}</td>
								</tr>
								@endforeach
							</table>
							@endif
						</td>
					</tr>
					<tr>
						<td>FDA Permit No.</td>
						<td>
							@if(!empty($fdapermit))
							{{ $fdapermit->permit_no }}
							@endif
						</td>
					</tr>
					<tr>
						<td>Billing Requirements</td>
						<td>{{ nl2br($activity->billing_remarks) }}</td>
					</tr>
					<tr>
						<td>Billing Deadline</td>
						<td>{{ date_format(date_create($activity->billing_date),'M j, Y') }}</td>
					</tr>
					<tr>
						<td>Special Instructions</td>
						<td>{{ nl2br($activity->instruction) }}</td>
					</tr>
				</table>
			</div>
			
			<div id="artworks">
				<h2>Artworks</h2>
				@if(!empty($artworks))
				<ul>
					@foreach($artworks as $artwork)
					<li>{{ HTML::image('images/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$artwork->hash_name ,$artwork->file_desc) }}</li>
					@endforeach
				</ul>
				@endif
			</div>

			<div id="fdapermit">
				<h2>FDA Permit</h2>
				@if(!empty($fdapermit))
				<?php 
					$file = explode(".", $fdapermit->file_desc);

				?>
				<ul>
				@if($file[1] != "pdf")
				
					<li>{{ HTML::image('fdapermit/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$fdapermit->hash_name ,$fdapermit->file_desc) }}</li>
				
				@else
				<li>{{ HTML::linkAction('ActivityController@fdadownload',$fdapermit->file_desc, $fdapermit->id, array('class' => 'btn btn-success btn-xs')) }}</li>
				@endif
				</ul>
				@endif
			</div>

			<div id="codes">
				<h2>Barcodes / Case Codes Per Scheme</h2>
				@if(!empty($schemes))
				<table>
					<tr>
						<th width="50%">Barcode</th>
						<th>Case Code</th>
					</tr>
					@foreach($schemes as $scheme)
					<tr>
						<td>
							@if(!empty($scheme->item_barcode))
							{{ $scheme->name }}<br>
							{{ DNS1D::getBarcodeSVG($scheme->item_barcode, "EAN13",2,100) }} <br>
							{{$scheme->item_barcode}}
							@endif
						</td>
						<td>
							@if(!empty($scheme->item_casecode))
							{{ $scheme->name }}<br>
							{{ DNS1D::getBarcodeSVG($scheme->item_casecode, "I25",3,100) }} <br>
							{{$scheme->item_casecode}}
							@endif
						</td>
						
					</tr>
					@endforeach
				</table>
				@endif
			</div>

			@if(count($pis) > 0)
			<div id="product">
				<h2>Product Information Sheet</h2>
				<table class="p-head bordered">
					<tr>
						<td>Product Category</td>
						<td>{{ $pis[2][1] }}</td>
					</tr>
					<tr>
						<td>Sub Category</td>
						<td>{{ $pis[3][1] }}</td>
					</tr>
					<tr>
						<td>Brand / Scheme</td>
						<td>{{ $pis[4][1] }}</td>
					</tr>
					<tr>
						<td>Target Market</td>
						<td>{{ $pis[5][1] }}</td>
					</tr>
					<tr>
						<td>Product Features</td>
						<td>{{ $pis[6][1] }}</td>
					</tr>
					<tr>
						<td>Major Competitors</td>
						<td>{{ $pis[7][1] }}</td>
					</tr>
					<tr>
						<td>Minor Competitors</td>
						<td>{{ $pis[8][1] }}</td>
					</tr>
					<?php 
						$x = 0; 
						for ($i=8; $i < count($pis); $i++) { 
							if($pis[$i][7] == "Case Dimensions (MM)"){
								$x = $i+2;
								break;
							}
						}
					?>
					<tr>
						<td colspan="2">
							<table class="pis-table">
								<tr>
									<th colspan="7"></th>
									<th colspan="3">Case Dimensions (MM)</th>
								</tr>
								<tr>
									<th>Item Description</th>
									<th>Pack Size</th>
									<th>Pack Color</th>
									<th>Units/Case</th>
									<th>Product Barcode</th>
									<th>Product Code</th>
									<th>Case/Ton</th>
									<th>L</th>
									<th>W</th>
									<th>H</th>
								</tr>
								@for($x1 = $x; $x1 < count($pis); $x1++)
									<?php if($pis[$x1][1] == "Product Dimension (MM)"){
										break;
										
									} ?>
									@if($pis[$x1][0] != "")
									<tr>
										@for($_x = 0; $_x < 10 ; $_x++)
											@if($_x == 6)
											<td>{{ number_format($pis[$x1][$_x],2) }}</td>
											@else
											<td>{{ $pis[$x1][$_x] }}</td>
											@endif
										
										@endfor
									</tr>
									@endif
									<?php $x = $x1; ?>
								@endfor
							</table>

							<table class="pis-table">
								<tr>
									<th></th>
									<th colspan="3">Product Dimension (MM)</th>
									<th colspan="3"></th>
									<th colspan="3">Maximum Case Stocking</th>
								</tr>
								<tr>
									<th>Item Description</th>
									<th>L</th>
									<th>W</th>
									<th>H</th>
									<th>Product Casecode</th>
									<th>Net Wgt Kg</th>
									<th>Gross Wgt KG</th>
									<th>CS/Layer</th>
									<th>Layer/Pallet</th>
									<th>Pallets/Tier</th>
								</tr>
								<?php $x = $x+3; ?>
								@for($x2 = $x; $x2 < count($pis); $x2++)
									<?php if($pis[$x2][8] == "Trade margins"){
										break;
										$x = $x2;
									} ?>
									@if($pis[$x2][0] != "")
									<tr>
										@for($_x2 = 0; $_x2 < 10 ; $_x2++)
										<td>{{ $pis[$x2][$_x2] }}</td>
										@endfor
									</tr>
									@endif
									<?php $x = $x2; ?>
								@endfor
							</table>

							<table class="pis-table">
								<tr>
									<th colspan="8"></th>
									<th colspan="2">Trade margins</th>
								</tr>
								<tr>
									<th>Item Description</th>
									<th>Total Shelf Life (SLED in Days)</th>
									<th>Pieces/Inner Pack (regular SKU with inner pack/carton)</th>
									<th>Product Barcode</th>
									<th>Product Code</th>
									<th>LPAT/CS</th>
									<th>LPAT per PC/MP</th>
									<th>SRP Per PC/MP</th>
									<th>%</th>
									<th>Absolute</th>
								</tr>
								<?php $x = $x+3; ?>
								@for($x3 = $x; $x3 < count($pis); $x3++)
									@if($pis[$x3][0] != "")
									<tr>
										@for($_x3 = 0; $_x3 < 10 ; $_x3++)
											@if($_x3 == 8)
											<td>{{ round(number_format($pis[$x3][$_x3],2)) }}</td>
											@else
											<td>{{ $pis[$x3][$_x3] }}</td>
											@endif
										
										@endfor
									</tr>
									@endif
								@endfor
							</table>
						</td>
					</tr>
				</table>
			</div>
			@endif

			<div id="allocations">
				<h2>Allocations</h2>
				@foreach($schemes as $scheme)

				<?php 
					$count = count($scheme->allocations);
					$loops = (int) ($count / 29);
					$scheme_count  = count($schemes);
					$scheme_loops = (int) ($scheme_count / 3);
					// echo $scheme_loops;
					$body ='';
					
					$cnt = 0;
				 ?>

				@for($i = 0; $i <= $loops; $i++)
					<?php 
					$allocs = array();
					$body ='';
					$last_count =  $cnt+29;
					 ?>
					@for ($x=$cnt; $x <= $last_count; $x++) 
						<?php if($cnt == $count){
							break;
						} 
						$num = $x + 1;
						// $final_alloc = $scheme->allocations[$x]->final_alloc;
						// $case = 0;
						// $deals = 0;
						// if($activity->activitytype->uom == "CASES"){
						// 	$case = $final_alloc;
						// 	$deals = $final_alloc * $scheme->deals;
						// }else{
						// 	if($final_alloc > 0){
						// 		$case = round($final_alloc / $scheme->deals);
						// 		$deals = $final_alloc;
						// 	}
							
						// }
						$class = '';
						if((empty($scheme->allocations[$x]->customer_id)) && (empty($scheme->allocations[$x]->shipto_id))){
							$class = 'style="background-color: #d9edf7;"';
						}
						if((!empty($scheme->allocations[$x]->customer_id)) && (!empty($scheme->allocations[$x]->shipto_id))){
							$class = 'style="background-color: #fcf8e3;"';
						}
						$body .='<tr '.$class.'>
							<td style="text-align:right;">'.$num.'</td>
							<td style="width:40px;border: 1px solid #000000">'.$scheme->allocations[$x]->group.'</td>
							<td style="width:120px;border: 1px solid #000000">'.$scheme->allocations[$x]->area.'</td>
							<td style="width:150px;border: 1px solid #000000">'.$scheme->allocations[$x]->sold_to.'</td>
							<td style="width:150px;border: 1px solid #000000">'.$scheme->allocations[$x]->ship_to.'</td>
							<td style="width:60px;border: 1px solid #000000">'.$scheme->allocations[$x]->channel.'</td>
							<td style="width:200px;border: 1px solid #000000">'.$scheme->allocations[$x]->outlet.'</td>
							<td style="text-align:right;">'.number_format($scheme->allocations[$x]->in_deals).'</td>
							<td style="text-align:right;">'.number_format($scheme->allocations[$x]->in_cases).'</td>
							<td style="text-align:right;">'.number_format($scheme->allocations[$x]->tts_budget,2).'</td>
							<td style="text-align:right;">'.number_format($scheme->allocations[$x]->pe_budget,2).'</td>
						</tr>';
						$cnt++;
						?>
					@endfor
					@if(!empty($body))
					<h2 class="alloc-header" style="float:left;">{{ $scheme->name }}</h2>
					<h2 class="alloc-header" style="float:right;">{{$i+1}} of {{$loops}}</h2>
					<table width="100%" style="padding:2px;">
						
						<thead>
							<tr>
								<th style="width:20px;">#</th>
								<th style="width:40px;border: 1px solid #000000">GROUP</th>
								<th style="width:120px;border: 1px solid #000000">AREA NAME</th>
								<th style="width:150px;border: 1px solid #000000">CUSTOMER SOLD TO</th>
								<th style="width:150px;border: 1px solid #000000">CUSTOMER SHIP TO NAME</th>
								<th style="width:60px;border: 1px solid #000000">CHANNEL</th>
								<th style="width:200px;border: 1px solid #000000">ACCOUNT NAME</th> 
								<th>ALLOCATION IN DEALS</th>
								<th>ALLOCATION IN CASES</th>
								<th>TTS BUDGET</th>
								<th>PE BUDGET</th>
							</tr>
						</thead>
					  	<tbody>
					  		{{ $body }}
					  	</tbody>
					</table> 
					@endif
				@endfor
				@endforeach
			</div>
		</div>
		
	</body>
</html>
