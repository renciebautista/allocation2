<div class="tab-pane fade active" id="sob">
		<br>
		<div class="panel panel-primary">
			<div class="panel-heading">SOB Details</div>
			<div class="panel-body">
				@if(($scheme->activity->activitytype->with_sob) && ($scheme->compute < 3))

				{{ Form::open(array('action' => array('SchemeController@updatesob', $scheme->id), 'id' => 'updatesob', 'class' => 'bs-component')) }}
				@if(count($sobs)>0)
				<?php $first_index = 1; ?>
				<?php $last_grp; ?>
				@foreach($sobgroups as $group)
					@foreach($sob_header[$group->id] as $key => $header)
						{{ Form::hidden('_wek['.$group->id.']['.$key.']', $header, ['id' => '_wek_'.$group->id.'_'.$key, 'class' => 'week-sum']) }}
					@endforeach
				@endforeach
				@endif

				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
									{{ Form::select('division', array('' => 'SELECT DIVISION') + $sobdivisions, $scheme->sdivision_code, array('data-placeholder' => 'SELECT DIVISION','id' => 'division', 'class' => 'form-control')) }}
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<div class="row">
								<div  class="col-lg-12">
								{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
								<select class="form-control" data-placeholder="SELECT CATEGORY" id="category" name="category" >
									<option value="">SELECT CATEGORY</option>
								</select>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
								{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
								<select class="form-control multiselect" data-placeholder="SELECT BRAND" id="brand" name="brand" >
									<option value="">SELECT BRAND</option>
								</select>

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
									{{ Form::text('start_date',date_format(date_create($scheme->sob_start_date),'m/d/Y'),array('id' => 'start_date', 'class' => 'form-control', 'placeholder' => 'Start Date')) }}
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
									{{ Form::text('weeks',$scheme->weeks,array('id' => 'weeks', 'class' => 'form-control', 'placeholder' => 'No. of Weeks')) }}
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
									{{ Form::submit('Plot', array('class' => 'btn btn-primary disable-button', 'id'=>'plotsob' , 'name' => 'submit')) }}
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
				<div class="row mytable">
					<div class="col-lg-12">
						<div class="allocation_total table-responsive">
							@if(count($sobs)>0)
							{{ Form::submit('Update SOB', array('class' => 'btn btn-primary disable-button  pull-right', 'id'=>'updatesob' , 'name' => 'submit' )) }}
							@else
							<button class="btn btn-primary disabled pull-right">Update SOB</button>
							@endif
							<table id="sob-allocation" class="table table-condensed table-bordered display compact">
								
								<thead>
									<?php $first_index = 1; ?>
									@foreach($sobgroups as $group)
									<tr class="sob-percent">
										<th colspan="4" >
											<span class="pull-right">{{ $group->sobgroup }}</span>
										</th>
										<?php $total = 0; ?>
										@foreach($sob_header[$group->id] as $key => $header)
										<th class="alloc_per">
											{{ Form::text('wek['.$group->id.']['.$key.']',$header,array('id' => 'wek_'.$group->id.'_'.$key, 'class' => 'numweek')) }}
										</th>
										<?php $total += $header; ?>
										@endforeach
										<th><span class="sum">{{ number_format($total,2) }}% </span></th>
									</tr>

									@endforeach
									<tr class="sob-header">
										<th>GROUP</th>
										<th>AREA</th>
										<th>SHIP TO</th>
										<th>PERCENTAGE GROUP</th>
										@foreach($sob_header[$sobgroups[0]->id] as $key => $header)
										<th class="alloc_per">WK {{ $key }}</th>
										@endforeach
										<th class="sob_alloc_header">Total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($sobs as $sob)
									<?php $sum = 0; ?>
									<tr>
										<td>{{ $sob->group }}</td>
										<td>{{ $sob->area }}</td>
										<td>{{ $sob->ship_to }}</td>
										<td>{{ $sob->sobgroup }}</td>
										@foreach($sob_header[$sobgroups[0]->id] as $key => $header)
										<?php $col = "wk_".$key; ?>
										<td class="sob_alloc">{{ $sob->$col }}</td>
										<?php $sum += $sob->$col; ?>
										@endforeach
										<td class="sob_alloc_header wek_sum"><span clss="sum_alloc">{{ $sum }}</span></td>
									</tr>
									@endforeach
									
								</tbody>
								
							</table> 
						</div>
					</div>
				</div>
				@endif

				{{ Form::close() }}

				@endif
			</div>
		</div>
	</div>