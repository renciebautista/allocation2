
<div id="activity">
	<table class="bordered">
		<tr nobr="true">
			<td width="80"><b>Activity Type</b></td>
			<td>{{ $activity->activitytype->activity_type }}</td>
		</tr>
		<tr nobr="true">
			<td><b>Activity Title</b></td>
			<td>{{ $activity->circular_name }}</td>
		</tr>
		<tr nobr="true">
			<td><b>Background</b></td>
			<td>{{ nl2br($activity->background) }}</td>
		</tr>
		<tr nobr="true">
			<td><b>Objectives</b></td>
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
			<td><b>Budget IO TTS</b></td>
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
		@endif
		@if(count($budgets) > 0)
		<tr nobr="true">
			<td><b>Budget IO PE</b></td>
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
		@endif
		@if(count($sku_involves) > 0)
		<tr nobr="true">
			<td><b>SKU/s Involved</b></td>
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
			<td><b>Area/s Involved</b></td>
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
			<td><b>DT Channel/s Involved</b></td>
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
			<td><b>Schemes</b></td>
			<td>
				@if(count($schemes)> 0)
				<table class="sub-table">
					<tr nobr="true">
						<th width="20"></th>
						<th width="150">Scheme Desc.</th>
						<th width="75">Item Code</th>
						<th width="75">Cost per Deal</th>
						<th width="75">Cost of Premium</th>
						<th width="75">Shopper Purchase Requirement</th>
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
			<td><b>SKU/s Involved Per Scheme</b></td>
			<td>
				@if(!empty($skuinvolves))
				<table class="sub-table">
					<tr nobr="true">
						<th width="20"></th>
						<th width="150">Host SKU Code - Description</th>
						<th width="150">Premium SKU Code - Description</th>
						<th width="150">Non ULP Premium</th>
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
						<th>Non-ULP Premium SKUs</th>
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
		<tr>
			<td><b>Timings</b></td>
			<td>
				@if(count($networks)> 0)
				<table class="sub-table timing">
					<tr>
						<th width="320">Activity</th>
						<th width="75">Start Date</th>
						<th width="75">End Date</th>
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
		@endif
		@if(count($activity_roles) > 0)
		<tr nobr="true">
			<td><b>Roles and Responsibilities</b></td>
			<td>
				@if(count($activity_roles)> 0)
				<table class="sub-table role">
					<tr nobr="true">
						<th width="160">Process Owner</th>
						<th width="160">Action Points</th>
						<th width="150">Timings</th>
					</tr>
					@foreach($activity_roles as $activity_role)
					<tr nobr="true">
						<td>{{ $activity_role->owner }}</td>
						<td>{{ $activity_role->point }}</td>
						<td>{{ $activity_role->timing }}</td>
					</tr>
					@endforeach
				</table>
				@endif
			</td>
		</tr>
		@endif
				
		@if(count($materials) > 0)
		<tr nobr="true">
			<td><b>Material Sourcing</b></td>
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
		@if(!empty($fdapermits))
		<tr nobr="true">
			<td><b>FDA Permit No.</b></td>
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
		@endif
		@if(!empty($activity->billing_remarks))
		<tr nobr="true">
			<td><b>Billing Requirements</b></td>
			<td>{{ nl2br($activity->billing_remarks) }}</td>
		</tr>
		@endif
		@if(!empty($activity->billing_date))
		<tr nobr="true">
			<td><b>Billing Deadline</b></td>
			@if($activity->billing_date != "")
			<td>{{ date_format(date_create($activity->billing_date),'M j, Y') }}</td>
			@else
			<td>N/A</td>
			@endif
			
		</tr>
		@endif
		@if(!empty($activity->instruction))
		<tr nobr="true">
			<td><b>Special Instructions</b></td>
			<td>{{ nl2br($activity->instruction) }}</td>
		</tr>
		@endif
	</table>
</div>