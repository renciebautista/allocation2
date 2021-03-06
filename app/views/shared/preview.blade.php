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
				padding-left: 40px;`
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

			#allocations table tr td{
				vertical-align: top;
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
				@include('shared.partial_activty_title')
			</div>
		
			<div id="activity">
				<table class="bordered">
					<tr>
						<td>Activity Type</td>
						<td>{{ $activity->activitytype_desc }}</td>
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
							@foreach($objectives as $objective)
							<li>{{ $objective->objective_desc }}</li>
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
									@if(!empty($budget->remarks))
									<li>{{ $budget->io_number }} - {{ $budget->remarks}}</li>
									@else
									<li>{{ $budget->io_number }}</li>
									@endif
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
									@if(!empty($budget->remarks))
									<li>{{ $budget->io_number }}  - {{ $budget->remarks}}</li>
									@else
									<li>{{ $budget->io_number }}</li>
									@endif
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
						<td>Channel/s Involved</td>
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
					@if(!$activity->activitytype->with_tradedeal)
					<tr>
						<td>Schemes</td>
						<td>
							@if(count($schemes)> 0)
							<table class="sub-table">
								<tr>
									<th style="width:3%"></th>
									<th>Scheme Desc.</th>
									<th style="width:16%">Promo Item Code</th>
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
					@else
					
					@endif
					@if(is_null($tradedeal))
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
										{{ $involve->sap_code}} - {{ $involve->sap_desc}} </br>
										@endforeach
									</td>
									<td>
										@foreach($sku['premiums'] as $premium)
										{{ $premium->sap_code}} - {{ $premium->sap_desc}} </br>
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
					@endif

					<tr>
						<td>Timings</td>
						<td>
							@if(count($networks)> 0)
							<table class="sub-table timing">
								<tr>
									<th>Activity</th>
									<th style="width:14%">Start Date</th>
									<th style="width:14%">End Date</th>
								</tr>
								<?php $last_date; ?>
								@foreach($networks as $network)
								<tr>
									<td>{{ $network->task }}</td>
									<td><?php echo ($network->final_start_date != null) ?  date_format(date_create($network->final_start_date),'M j, Y') : '';?></td>
									<td><?php echo ($network->final_end_date != null) ?  date_format(date_create($network->final_end_date),'M j, Y') : '';?></td>
									
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
									<td>{{ $activity_role->timing }}</td>
									
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
									<td>{{ $material->source_desc }}</td>
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
							@if(!empty($fdapermits))
							<ul>
								@foreach($fdapermits as $fdapermit)
								<li>{{ $fdapermit->permit_no }}</li>
								@endforeach
							</ul>
							@endif
						</td>
					</tr>
					<tr>
						<td>Billing Requirements</td>
						<td>{{ nl2br($activity->billing_remarks) }}</td>
					</tr>
					<tr>
						<td>Billing Deadline</td>
						@if($activity->billing_date != "")
						<td>{{ date_format(date_create($activity->billing_date),'M j, Y') }}</td>
						@else
						<td></td>
						@endif
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

			<div id="codes">
				<h2>Barcodes / Case Codes Per Scheme</h2>
				@if(!empty($schemes))
				<table>
					<tr>
						<th width="50%">Promo Item Barcode</th>
						<th>Promo Item Casecode</th>
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

			<div id="fdapermit">
				<h2>FDA Permit</h2>
				@if(!empty($fdapermits))
				
				<ul>
					@foreach($fdapermits as $permit)
					<?php 
						$file = explode(".", $fdapermit->file_desc);
					?>
					<?php $file_ex = strtolower($file[1]); ?>
					@if(($file_ex != "pdf") && ($file_ex != "xps"))
						<li>{{ HTML::image('fdapermit/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$permit->hash_name ,$permit->file_desc) }}</li>
					@else
						<li>{{ HTML::linkAction('ActivityController@fdadownload',$permit->file_desc, $permit->id, array('class' => 'btn btn-success btn-xs')) }}</li>
					@endif
					@endforeach
				</ul>
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

			@if(!isset($tradedeal))
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
							$tts = '<td style="text-align:right;">'.number_format($scheme->allocations[$x]->tts_budget,2).'</td>';
						}
						
						if(in_array(2,$required_budget_type)){
							$pe = '<td style="text-align:right;">'.number_format($scheme->allocations[$x]->pe_budget,2).'</td>';
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
							<td style="text-align:right;">'.number_format($scheme->allocations[$x]->in_cases).'</td>'.
							$tts.
							$pe.
						'</tr>';


						$cnt++;
						?>
					@endfor
					@if(!empty($body))

					<h2 class="alloc-header" style="float:left;">{{ $scheme->name }}</h2>
					<h2 class="alloc-header" style="float:right;">{{$i+1}} of {{$loops}}</h2>
					@if($scheme->compute == 2)
					<h3 style="clear: both;">Allocation is not system generated. It is manually computed by the proponent.</h3>
					<br>
					@endif
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

								@if(in_array(1,$required_budget_type))
								<th>TTS BUDGET</th>
								@endif
								@if(in_array(2,$required_budget_type))
								<th>PE BUDGET</th>
								@endif
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
			@else
			<div id="allocations">
				<h2>BBFREE Schemes</h2>
				<h3>Download attached excel file for allocations.</h3>
				<table class="sub-table">
								<tr>
									<th style="width:14%">Activity</th>
									<th style="width:10%">Scheme Code</th>
									<th style="width:14%">Scheme Description</th>
									<th style="width:10%">Host Code</th>
									<th style="width:14%">Host Description</th>
									<th style="width:10%">Premium Code / PIMS Code</th>
									<th style="width:14%">Premium Description</th>
									<th >Channels Involved</th>
								</tr>
								
								@foreach($tradedealschemes as $scheme)
										<?php $x = false; ?>
										<?php $host_cnt = 1; ?>
										@if(!empty($scheme->host_skus))
										<?php $host_cnt = count($scheme->host_skus); ?>
											@foreach($scheme->host_skus as $host_sku)
											<?php $y = false; ?>
											@if(!$x)
												<tr>
												<td rowspan="{{$host_cnt}}">{{ $scheme->name }}</td>
											@endif

											@if(!$y)
												<td>{{ $host_sku->scheme_code }} </td>
												<td>{{ $host_sku->scheme_desc }}</td>
												<td>{{ $host_sku->host_code }}</td>
												<td>{{ $host_sku->desc_variant }}</td>
												<td>{{ $host_sku->pre_code }}</td>
												<td>{{ $host_sku->pre_variant }}</td>
												<?php $y = true; ?>
											@endif
											
											@if(!$x)
												<?php $x = true; ?>
												<td rowspan="{{$host_cnt}}" >
												@if(!empty($scheme->rtms))
													@foreach($scheme->rtms as $rtm)
													{{ $rtm->sold_to }} </br>
													@endforeach
												@endif
												
												@if(!empty($scheme->channels))
													@foreach($scheme->channels as $channel)
													{{ $channel->sub_type_desc }} </br>
													@endforeach
												@endif
												</td>
											@endif
											</tr>
											@endforeach
										@endif
									
								@endforeach				
				</table>

			
				@include('shared.allocationsummary')
			</div>
			@endif
		</div>
		
	</body>
</html>
