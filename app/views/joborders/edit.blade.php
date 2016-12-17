@extends('layouts.layout')

@section('content')


<div class="row">
  	<div class="col-lg-10 col-md-offset-1">
  		@include('partials.notification')
  		<div class="panel panel-default">
			<div class="panel-heading">Job Order Details</div>
			<div class="panel-body">
				<table id="joborder" class="table table-bordered"> 
					<tbody>
						<tr> 
							<td>Job Order #</td> 
							<td>{{ $joborder->id }}</td> 
							<td>Date Created</td> 
							<td>{{ date_format(date_create($joborder->created_at),'m/d/Y H:m:s') }}</td> 
						</tr>
						<tr> 
							<td>Activity</td> 
							<td colspan="3">{{ $joborder->activity->id}} - {{ $joborder->activity->circular_name }}</td> 
						</tr>
						<tr> 
							<td>Status</td> 
							<td colspan="3">{{ $joborder->status->joborder_status }}</td> 
						</tr>
						<tr> 
							<td>Task</td> 
							<td colspan="3">{{ $joborder->task }}</td> 
						</tr> 
						<tr> 
							<td>Sub Task</td> 
							<td colspan="3">{{ $joborder->sub_task }}</td> 
						</tr> 
						<tr> 
							<td>Target Date</td> 
							<td colspan="3">{{ date_format(date_create($joborder->target_date),'m/d/Y') }}</td> 
						</tr>
						<tr> 
							<td>End Date</td> 
							<td colspan="3">{{ date_format(date_create($joborder->end_date),'m/d/Y') }}</td> 
						</tr>
						<tr> 
							<td>Created By</td> 
							<td colspan="3">
								{{ $joborder->createdBy->getFullname() }}
							</td> 
						</tr>
						<tr> 
							<td>Assigned To</td> 
							<td colspan="3">
								@if($joborder->assigned_to > 0)
								{{ $joborder->assignedto->getFullname() }}
								@endif
							</td> 
						</tr>
					</tbody>
				</table>
			</div>		
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Final Artworks</div>
			<div class="panel-body">
				<div class="attachment">
					<ul>
					@foreach($artworks as $file)
					<li>
						<a target="_blank"href="{{route('joborders.artworkdownload', $file->random_name)}}">
							{{ HTML::image('jorderartwork/'.$file->random_name, $file->file_name) }}
						</a>
						{{ Form::open(array('method' => 'DELETE', 'action' => array('JoborderController@artworkdelete', $file->random_name))) }}  
						{{ Form::submit('Remove', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
						{{ Form::close() }}
					</li>
					
					@endforeach
					</ul>
				</div>
					{{ Form::open(array('action' => array('JoborderController@uploadphoto', $joborder->id) ,'class' => 'bs-component' ,'id' => 'myform', 'files'=>true)) }}
					<div class="form-container">
						<div class="form-group">
							<input type="file" name="files[]" id="filer_input1" multiple="multiple">
						</div>
						<div class="form-group">
							<button class="btn btn-primary btn-style" type="submit">Submit</button>
						</div>
					</div>	
					{{ Form::close() }}
					<hr>
				</div>	
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Comments</div>
			<div class="panel-body">
				<div>
					@foreach($comments as $comment)
					<div class="comment_list">
						<div clas="right">
							<h3>{{ $comment->createdBy->getFullname()}} <small>{{ Carbon::createFromTimeStamp(strtotime($comment->created_at))->diffForHumans()}}</small></h3>
							<div class="comments">
								<p><?php echo nl2br($comment->comment) ?></p>
								<div class="attachment">
									<ul>
									@foreach($comment->files as $file)
									<li>
										<a target="_blank"href="{{route('joborders.download', $file->random_name)}}">
											{{ HTML::image('commentimage/'.$file->random_name, $file->file_name) }}
											<div class="file-name">{{ $file->file_name }}</div>
										</a>
										
									</li>
									@endforeach
									</ul>
								</div>
								
							</div>
							

						</div>
					</div>
					@endforeach
				</div>
			</div>	
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Insert Comment</div>
			<div class="panel-body">
				<div>
					{{ Form::open(array('route' => array('joborders.update', $joborder->id), 'method' => 'PUT',  'class' => 'bs-component', 'files'=>true)) }}			
					<div class="form-container">
						<div class="form-group">
							<input type="file" name="files" id="filer_input2" multiple="multiple">
						</div>

						@if($joborder->joborder_status_id == 1)
						<div class="form-group">
							<div class="row">
								<div class="col-lg-6">
									{{ Form::label('assigned_to', 'Assign To', array('class' => 'control-label')) }}
									{{ Form::select('assigned_to', array('0' => 'Please Select') + $dept_users, [], array('class' => 'form-control')) }}
								</div>
							</div>
							
						</div>
						<br>
						@endif
						<div class="form-group">
							<textarea name="comment" id="comment"></textarea>
						</div>
						@if(!$staff)
						@if(($joborder->joborder_status_id > 1) && (Auth::user()->ability([], ['manage_department_jo'])))
						<div class="form-group">
							<div class="row">
								<div class="col-lg-6">
									{{ Form::label('status', 'Status', array('class' => 'control-label')) }}
									{{ Form::select('jo_status', array('0' => 'Please Select') + $joudpatestatus, [], array('class' => 'form-control')) }}
								</div>
							</div>
							
						</div>
						@endif
						@endif
						<br>
						
						<div class="form-group">
							@if(!$staff)
							{{ HTML::linkAction('JoborderController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
							@else
							{{ HTML::linkAction('MyJobOrderController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
							@endif
							<button class="btn btn-primary btn-style" type="submit">Submit</button>
						</div>
					</div>
					{{ Form::close()}}	
				</div>
			</div>
		</div>
  		
		
  	</div>
</div>


@stop

@section('page-script')
	$('#filer_input1').filer({
	    changeInput: true,
	    showThumbs: true,
	    addMore: true
	});

	$('#filer_input2').filer({
	    changeInput: true,
	    showThumbs: true,
	    addMore: true
	});

	tinymce.init({
	  selector: 'textarea',
	  height: 150,
	  menubar: false,
	  statusbar : false,
	  toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
	});
@stop
