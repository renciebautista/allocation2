@extends('layouts.layout')

@section('content')


<div class="row">
  	<div class="col-lg-10 col-md-offset-1">
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
					<td>Start Date</td> 
					<td colspan="3">{{ date_format(date_create($joborder->start_date),'m/d/Y') }}</td> 
				</tr>
				<tr> 
					<td>End Date</td> 
					<td colspan="3">{{ date_format(date_create($joborder->end_date),'m/d/Y') }}</td> 
				</tr>
				<tr> 
					<td>Assigned To</td> 
					<td colspan="3"></td> 
				</tr>
			</tbody>
		</table>
		<hr>
		<div>
			@foreach($comments as $comment)
			<div class="comment_list">
				<div clas="right">
					<h3>{{ $comment->createdBy->getFullname()}} <small>{{ Carbon::createFromTimeStamp(strtotime($comment->created_at))->diffForHumans()}}</small></h3>
					<p><?php echo nl2br($comment->comment) ?></p>
				</div>
			</div>
			@endforeach
			<div class="form-container">
				<div class="form-group">
					<input type="file" name="files[]" id="filer_input" multiple="multiple">
				</div>
				<div class="form-group">
					<textarea name="comment" id="comment"></textarea>
				</div>
				<div class="form-group">
					{{ HTML::linkAction('JoborderController@unassigned', 'Back', array(), array('class' => 'btn btn-default')) }}
					<button class="btn btn-primary btn-style" type="submit">Submit</button>
				</div>
			</div>
			
		</div>
  		
		
  	</div>
</div>


@stop

@section('page-script')
	$('#filer_input').filer({
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
