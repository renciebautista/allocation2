<div class="tab-pane fade active" id="sob">
		<br>
		<div class="panel panel-primary">
			<div class="panel-heading">SOB Details</div>
			<div class="panel-body">
				@if(($scheme->activity->activitytype->with_sob) && ($scheme->compute < 3))

				{{ Form::open(array('action' => array('SchemeController@updatesob', $scheme->id), 'files'=>true, 'id' => 'updatesob', 'class' => 'bs-component')) }}
				@foreach($sob_header as $key => $header)
					{{ Form::hidden('_wek['.$key.']', $header, ['id' => '_wek'.$key, 'class' => 'week-sum']) }}
				@endforeach

				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
									{{ Form::select('brand', $brands, $scheme->brand_desc, array('data-placeholder' => 'Select Brand','id' => 'brand', 'class' => 'form-control')) }}
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
									<a class="btn btn-success" target="_blank" href="{{ URL::action('SchemeController@exportsob', $scheme->id ) }}">Export To Excel</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<br>
				<div class="row mytable">
					<div class="col-lg-12">
						<div class="allocation_total table-responsive">
							<table id="sob-allocation" class="table table-condensed table-bordered display compact">
								<thead>
									
									<tr class="sob-percent">
										<th colspan="3" >{{ Form::submit('Update SOB', array('class' => 'btn btn-primary btn-xs disable-button pull-right', 'id'=>'updatesob' , 'name' => 'submit' )) }}</th>
										<?php $total = 0; ?>
										@foreach($sob_header as $key => $header)
										<th class="alloc_per">
											{{ Form::text('wek['.$key.']',$header,array('id' => 'wek_'.$key, 'class' => 'numweek')) }}
										</th>
										<?php $total += $header; ?>
										@endforeach
										<th><span id="sum">{{ number_format($total,2) }}% </span></th>
									</tr>
									<tr class="sob-header">
										<th>GROUP</th>
										<th>AREA</th>
										<th>SHIP TO</th>
										@foreach($sob_header as $key => $header)
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
										@foreach($sob_header as $key => $header)
										<?php $col = "wk_".$key; ?>
										<td class="sob_alloc">{{ $sob->$col }}</td>
										<?php $sum += $sob->$col; ?>
										@endforeach
										<td class="sob_alloc_header wek_sum"><span id="sum_alloc">{{ $sum }}</span></td>
										
									</tr>
									@endforeach
									
								</tbody>
							</table> 
						</div>
					</div>
				</div>

				{{ Form::close() }}

				@endif
			</div>
		</div>
	</div>