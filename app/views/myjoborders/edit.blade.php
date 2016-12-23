@extends('layouts.layout')

@section('content')


<div class="row">
  	<div class="col-lg-10 col-md-offset-1">
  		@include('partials.notification')
  		
  		<div class="form-group">
			{{ HTML::linkAction('MyJobOrderController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>

  		@include('shared.jodetails')


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
						@if($joborder->joborder_status_id < 3)
						{{ Form::open(array('method' => 'DELETE', 'action' => array('JoborderController@artworkdelete', $file->random_name))) }}  
						{{ Form::submit('Remove', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
						{{ Form::close() }}
						@endif

					</li>
					
					@endforeach
					</ul>
				</div>

					@if($joborder->joborder_status_id < 3)
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
					@endif
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

		@if($joborder->joborder_status_id < 3)
		<div class="panel panel-default">
			<div class="panel-heading">Insert Comment</div>
			<div class="panel-body">
				<div>
					{{ Form::open(array('route' => array('joborders.update', $joborder->id), 'method' => 'PUT',  'class' => 'bs-component', 'files'=>true)) }}			
					<div class="form-container">
						<div class="form-group">
							<input type="file" name="files" id="filer_input2" multiple="multiple">
						</div>

						@if($joborder->start_date == '0000-00-00')
						<div class="form-group">
							<div class="row">
								<div class="col-lg-3">
									{{ Form::label('start_date', 'Estimated Start Date', array('class' => 'control-label')) }}
									{{ Form::text('start_date','',array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'id' => 'start_date')) }}
								</div>

								<div class="col-lg-3">
									{{ Form::label('end_date', 'Estimated End Date', array('class' => 'control-label')) }}
									{{ Form::text('end_date','',array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'id' => 'end_date')) }}
								</div>
							</div>
						</div>
						@endif

						<div class="checkbox"> <label> <input name="return" type="checkbox"> Return Job Order </label> </div>

						<div class="form-group">
							<textarea name="comment" id="comment"></textarea>
						</div>

						<br>
						
						<div class="form-group">
							{{ HTML::linkAction('MyJobOrderController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
							<button class="btn btn-primary btn-style" type="submit">Submit</button>
						</div>
					</div>
					{{ Form::close()}}	
				</div>
			</div>
		</div>
  		
		@endif

  	</div>
</div>


@stop

@section('page-script')

	$('#start_date').datetimepicker({
		pickTime: false,
		calendarWeeks: true,
		minDate: moment()
	}).mask("99/99/9999",{placeholder:"mm/dd/yyyy"})
	.on("dp.change", function (e) {
        $('#end_date').data("DateTimePicker").setMinDate(e.date);
    });

	$('#end_date').datetimepicker({
		pickTime: false,
		calendarWeeks: true,
		minDate: moment()
	}).mask("99/99/9999",{placeholder:"mm/dd/yyyy"})
	.on("dp.change", function (e) {
        $('#start_date').data("DateTimePicker").setMaxDate(e.date);
    });

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
