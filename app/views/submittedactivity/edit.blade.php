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
			{{ HTML::linkRoute('submittedactivity.index', 'Back To Activity List', array(), array('class' => 'btn btn-default')) }}

			<!-- Button trigger modal -->
			@if(($approver->status_id == 0) && ($valid))
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySubmit" data-whatever="Approve">
			  	Approve
			</button>
			<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#mySubmit" data-whatever="Deny">
			  	Deny
			</button>
			@endif
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="mySubmit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
	    <div class="modal-content">
	    	{{ Form::open(array('action' => array('SubmittedActivityController@updateactivity', $activity->id), 'class' => 'bs-component','id' => 'updateactivity')) }}
	    	{{ Form::hidden('status', '', array('id' => 'status')) }}
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel"></h4>
	      	</div>
	      	<div class="modal-body">
	          	<div class="form-group">
	            	{{ Form::label('submitremarks', 'Comments:', array('class' => 'control-label')) }}
	            	{{ Form::textarea('submitremarks','',array('class' => 'form-control', 'placeholder' => 'Comments')) }}
	          	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        	<button class="btn btn-primary">Submit</button>	    
	     	</div>
	     	{{ Form::close() }}
	    </div>
  	</div>
</div>


<ul class="nav nav-tabs">
	<li class="active"><a aria-expanded="true" href="#activty">Activity Preview</a></li>
	<li class=""><a aria-expanded="false" href="#comments">Comments</a></li>
</ul>

<div id="myTabContent" class="tab-content">
	<div class="tab-pane fade active in" id="activty">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">Activity Preview</div>
		  	<div class="panel-body" style="height:600px;">
				<iframe src="{{ URL::action('DownloadedActivityController@preview', $activity->id) }}" id="view1" name="view1" scrolling="auto" frameborder="0" height="100%" width="100%"></iframe>
		  	</div>
		</div>
		
	</div>

	<!-- attachment details -->
	<div class="tab-pane fade" id="comments">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">Comments</div>
		  	<div class="panel-body">
				<ul class="comment">
					@foreach($comments as $comment)
	                <li class="left clearfix">
	                    <div class="comment-body clearfix">
	                        <div class="header">
	                            <strong class="primary-font">{{ $comment->createdby->getFullname()}} 
	                            	<p class="{{ $comment->class }}">({{ $comment->comment_status }})</p>
	                            </strong> 
	                            <small class="pull-right text-muted">
	                                <i class="fa fa-clock-o fa-fw"></i> {{ Carbon::parse($comment->created_at)->subMinutes(2)->diffForHumans()}}
	                            </small>
	                        </div>
	                        <p>{{ $comment->comment }}</p>
	                    </div>
	                </li>
	                @endforeach
	            </ul>
		  	</div>
		</div>
	</div>

	
</div>

@stop


@section('page-script')

$('.nav-tabs a').click(function (e) {
	// No e.preventDefault() here
	$(this).tab('show');
});

if(location.hash.length > 0){
	var activeTab = $('[href=' + location.hash + ']');
	activeTab && activeTab.tab('show');
}

$('#mySubmit').on('show.bs.modal', function (event) {
 	var button = $(event.relatedTarget) // Button that triggered the modal
  	var action = button.data('whatever') // Extract info from data-* attributes
  	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
  	// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
  	var modal = $(this)
  	modal.find('.modal-title').text(action+" activity")
  	status = 0;
  	if(action == "Approve"){
  		status = 1;
  	}
  	modal.find('#status').val(status);
})

$("form[id='updateactivity']").on("submit",function(e){
	var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize(),
			method: method,
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					$('#mySubmit').modal('hide');
					location.reload();	
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
	}
	
	e.preventDefault();
});

$("#updateactivity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		status: "required",
		submitremarks: "required"

	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	}
});
@stop