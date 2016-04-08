<div class="tab-pane fade active" id="sob">
		<br>
		
		<div class="panel panel-primary">
			
			<div class="panel-heading">SOB Details</div>
			@if(count($sobs)>0)
			<div class="panel-body">
				@if(($scheme->activity->activitytype->with_sob) && ($scheme->compute < 3))
				
				<?php $first_index = 1; ?>
				@foreach($sobgroups as $group)

					@foreach($sob_header[$group->id] as $key => $header)
						{{ Form::hidden('_wek['.$group->id.']['.$key.']', $header, ['id' => '_wek_'.$group->id.'_'.$key, 'class' => 'week-sum']) }}
					@endforeach
				@endforeach
	

				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('sdivision', 'Division', array('class' => 'control-label')) }}
									{{ Form::text('sdivision',$scheme->sdivision,array('class' => 'form-control','readonly' => '')) }}
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<div class="row">
								<div  class="col-lg-12">
								{{ Form::label('scategory', 'Category', array('class' => 'control-label')) }}
								{{ Form::text('scategory',$scheme->scategory,array('class' => 'form-control','readonly' => '')) }}

								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
								{{ Form::label('sbrand', 'Brand', array('class' => 'control-label')) }}
								{{ Form::text('sbrand',$scheme->brand_desc,array('class' => 'form-control','readonly' => '')) }}
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
									{{ Form::label('start_date', 'Start Date', array('class' => 'control-label')) }}
									{{ Form::text('start_date1',date_format(date_create($scheme->sob_start_date),'m/d/Y'),array( 'class' => 'form-control', 'placeholder' => 'Start Date','readonly' => '')) }}
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
									{{ Form::label('weeks', 'No. of Weeks', array('class' => 'control-label')) }}
									{{ Form::text('weeks',$scheme->weeks,array('id' => 'weeks', 'class' => 'form-control', 'placeholder' => 'No. of Weeks', 'readonly' => '')) }}
								</div>
							</div>
						</div>
					</div>
				</div>

				<br>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									@if(count($sobs)>0)
									<a class="btn btn-success" target="_blank" href="{{ URL::action('SchemeController@exportsob', $scheme->id ) }}">Export To Excel</a>
									@else
									<button class="btn btn-success disabled">Export To Excel</button>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				@if(count($sobs)>0)
				<br>
				@include('shared.sob_table_read_only')	
				@endif

				@endif
			</div>
			@else
			<div class="panel-body">
				<h2>No SOB Allocation created.</h2>
				</div>
			@endif
		</div>
		
	</div>