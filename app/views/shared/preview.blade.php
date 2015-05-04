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
				margin-top: 20px;
			}

			#allocations table  tr > th {
			  	border: 1px solid #000000;
			  	padding: 5px;
			 	background-color: #000000;
  				color: #FFFFFF;
  				text-align: left;
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
						<td>Proponent Name</td>
						<td>: {{ $activity->createdby->getFullname() }} / {{ $activity->createdby->contact_no }}</td>
					</tr>
					<tr>
						<td>PMOG Partner</td>
						<td>: 
							@if(!empty($planner->planner))
							{{ $planner->planner->getFullname() }} / {{ $planner->planner->contact_no }}
							@endif
						</td>
					</tr>
					<tr>
						<td>TOP Cycle</td>
						<td>: {{ $activity->cycle->cycle_name }}</td>
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
						<td>{{ $activity->background }}</td>
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
							<ul>
							@foreach($budgets as $budget)
							@if($budget->budget_type_id == 1)
							<li>{{ $budget->io_number }} - {{ $budget->remarks}}</li>
							@endif
							@endforeach
							
							</ul>
						</td>
					</tr>
					<tr>
						<td>Budget IO PE</td>
						<td>
							<ul>
							@foreach($budgets as $budget)
							@if($budget->budget_type_id == 2)
							<li>{{ $budget->io_number }}  - {{ $budget->remarks}}</li>
							@endif
							@endforeach
							
							</ul>
						</td>
					</tr>
					<tr>
						<td>SKU/s Involved</td>
						<td>
							<table class="sub-table">
								<tr>
									<th>Material Code</th>
									<th>Material Description</th>
								</tr>
								@foreach($skuinvolves as $involve)
								<tr>
									<td>{{ $involve->sap_code }}</td>
									<td>{{ $involve->sap_desc }}</td>
								</tr>
								@endforeach
							</table>
						</td>
					</tr>
					<tr>
						<td>Area/s Involved</td>
						<td>
							<ul>
								@foreach($areas as $area)
								<li>{{ $area}}</li>
								@endforeach
							</ul>
						</td>
					</tr>
					<tr>
						<td>DT Channel/s Involved</td>
						<td>
							<ul>
								@foreach($channels as $channel)
								<li>{{ $channel->channel_name }}</li>
								@endforeach
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
									<th>Shopper Purchase Requirement</th>
								</tr>
								@foreach($schemes as $scheme)
								<tr>
									<td>{{ $scheme->name }}</td>
									<td>{{ ($scheme->item_code == '') ? 'N/A' :  $scheme->item_code }}</td>
									<td>{{ number_format($scheme->ulp,2) }}</td>
									<td>{{ number_format($scheme->srp_p,2) }}</td>
									<td>{{ number_format($scheme->pr,2) }}</td>
								</tr>
								@endforeach
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
								@foreach($networks as $network)
								<tr>
									<td>{{ $network->task }}</td>
									<td>{{ date_format(date_create($network->start_date),'M j, Y') }}</td>
									<td>{{ date_format(date_create($network->end_date),'M j, Y') }}</td>
								</tr>
								@endforeach
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
								@foreach($materials as $material)
								<tr>
									<td>{{ $material->source->source }}</td>
									<td>{{ $material->material }}</td>
								</tr>
								@endforeach
							</table>
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
				<ul>
					@foreach($artworks as $artwork)
					<li>{{ HTML::image('images/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$artwork->hash_name ,$artwork->file_desc) }}</li>
					@endforeach
				</ul>
			</div>

			<div id="codes">
				<h2>Schemes Case Codes / Bar Codes</h2>
				<table>
					<tr>
						<th>Case Code</th>
						<th>Bar  Code</th>
					</tr>
					@foreach($schemes as $scheme)
					<tr>
						<td>
							{{ $scheme->name }}<br>
							{{ DNS1D::getBarcodeSVG($scheme->item_casecode, "I25",3,100) }} <br>
							{{$scheme->item_casecode}}
						</td>
						<td>
							{{ $scheme->name }}<br>
							{{ DNS1D::getBarcodeSVG($scheme->item_barcode, "EAN13",2,100) }} <br>
							{{$scheme->item_barcode}}
						</td>
					</tr>
					@endforeach
				</table>
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
							if($pis[$i][6] == "Case Dimensions (MM)"){
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
									<?php if($pis[$x2][4] == "Trade margins"){
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
				<?php 
					$count = count($scheme_customers);
					$loops = (int) ($count / 29);
					$scheme_count  = count($schemes);
					$scheme_loops = (int) ($scheme_count / 3);
					//echo $scheme_loops;
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
						$body .='<tr>
							<td>'.$num.'</td>
							<td style="width:40px;border: 1px solid #000000">'.$scheme_customers[$x]->group.'</td>
							<td style="width:120px;border: 1px solid #000000">'.$scheme_customers[$x]->area.'</td>
							<td style="width:150px;border: 1px solid #000000">'.$scheme_customers[$x]->sold_to.'</td>
							<td style="width:150px;border: 1px solid #000000">'.$scheme_customers[$x]->ship_to.'</td>
							<td style="width:60px;border: 1px solid #000000">'.$scheme_customers[$x]->channel.'</td>
							<td style="width:200px;border: 1px solid #000000">'.$scheme_customers[$x]->outlet.'</td>
						</tr>';
						$cnt++;
						?>
					@endfor
					<table width="100%" style="padding:2px;">
						<thead>
							<tr>
								<th style="width:720px;border: 1px solid #000000" colspan="7">Customers</th>
							</tr>
							<tr>
								<th>#</th>
								<th style="width:40px;border: 1px solid #000000">Group</th>
								<th style="width:120px;border: 1px solid #000000">Area</th>
								<th style="width:150px;border: 1px solid #000000">Sold To</th>
								<th style="width:150px;border: 1px solid #000000">Ship To</th>
								<th style="width:60px;border: 1px solid #000000">Channel</th>
								<th style="width:200px;border: 1px solid #000000">Outlet</th> 
							</tr>
						</thead>
					  	<tbody>
					  		{{ $body }}
					  	</tbody>
					</table> 

					<table>
						<thead>
							<tr>
								
							</tr>
							<tr>
								
							</tr>
						</thead>
					  	<tbody>
					  	</tbody>
					</table> 
				@endfor
			</div>
		</div>
		
	</body>
</html>
