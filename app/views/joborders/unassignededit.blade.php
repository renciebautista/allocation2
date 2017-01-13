@extends('layouts.layout')

@section('content')


<div class="row">
  	<div class="col-lg-10 col-md-offset-1">
  		@include('partials.notification')

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
			{{ Form::open(array('route' => array('joborders.unassignedstore', $joborder->id), 'class' => 'bs-component', 'files'=>true)) }}			
			<div class="form-container">
				<div class="form-group">
					<input type="file" name="files" id="filer_input" multiple="multiple">
				</div>
				<div class="form-group">
					<textarea name="comment" id="comment"></textarea>
				</div>
				<div class="form-group">
					{{ HTML::linkAction('JoborderController@unassigned', 'Back', array(), array('class' => 'btn btn-default')) }}
					<button class="btn btn-primary btn-style" type="submit">Submit</button>
				</div>
			</div>
			{{ Form::close()}}
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
