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
			{{ HTML::linkRoute('downloadedactivity.index', 'Back To List', array(), array('class' => 'btn btn-default')) }}
			<button class="btn btn-primary disabled">Submit to Approver</button>

			<button class="btn btn-warning disabled">Recall</button>
		</div>
	</div>

</div>


<ul class="nav nav-tabs">
	<li class="active"><a aria-expanded="true" href="#activty" data-toggle="tab">Activity Details</a></li>
	<li class=""><a aria-expanded="false" href="#customer" data-toggle="tab">Customer Details</a></li>
	<li class=""><a aria-expanded="false" href="#schemes" data-toggle="tab">Schemes</a></li>
	<li class=""><a aria-expanded="false" href="#budget" data-toggle="tab">Budget Details</a></li>
	<li class=""><a aria-expanded="false" href="#timings" data-toggle="tab">Timings Details</a></li>
	<li class=""><a aria-expanded="false" href="#materials" data-toggle="tab">Material Sourcing</a></li>
	<li class=""><a aria-expanded="false" href="#attachment" data-toggle="tab">Attachment</a></li>
</ul>
<div id="myTabContent" class="tab-content">

	<!-- activity details -->
  	<div class="tab-pane fade active in" id="activty">
  		<br>
  		{{ Form::open(array('route' => array('activity.update', $activity->id), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateActivity')) }}
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('status', 'Status', array('class' => 'control-label')) }}
								{{ Form::text('status',$activity->status->status, array('class' => 'form-control','readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

  			<div class="row">
  				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
								{{ Form::select('scope', array('0' => 'PLEASE SELECT') + $scope_types, $activity->scope_type_id, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Approver -->
			<div class="row">
  				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('planner', 'PMOG Planner', array('class' => 'control-label')) }}
								{{ Form::select('planner', array('0' => 'PLEASE SELECT') + $planners, (!is_null($sel_planner)) ? $sel_planner->user_id : 0, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								
								{{ Form::label('approver', 'Activity Approver', array('class' => 'control-label')) }}
								{{ Form::select('approver[]', $approvers, $sel_approver , array('id' => 'approver', 'class' => 'form-control', 'multiple' => 'multiple')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Approver -->

			<!-- Cycle -->
			<div class="row">
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div id="multiselect" class="col-lg-12">
								{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
								{{ Form::select('activity_type',array('0' => 'PLEASE SELECT') + $activity_types, $activity->activity_type_id, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('lead_time', 'Activity Leadtime (days)', array('class' => 'control-label')) }}
								{{ Form::text('lead_time', $activity->duration,array('class' => 'form-control', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('implementation_date', 'Target Implementation Date', array('class' => 'control-label')) }}
								{{ Form::text('implementation_date',date_format(date_create($activity->eimplementation_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
								
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('download_date', 'Target Download Date ', array('class' => 'control-label')) }}
								{{ Form::text('download_date',date_format(date_create($activity->edownload_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				
				
			</div>
			<!-- End Cycle -->

			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-6">
								{{ Form::label('cycle', 'TOP Cycle', array('class' => 'control-label')) }}
								{{ Form::select('cycle', array('0' => 'PLEASE SELECT') + $cycles, $activity->cycle_id, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('activity_title', 'Activity Title', array('class' => 'control-label')) }}
								{{ Form::text('activity_title',$activity->circular_name	,array('class' => 'form-control', 'placeholder' => 'Activity Title')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
								{{ Form::select('division',  array('0' => 'PLEASE SELECT') + $divisions, null, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div id="multiselect" class="col-lg-12">
								{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
								<select class="form-control" data-placeholder="SELECT CATEGORY" id="category" name="category[]" multiple="multiple" ></select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
								<select class="form-control" data-placeholder="SELECT BRAND" id="brand" name="brand[]" multiple="multiple" ></select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('objective', 'SKU/s Involved', array('class' => 'control-label' )) }}
								{{ Form::select('objective[]', $objectives, null, array('id' => 'objective', 'class' => 'form-control', 'multiple' => 'multiple')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
  				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('objective', 'Objectives', array('class' => 'control-label' )) }}
									{{ Form::select('objective[]', $objectives, null, array('id' => 'objective', 'class' => 'form-control', 'multiple' => 'multiple')) }}
							</div>
						</div>
					</div>
					
				</div>
			</div>

			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('background', 'Background and Rationale', array('class' => 'control-label')) }}
									{{ Form::textarea('background',$activity->background,array('class' => 'form-control', 'placeholder' => 'Background and Rationale')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
  		</div>
  	</div>

  	<!-- customer details -->
  	<div class="tab-pane fade" id="customer">
  		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
  					<div id="tree3"></div>
					{{ Form::hidden('customers', null, array('id' => 'customers')) }}
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>

  		</div>
  	</div>

  	<!-- scheme details -->
  	<div class="tab-pane fade" id="schemes">
  		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<table class="table table-striped table-hover ">
					  <thead>
					    <tr>
					      <th>#</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr>
					      <td>1</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr>
					      <td>2</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="info">
					      <td>3</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="success">
					      <td>4</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="danger">
					      <td>5</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="warning">
					      <td>6</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="active">
					      <td>7</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					  </tbody>
					</table> 
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
  		</div>
  	</div>

  	<!-- budget details -->
  	<div class="tab-pane fade" id="budget">
		<br>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">With Existing Budget IO</h3>
			</div>
			<div class="panel-body">
				<div id="c_with_budget">
	  				<div class="row">
		  				<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										<table id="budget_table" class="table table-striped table-hover ">
										  	<thead>
										    	<tr>
										      		<th>Type</th>
										      		<th>IO</th>
										      		<th>Amount</th>
										      		<th>Start Date</th>
										      		<th>End Date</th>
										      		<th>Remarks</th>
										    	</tr>
										  	</thead>
										  	@if(count($budgets)> 0)
										  	<tbody>
										  		@foreach ($budgets as $budget)
										  		<tr id="{{ $budget->id }}">
										  			<td>{{ $budget->budgettype->budget_type }}</td>
											  		<td>{{ strtoupper($budget->io_number) }}</td>
											  		<td>{{ number_format($budget->amount,2) }}</td>
											  		<td>{{ date_format(date_create($budget->start_date),'m/d/Y') }}</td>
											  		<td>{{ date_format(date_create($budget->end_date),'m/d/Y') }}</td>
											  		<td>{{ $budget->remarks }}</td>
										  		</tr>
										  		@endforeach
										  	</tbody>
										  	@endif
										  	
										</table> 
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
		  				<div class="col-lg-6">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('scope', 'Billing Deadline', array('class' => 'control-label')) }}
										{{ Form::text('circular_name','',array('class' => 'form-control', 'placeholder' => 'Billing Deadline')) }}
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										
										{{ Form::label('approver', 'Billing Requirements', array('class' => 'control-label')) }}
										{{ Form::textarea('background','',array('class' => 'form-control', 'placeholder' => 'Billing Requirements' , 'size' => '50x5')) }}
									</div>
								</div>
							</div>
						</div>
					</div>
			
				</div>
			</div>
		</div>

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">IO for Raising</h3>
			</div>
			<div class="panel-body">
				<div id="c_without_budget">
		  			<div class="row">
		  				<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										<table id="no_budget_table" class="table table-striped table-hover ">
										  	<thead>
										    	<tr>
										      		<th>Type</th>
										      		<th>Budget Holder</th>
										      		<th>Budget Name</th>
										      		<th>Start Date</th>
										      		<th>End Date</th>
										      		<th>Remarks</th>
										    	</tr>
										  	</thead>
										  	@if(count($nobudgets)> 0)
										  	<tbody>
										  		@foreach ($nobudgets as $nobudget)
										  		<tr id="{{ $nobudget->id }}">
										  			<td>{{ $nobudget->budgettype->budget_type }}</td>
											  		<td>{{ $nobudget->budget_no }}</td>
											  		<td>{{ $nobudget->budget_name }}</td>
											  		<td>{{ date_format(date_create($nobudget->start_date),'m/d/Y') }}</td>
											  		<td>{{ date_format(date_create($nobudget->end_date),'m/d/Y') }}</td>
											  		<td>{{ $nobudget->remarks }}</td>
										  		</tr>
										  		@endforeach
										  	</tbody>
										  	@endif
										</table> 
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		

		
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<button class="btn btn-default btn-style" type="submit">Back</button>
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
  	</div>

  	<!-- timings details -->
  	<div class="tab-pane fade" id="timings">
  		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<table class="table table-striped table-hover ">
					  <thead>
					    <tr>
					      <th>#</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr>
					      <td>1</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr>
					      <td>2</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="info">
					      <td>3</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="success">
					      <td>4</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="danger">
					      <td>5</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="warning">
					      <td>6</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="active">
					      <td>7</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					  </tbody>
					</table> 
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
  		</div>
  	</div>

  	<!-- material details -->
  	<div class="tab-pane fade" id="materials">
  		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<table class="table table-striped table-hover ">
					  <thead>
					    <tr>
					      <th>#</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr>
					      <td>1</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr>
					      <td>2</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="info">
					      <td>3</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="success">
					      <td>4</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="danger">
					      <td>5</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="warning">
					      <td>6</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="active">
					      <td>7</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					  </tbody>
					</table> 
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
  		</div>
  	</div>

  	<!-- attachment details -->
  	<div class="tab-pane fade" id="attachment">
  		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<table class="table table-striped table-hover ">
					  <thead>
					    <tr>
					      <th>#</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					      <th>Column heading</th>
					    </tr>
					  </thead>
					  <tbody>
					    <tr>
					      <td>1</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr>
					      <td>2</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="info">
					      <td>3</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="success">
					      <td>4</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="danger">
					      <td>5</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="warning">
					      <td>6</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					    <tr class="active">
					      <td>7</td>
					      <td>Column content</td>
					      <td>Column content</td>
					      <td>Column content</td>
					    </tr>
					  </tbody>
					</table> 
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
					</div>
				</div>
			</div>
  		</div>
  	</div>
</div>


@stop

@section('page-script')
<!-- $('textarea#background').ckeditor(); -->

<!-- activity details -->

function duration(value){
	$.ajax({
		type: "GET",
		url: "../activitytype/"+value+"/network/totalduration",
		success: function(msg){
			$('#lead_time').val(msg);

			$('#implementation_date').val(moment().add(msg,'days').format('MM/DD/YYYY'));
			$('#download_date').val(moment().format('MM/DD/YYYY'))

			$('#implementation_date').data("DateTimePicker").setMinDate(moment().add(msg,'days').format('MM/DD/YYYY'));
		},
		error: function(){
			alert("failure");
		}
	});
}

$('select#approver, select#involve').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#activity_type').on("change",function(){
	duration($(this).val());
});

$('#implementation_date').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	minDate: moment()
});

$("#implementation_date").on("dp.change",function (e) {
	// console.log(moment(e.date).subtract($('#lead_time').val(),'days').format('MM/DD/YYYY'));
	$('#download_date').val(moment(e.date).subtract($('#lead_time').val(),'days').format('MM/DD/YYYY'));
});

$('#implementation_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});


$('select#division').on("change",function(){
	suggest_name();
	$.ajax({
			type: "POST",
			data: {q: $(this).val()},
			url: "../api/category",
			success: function(data){
				$('select#category').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#category')); 
				});
			$('select#category').multiselect('rebuild');
		   }
		});
});


$('select#category').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		$.ajax({
			type: "POST",
			data: {categories: GetSelectValues($('select#category :selected'))},
			url: "../api/brand",
			success: function(data){
				$('select#brand').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#brand')); 
				});
			$('select#brand').multiselect('rebuild');
		   }
		});
		suggest_name();
	}
});

$('select#brand').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		suggest_name();
	}
});

$('select#objective').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});


$("form[id='updateActivity']").on("submit",function(e){
    var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');
	$.ajax({
		url: url,
		data: form.serialize(),
		method: method,
		success: function(data){
			alert('Activity updated');
		}
	});
	e.preventDefault();
});


<!-- Customer details -->
// fancy tree
$("#tree3").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/customers"
	},
	  select: function(event, data) {
	  	// Get a list of all selected nodes, and convert to a key array:
        // var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
        //  return node.key;
        // });
        // $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		// $("#echoSelectionRootKeys3").text(selRootKeys.join("."));
		// $("#echoSelectionRootKeys3").text(selRootKeys.join(", "));

		var keys = selRootKeys.join(".").split(".");
		console.log(keys);
		if($.inArray('E1397', keys) != -1){
			$('select#channel').multiselect('enable');
		}else{
			$('select#channel').multiselect('deselectAll', false);
			$('select#channel').multiselect('updateButtonText')
			$('select#channel').multiselect('disable');
		}

		$("#customers").val(selRootKeys.join(", "));
	  },
});


<!-- Budget details -->


$(".btn-style").click(function (e) {
	e.preventDefault();
    var target = $(".nav-tabs li.active");
    var sibbling;
    if ($(this).text() === "Next") {
        sibbling = target.next();
    } else {
        sibbling = target.prev();
    }

    if (sibbling.is("li")) {
        sibbling.children("a").tab("show");
    }
});

@stop



