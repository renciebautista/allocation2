
<div id="activity">
	<table class="bordered">
		<tr nobr="true">
			<td width="100">Activity Type</td>
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
		<tr nobr="true">
			<td>SKU/s Involved</td>
			<td>
				@if(!empty($skuinvolves))
				<table class="sub-table">
					<tr nobr="true">
						<th>Host SKU Code</th>
						<th>Host SKU Description</th>
						<th>Premium SKU Code</th>
						<th>Premium SKU Description</th>
					</tr>
					@foreach($skuinvolves as $involve)
					<tr nobr="true">
						<td>{{ $involve->sap_code }}</td>
						<td>{{ $involve->sap_desc }}</td>
						<td>{{ $involve->sap_code }}</td>
						<td>{{ $involve->sap_desc }}</td>
					</tr>
					@endforeach
				</table>
				@endif
			</td>
		</tr>
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
		<tr nobr="true">
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
		<tr nobr="true">
			<td>Schemes</td>
			<td>
				@if(count($schemes)> 0)
				<table class="sub-table">
					<tr nobr="true">
						<th>Scheme Desc.</th>
						<th>Item Code</th>
						<th>Cost per Deal</th>
						<th>Cost of Premium</th>
						<th>Shopper Purchase Requirement</th>
					</tr>
					@foreach($schemes as $scheme)
					<tr nobr="true">
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
					@foreach($networks as $network)
					<tr nobr="true">
						<td>{{ $network->task }}</td>
						<td>{{ date_format(date_create($network->start_date),'M j, Y') }}</td>
						<td>{{ date_format(date_create($network->end_date),'M j, Y') }}</td>
					</tr>
					@endforeach
				</table>
				@endif
			</td>
		</tr>
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
		<tr nobr="true">
			<td>FDA Permit No.</td>
			<td>
				@if(!empty($fdapermit))
				{{ $fdapermit->permit_no }}
				@endif
			</td>
		</tr>
		<tr nobr="true">
			<td>Billing Requirements</td>
			<td>{{ nl2br($activity->billing_remarks) }}</td>
		</tr>
		<tr nobr="true">
			<td>Billing Deadline</td>
			<td>{{ date_format(date_create($activity->billing_date),'M j, Y') }}</td>
		</tr>
		<tr nobr="true">
			<td>Special Instructions</td>
			<td>{{ nl2br($activity->instruction) }}</td>
		</tr>
	</table>
</div>