@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit {{ $activity->circular_name }}</h1>
	  	</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			{{ HTML::linkRoute('submittedactivity.index', 'Back To Activity List', array(), array('class' => 'btn btn-default')) }}

			<!-- Button trigger modal -->
			@if(($approver->status_id == 0) && ($valid))
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySubmit" data-whatever="Approve">
			  	Approve
			</button>
			<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mySubmit" data-whatever="Deny">
			  	Deny
			</button>
			@endif
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="mySubmit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
	    <div class="modal-content">
	    	{{ Form::open(array('action' => array('SubmittedActivityController@updateactivity', $activity->id), 'class' => 'bs-component','id' => 'updateactivity')) }}
	    	{{ Form::hidden('status', '', array('id' => 'status')) }}
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel"></h4>
	      	</div>
	      	<div class="modal-body">
	          	<div class="form-group">
	            	{{ Form::label('submitremarks', 'Comments:', array('class' => 'control-label')) }}
	            	{{ Form::textarea('submitremarks','',array('class' => 'form-control', 'placeholder' => 'Comments', 'size' => '30x5')) }}
	          	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        	<button class="btn btn-primary">Submit</button>	    
	     	</div>
	     	{{ Form::close() }}
	    </div>
  	</div>
</div>


<ul class="nav nav-tabs">
	<li class="active"><a aria-expanded="true" href="#activty">Activity Preview</a></li>
	<li class=""><a aria-expanded="false" href="#comments">Comments</a></li>
</ul>

