<div>
						<table id="scheme_summary" class="table table-bordered table-hover table-responsive" width="100%">
						<!--<table id="scheme_summary" class="table table-condensed table-bordered display compact ">-->
							<thead>
								<tr>
									<th rowspan="2" style="width:20px;">Group</th>
									<th rowspan="2">Area</th>
									<th rowspan="2">Sold To</th>
									<th rowspan="2">Ship To</th>
									<th rowspan="2">Channel</th>
									<th rowspan="2">Outlet</th>
									@foreach($schemes as $scheme)
										<th class="text-center" colspan="4">{{ $scheme->name }}</th>
									@endforeach
								</tr>
								
								<tr>
									
									@foreach($schemes as $scheme)
										<th>Deals</th>
										<th>Cases</th>
										<th>TTS Budget</th>
										<th>PE Budget</th>
									@endforeach
								</tr>
							</thead>
						  	<tbody>
						  		@if(count($scheme_customers) == 0)
						  		<tr>
						  			<td colspan="15">No record found!</td>
						  		</tr>
						  		@endif

								@foreach($scheme_customers as $scheme_customer)
								<?php 
								$class = '';
									if((empty($scheme_customer->customer_id)) && (empty($scheme_customer->shipto_id))){
										$class = 'style="background-color: #d9edf7;"';
									}
									if((!empty($scheme_customer->customer_id)) && (!empty($scheme_customer->shipto_id))){
										$class = 'style="background-color: #fcf8e3;"';
									}
								 ?>

								<tr {{ $class }}>
									<td style="width:10px;">{{ $scheme_customer->group }}</td>
									<td style="width:50px;">{{ $scheme_customer->area }}</td>
									<td style="width:100px;">{{ $scheme_customer->sold_to }}</td>
									<td style="width:100px;">{{ $scheme_customer->ship_to }}</td>
									<td style="width:50px;">{{ $scheme_customer->channel }}</td>
									<td style="width:100px;">{{ $scheme_customer->outlet }}</td>
									@foreach($schemes as $scheme)
										@if($activity->activitytype->uom == "CASES")
										<td class="text-right">0</td>
										<td class="text-right">
											@if(isset($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]))
											{{ number_format($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]) }}
											@endif
										</td>
										@else
										<td class="text-right">
											@if(isset($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]))
											{{ number_format($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]) }}
											@endif
										</td>
										<td class="text-right">0</td>
										@endif
										
										<td class="text-right">1</td>
										<td class="text-right">1</td>
									@endforeach
								</tr>
								@endforeach
						  	</tbody>
						</table> 
					</div>