@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Activity</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			{{ HTML::linkRoute('activity.index', 'Back To Activity List', array(), array('class' => 'btn btn-default')) }}
		</div>
	</div>

</div>


<ul class="nav nav-tabs">
	<li class="active"><a aria-expanded="true" href="#activty" data-toggle="tab">Activity Details</a></li>
	<li class="disabled"><a>Budget Details</a></li>
	<li class="disabled"><a>Customer Details</a></li>
	<li class="disabled"><a>Schemes</a></li>
	<li class="disabled"><a>Timings Details</a></li>
	<li class="disabled"><a>Attachment</a></li>
</ul>
<div id="myTabContent" class="tab-content">
  	<div class="tab-pane fade active in" id="activty">
  		<br>
  		{{ Form::open(array('route' => 'activity.store','class' => 'bs-component', 'id' => 'myform')) }}
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
								{{ Form::select('scope', array('0' => 'PLEASE SELECT') + $scope_types, null, array('id' => 'scope','class' => 'form-control')) }}
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
								{{ Form::select('planner', array('0' => 'PLEASE SELECT') + $planners, null, array('id' => 'planner', 'class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								
								{{ Form::label('approver', 'Activity Approver', array('class' => 'control-label')) }}
								{{ Form::select('approver[]', $approvers, null, array('id' => 'approver', 'class' => 'form-control', 'multiple' => 'multiple')) }}
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
								{{ Form::select('activity_type', array('0' => 'PLEASE SELECT') + $activity_types, null, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('lead_time', 'Activity Leadtime (days)', array('class' => 'control-label')) }}
								{{ Form::text('lead_time','',array('class' => 'form-control', 'placeholder' => '0' , 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('download_date', 'Target Download Date ', array('class' => 'control-label')) }}
								{{ Form::text('download_date','',array('id' => 'download_date', 'class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'readonly' => '')) }}
								
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('implementation_date', 'Target Implementation Date', array('class' => 'control-label')) }}
								{{ Form::text('implementation_date','',array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
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
								<select class="form-control" data-placeholder="SELECT CYCLE" id="cycle" name="cycle">
									<option value="0">PLEASE SELECT</option>
								</select>
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
								{{ Form::text('activity_title','',array('id' => 'activity_title', 'class' => 'form-control', 'placeholder' => 'Activity Title' ,'maxlength' => 80)) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
								{{ Form::select('division[]',  $divisions, null, array('id' => 'division', 'class' => 'form-control' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT DIVISION')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div id="multiselect" class="col-lg-12">
								{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
								<select class="form-control" data-placeholder="SELECT CATEGORY" id="category" name="category[]" multiple="multiple" ></select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
								<select class="form-control" data-placeholder="SELECT BRAND" id="brand" name="brand[]" multiple="multiple" ></select>
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
									{{ Form::textarea('background','',array('class' => 'form-control', 'placeholder' => 'Background and Rationale')) }}
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
								{{ Form::label('instruction', 'Special Instruction', array('class' => 'control-label')) }}
									{{ Form::textarea('instruction','',array('class' => 'form-control', 'placeholder' => 'Special Instruction')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
					</div>
				</div>
			</div>
  		</div>
  		{{ Form::close() }}
  	</div>
</div>


@include('javascript.activity.create')

@stop

@section('page-script')

@stop