<div id="myTabContent" class="tab-content">
	<div class="tab-pane fade active in" id="activty">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">Activity Preview</div>
		  	<div class="panel-body">
				<div id="activity_preview">
					<div class="ap-logo">
					{{ HTML::image('assets/images/logo.png' ,'Uniliver Logo') }}
					</div>

					<div class="ap-header">
						<h2>Unilever Philippines, Inc.</h2>
						<p>Customer Marketing Department</p>
					</div>
				
					<div class="ap-title">
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
								@if(!empty($activity->pmog[0]))
								<td>: {{ $activity->pmog[0]->getFullname() }} / {{ $activity->pmog[0]->contact_no }}</td>
								@else
								<td>:</td>
								@endif
							</tr>
							<tr>
								<td>TOP Cycle</td>
								<td>: {{ $activity->cycle->cycle_name }}</td>
							</tr>
						</table>
					</div>
				
					<div class="ap-activity">
						<table class="bordered">
							<tr>
								<td>Activity Type</td>
								<td>{{ $activity->activitytype-> activity_type }}</td>
								<td>{{ Form::text('activity_type','') }}</td>
							</tr>
							<tr>
								<td>Activity Title</td>
								<td>{{ $activity->circular_name }}</td>
								<td>{{ Form::text('activity_title','') }}</td>
							</tr>
							<tr>
								<td>Background</td>
								<td>{{ nl2br($activity->background) }}</td>
								<td>{{ Form::text('activity_background','') }}</td>
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
								<td>{{ Form::text('activity_objective','') }}</td>
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
								<td>{{ Form::text('activity_tts','') }}</td>
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
								<td>{{ Form::text('activity_pe','') }}</td>
							</tr>
							<tr>
								<td>SKU/s Involved</td>
								<td>
									@if(!empty($skuinvolves))
									<table class="sub-table sku">
										<tr>
											<th>Host SKU Code</th>
											<th>Host SKU Description</th>
											<th>Premium SKU Code</th>
											<th>Premium SKU Description</th>
										</tr>
										@foreach($skuinvolves as $involve)
										<tr>
											<td>{{ $involve->sap_code }}</td>
											<td>{{ $involve->sap_desc }}</td>
											<td>{{ $involve->sap_code }}</td>
											<td>{{ $involve->sap_desc }}</td>
										</tr>
										@endforeach
									</table>
									@endif

									@if(!empty($non_ulp))
									<table class="sub-table ulp" style="margin-top:5px;"> 
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
								<td>{{ Form::text('activity_skus','') }}</td>
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
								<td>{{ Form::text('activity_area','') }}</td>
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
								<td>{{ Form::text('activity_channel','') }}</td>
							</tr>
							<tr>
								<td>Schemes</td>
								<td>
									@if(count($schemes)> 0)
									<table class="sub-table schemes">
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
									@endif
								</td>
								<td>{{ Form::text('activity_scheme','') }}</td>
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
										@foreach($networks as $network)
										<tr>
											<td>{{ $network->task }}</td>
											<td>{{ date_format(date_create($network->start_date),'M j, Y') }}</td>
											<td>{{ date_format(date_create($network->end_date),'M j, Y') }}</td>
										</tr>
										@endforeach
									</table>
									@endif
								</td>
								<td>{{ Form::text('activity_timing','') }}</td>
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
								<td>{{ Form::text('activity_material','') }}</td>
							</tr>
							<tr>
								<td>FDA Permit No.</td>
								<td>
									@if(!empty($fdapermit))
									{{ $fdapermit->permit_no }}
									@endif
								</td>
								<td>{{ Form::text('activity_fda','') }}</td>
							</tr>
							<tr>
								<td>Billing Requirements</td>
								<td>{{ nl2br($activity->billing_remarks) }}</td>
								<td>{{ Form::text('activity_billing','') }}</td>
							</tr>
							<tr>
								<td>Billing Deadline</td>
								<td>{{ date_format(date_create($activity->billing_date),'M j, Y') }}</td>
								<td>{{ Form::text('activity_deadline','') }}</td>
							</tr>
							<tr>
								<td>Special Instructions</td>
								<td>{{ nl2br($activity->instruction) }}</td>
								<td>{{ Form::text('activity_ins','') }}</td>
							</tr>
						</table>
					</div>
					
					<div class="ap-artworks">
						<h2>Artworks</h2>
						{{ Form::text('activity_pis','') }}
						@if(!empty($artworks))
						<ul>
							@foreach($artworks as $artwork)
							<li>{{ HTML::image('images/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$artwork->hash_name ,$artwork->file_desc) }}</li>
							@endforeach
						</ul>
						@endif
					</div>

					<div class="ap-fdapermit">
						<h2>FDA Permit</h2>
						{{ Form::text('activity_pis','') }}
						@if(!empty($fdapermit))
						<ul>
							<li>{{ HTML::image('fdapermit/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/'.$fdapermit->hash_name ,$fdapermit->file_desc) }}</li>
						</ul>
						@endif
					</div>

					<div class="ap-codes">
						<h2>Barcodes / Case Codes Per Scheme</h2>
						{{ Form::text('activity_barcode','') }}
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
					<div class="ap-product">
						<h2>Product Information Sheet</h2>
						{{ Form::text('activity_pis','') }}
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

					<div class="ap-allocations">
						<h2>Allocations</h2>
						{{ Form::text('activity_allocation','') }}
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
		  	</div>
		</div>
		
	</div>

	<!-- attachment details -->
	<div class="tab-pane fade" id="comments">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">Comments</div>
		  	<div class="panel-body">
				<ul class="comment">
					@foreach($comments as $comment)
	                <li class="left clearfix">
	                    <div class="comment-body clearfix">
	                        <div class="header">
	                            <strong class="primary-font">{{ $comment->createdby->getFullname()}} 
	                            	<p class="{{ $comment->class }}">({{ $comment->comment_status }})</p>
	                            </strong> 
	                            <small class="pull-right text-muted">
	                                <i class="fa fa-clock-o fa-fw"></i> {{ Carbon::parse($comment->created_at)->subMinutes(2)->diffForHumans()}}
	                            </small>
	                        </div>
	                        <p>{{ $comment->comment }}</p>
	                    </div>
	                </li>
	                @endforeach
	            </ul>
		  	</div>
		</div>
	</div>

	
</div>

@stop


@section('page-script')

$('.nav-tabs a').click(function (e) {
	// No e.preventDefault() here
	$(this).tab('show');
});

if(location.hash.length > 0){
	var activeTab = $('[href=' + location.hash + ']');
	activeTab && activeTab.tab('show');
}

$('#mySubmit').on('show.bs.modal', function (event) {
 	var button = $(event.relatedTarget) // Button that triggered the modal
  	var action = button.data('whatever') // Extract info from data-* attributes
  	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  	var modal = $(this)
  	modal.find('.modal-title').text(action+" activity")
  	status = 0;
  	if(action == "Approve"){
  		status = 1;
  	}
  	modal.find('#status').val(status);
})

$("form[id='updateactivity']").on("submit",function(e){
	var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize(),
			method: method,
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					$('#mySubmit').modal('hide');
					location.reload();	
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
	}
	
	e.preventDefault();
});

$("#updateactivity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		status: "required",
		submitremarks: "required"

	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	}
});
@stop