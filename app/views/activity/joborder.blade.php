@extends('layouts.layout')

@section('content')


<div class="row">
  	<div class="col-lg-10 col-md-offset-1">
  		@include('partials.notification')

  		<div class="form-group">
			{{ HTML::linkAction('ActivityController@edit', 'Back', array('id' => $joborder->activity_id), array('class' => 'btn btn-default')) }}
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
						@if($joborder->joborder_status_id < 4)
						{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@joborderartworkdelete', $file->random_name))) }}  
						{{ Form::submit('Remove', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
						{{ Form::close() }}
						@endif
					</li>
					
					@endforeach
					</ul>
				</div>
				@if($joborder->joborder_status_id < 4)
					{{ Form::open(array('action' => array('ActivityController@joborderuploadphoto', $joborder->id) ,'class' => 'bs-component' ,'id' => 'myform', 'files'=>true)) }}
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

		
  				@if($joborder->joborder_status_id < 4)

		<div class="panel panel-default">
			<div class="panel-heading">Insert Comment</div>
			<div class="panel-body">
				<div>
					{{ Form::open(array('route' => array('joborders.update', $joborder->id), 'method' => 'PUT',  'class' => 'bs-component', 'files'=>true)) }}			
					<div class="form-container">
						<div class="form-group">
							<input type="file" name="files" id="filer_input2" multiple="multiple">
						</div>
						<div class="form-group">
							<textarea name="comment" id="comment"></textarea>
						</div>
						
						<div class="form-group">
							{{ HTML::linkAction('ActivityController@edit', 'Back', array($joborder->activity_id,'#jo'), array('class' => 'btn btn-default')) }}
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
