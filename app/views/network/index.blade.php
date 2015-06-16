@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>{{ $activitytype->activity_type }} Network</h1>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-lg-12">
		{{ HTML::linkRoute('activitytype.index', 'Back To Activity Type List', array(), array('class' => 'btn btn-default')) }}
		<!-- Button trigger modal -->
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
			<i class="fa fa-plus"></i> Milestone
		</button>
	</div>
</div>



<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Modal title</h4>
			</div>
			<form id="activity">
				<div class="modal-body">
					<div class="form-group">
						{{ Form::label('milestone', 'Milestone', array('class' => 'control-label')) }}
						{{ Form::text('milestone','',array('id' => 'milestone', 'class' => 'form-control', 'placeholder' => 'Milestone')) }}
					</div>
					<div class="form-group">
						{{ Form::label('task', 'Task', array('class' => 'control-label')) }}
						{{ Form::text('task','',array('id' => 'task', 'class' => 'form-control', 'placeholder' => 'Task')) }}
					</div>
					<div class="form-group">
						{{ Form::label('responsible', 'Team Responsible', array('class' => 'control-label')) }}
						{{ Form::text('responsible','', array('id' => 'responsible','class' => 'form-control', 'placeholder' => 'Team Responsible')) }}
					</div>
					<div class="form-group">
						{{ Form::label('depend_on', 'Depends On', array('class' => 'control-label')) }}
						<select class="form-control" data-placeholder="SELECT ACTIVITY" id="depend_on" name="depend_on[]" multiple="multiple" ></select>
					</div>
					<div class="form-group">
						{{ Form::label('duration', 'Duration (days)', array('class' => 'control-label')) }}
						{{ Form::text('duration','',array('id' => 'duration','class' => 'form-control', 'placeholder' => 'Duration (days)')) }}
					</div>
					<div class="form-group">
						<div class="checkbox">
					        <label>
					        	{{ Form::checkbox('show',null,array('id' => 'show')) }} Show in Activity Preview
					        </label>
					    </div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button id="submit" type="button" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<div class="table-responsive">
			<table id="activity_table" class="table table-striped table-condensed table-hover table-bordered">
			</table> 
		</div>
	</div>

	<p id="totalduration"></p>
</div>


@stop

@section('page-script')


function duration(){
	$.ajax({
		type: "GET",
		url: "network/totalduration",
		success: function(msg){
			$("#totalduration").empty();
			$("#totalduration").append( " <strong>Total Duration : "+msg.days+" days</strong>" );
		},
		error: function(){
			alert("failure");
		}
	});
}

duration();

$('#myModal').on('show.bs.modal', function (event) {

	var modal = $(this)
	modal.find('.modal-title').text('New Network')

	$.ajax({
		type: "GET",
		url: "network/dependon",
		success: function(data){
			$('select#depend_on').empty();
			$.each(data, function(index, o) {
				$('<option />', {value: o.id, text: o.task_id}).appendTo($('select#depend_on')); 
			});
		$('select#depend_on').multiselect('rebuild');
	   }
	});

	$("#milestone").val('');
    $("#task").val('');
    $("#responsible").val('');
    $("#duration").val('');
    $("#error").empty();
    $('.form-group').removeClass('has-error');
});

$('button#submit').click(function(){
	if($("#activity").valid()){
		$.ajax({
			type: "POST",
			url: "network/create",
			data: $('form#activity').serialize(),
			success: function(msg){
				$('#activity_table').bootstrapTable("refresh");
			    $('#myModal').modal('hide');
			    duration();
			},
			error: function(){
				alert("failure");
			}
		});
	}
	
});






$('#activity_table').bootstrapTable({
    method: 'get',
    url: 'network/list',
    columns: [{
       	field: 'task_id',
        title: 'Task ID',
        align: 'center',
         width : 60,
    }, {
        field: 'milestone',
        title: 'Milestone',
        align: 'center'
    }, {
        field: 'task',
        title: 'Task',
        align: 'center'
    }, {
        field: 'responsible',
        title: 'Team Responsible',
        align: 'center'
    }, {
        field: 'duration',
        title: 'Duration (days)',
        align: 'center'
    },{
        field: 'depend_on',
       	title: 'Depends On',
        align: 'center'
    },{
        field: 'show',
        title: 'Show',
        align: 'center',
        formatter: showFormatter,
    },{
        field: 'id',
        title: 'Action',
        align: 'center',
        formatter: actionFormatter,
        width : 100,
    }]
});

function showFormatter(value) {
    if(value){
    	return 'TRUE';
	}else{
		return 'FALSE';
	}
}
function actionFormatter(value) {
    return '<a class="btn btn-info btn-xs" href="https://github.com/wenzhixin/' + value + '">Edit</a>   <a class="btn btn-danger btn-xs" href="https://github.com/wenzhixin/' + value + '">Delete</a>';
}


$('select#depend_on').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});


$("#activity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		milestone: {
			required: true,
			maxlength: 80
			},
		task: {
			required: true,
			maxlength: 80
			},
		responsible: {
			required: true,
			maxlength: 80
			},
		duration: {
			required: true,
			maxlength: 80
			}
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