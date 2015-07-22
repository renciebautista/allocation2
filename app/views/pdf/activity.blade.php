
<div id="activity">
	<table class="bordered">
		<tr nobr="true">
			<td width="80">Activity Type</td>
			<td>{{ $activity->activitytype-> activity_type }}</td>
		</tr>
		<tr nobr="true">
			<td>Activity Title</td>
			<td>{{ $activity->circular_name }}</td>
		</tr>
		<tr nobr="true">
			<td>Background</td>
			<td>{{ nl2br($activity->background) }}</td>
		</tr>
		<tr nobr="true">
			<td>Objectives</td>
			<td>
				<ul style="margin: 0px; padding: 0px;">
				@foreach($activity->objectives as $objective)
				<li>{{ $objective->objective }}</li>
				@endforeach
				</ul>
			</td>
		</tr>
		@if(count($budgets) > 0)
		<tr nobr="true">
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
		@endif
		@if(count($budgets) > 0)
		<tr nobr="true">
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
		@endif
		@if(count($sku_involves) > 0)
		<tr nobr="true">
			<td>SKU/s Involved</td>
			<td>
				@if(!empty($sku_involves))
				<table class="sub-table">
					<tr nobr="true">
						<th>SKU Code</th>
						<th>Description</th>
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
		@endif
		@if(count($areas) > 0)
		<tr nobr="true">
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
		@endif
		@if(count($channels) > 0)
		<tr nobr="true">
			<td>DT Channel/s Involved</td>
			<td>
				@if(!empty($channels))
				<ul>
					@foreach($channels as $channel)
					<li>{{ $channel }}</li>
					@endforeach
				</ul>
				@endif
			</td>
		</tr>
		@endif
		@if(count($schemes) > 0)
		<tr nobr="true">
			<td>Schemes</td>
			<td>
				@if(count($schemes)> 0)
				<table class="sub-table">
					<tr nobr="true">
						<th width="20"></th>
						<th width="130">Scheme Desc.</th>
						<th>Item Code</th>
						<th>Cost per Deal</th>
						<th>Cost of Premium</th>
						<th>Shopper Purchase Requirement</th>
					</tr>
					<?php $cnt =1; ?>
					@foreach($schemes as $scheme)
					<tr nobr="true">
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
		@endif
		@if(count($skuinvolves) > 0)
		<tr nobr="true">
			<td>SKU/s Involved Per Scheme</td>
			<td>
				@if(!empty($skuinvolves))
				<table class="sub-table">
					<tr nobr="true">
						<th width="20"></th>
						<th width="142">Host SKU Code - Description</th>
						<th width="143">Premium SKU Code - Description</th>
						<th width="143">Non ULP Premium</th>
					</tr>
					<?php $cnt =1; ?>
					@foreach($skuinvolves as $key => $sku)
					<tr nobr="true">
						<td>{{ $cnt++ }}</td>
						<td>
							@if(!empty($sku['involves']))
							<ul>
							@foreach($sku['involves'] as $involve)
							<li>{{ $involve->sap_code}} - {{ $involve->sap_desc}}</li>
							@endforeach
							</ul>
							@endif
						</td>
						<td>
							@if(!empty($sku['premiums']))
							<ul>
							@foreach($sku['premiums'] as $premium)
							<li>{{ $premium->sap_code}} - {{ $premium->sap_desc}}</li>
							@endforeach
							</ul>
							@endif
						</td>
						<td>
							@if(!empty($sku['non_ulp']))
							<ul>
							@foreach($sku['non_ulp'] as $_non_ulp)
							@if($_non_ulp != "")
							<li>{{ $_non_ulp }}</li>
							@endif
							@endforeach
							</ul>
							@endif
						</td>
					</tr>
					@endforeach
				</table>
				@endif

				@if(!empty($non_ulp))
				<table class="sub-table" style="margin-top:5px;"> 
					<tr>
						<th>Non ULP Premium SKUs</th>
					</tr>
					@foreach($non_ulp as $ulp)
					<tr>
						<td>{{ $ulp }}</td>
					</tr>
					@endforeach
				</table>
				@endif
			</td>
		</tr>
		@endif
		@if(count($networks) > 0)
		<tr nobr="true">
			<td>Timings</td>
			<td>
				@if(count($networks)> 0)
				<table class="sub-table timing">
					<tr nobr="true">
						<th>Activity</th>
						<th>Start Date</th>
						<th>End Date</th>
					</tr>
					<?php $last_date; ?>
					@foreach($networks as $network)
					<tr nobr="true">
						<td>{{ $network->task }}</td>
						<td><?php echo ($network->final_start_date != null) ?  date_format(date_create($network->final_start_date),'m/d/Y') : '';?></td>
						<td><?php echo ($network->final_end_date != null) ?  date_format(date_create($network->final_end_date),'m/d/Y') : '';?></td>
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
		@endif
		@if(count($materials) > 0)
		<tr nobr="true">
			<td>Material Sourcing</td>
			<td>
				@if(count($materials)> 0)
				<table class="sub-table source">
					<tr nobr="true">
						<th>Source</th>
						<th>Materials</th>
					</tr>
					@foreach($materials as $material)
					<tr nobr="true">
						<td>{{ $material->source->source }}</td>
						<td>{{ $material->material }}</td>
					</tr>
					@endforeach
				</table>
				@endif
			</td>
		</tr>
		@endif
		@if(!empty($fdapermit))
		<tr nobr="true">
			<td>FDA Permit No.</td>
			<td>
				@if(!empty($fdapermit))
				{{ $fdapermit->permit_no }}
				@endif
			</td>
		</tr>
		@endif
		@if(!empty($activity->billing_remarks))
		<tr nobr="true">
			<td>Billing Requirements</td>
			<td>{{ nl2br($activity->billing_remarks) }}</td>
		</tr>
		@endif
		@if(!empty($activity->billing_date))
		<tr nobr="true">
			<td>Billing Deadline</td>
			<td>{{ date_format(date_create($activity->billing_date),'M j, Y') }}</td>
		</tr>
		@endif
		@if(!empty($activity->instruction))
		<tr nobr="true">
			<td>Special Instructions</td>
			<td>{{ nl2br($activity->instruction) }}</td>
		</tr>
		@endif
	</table>
</div>