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
						{{ Form::text('milestone','',array('class' => 'form-control', 'placeholder' => 'Milestone')) }}
					</div>
					<div class="form-group">
						{{ Form::label('task', 'Task', array('class' => 'control-label')) }}
						{{ Form::text('task','',array('class' => 'form-control', 'placeholder' => 'Task')) }}
					</div>
					<div class="form-group">
						{{ Form::label('responsible', 'Team Responsible', array('class' => 'control-label')) }}
						{{ Form::text('responsible','',array('class' => 'form-control', 'placeholder' => 'Team Responsible')) }}
					</div>
					<div class="form-group">
						{{ Form::label('depend_on', 'Depends On', array('class' => 'control-label')) }}
						<select class="form-control" data-placeholder="SELECT ACTIVITY" id="depend_on" name="depend_on[]" multiple="multiple" ></select>
					</div>
					<div class="form-group">
						{{ Form::label('duration', 'Duration (days)', array('class' => 'control-label')) }}
						{{ Form::text('duration','',array('class' => 'form-control', 'placeholder' => 'Duration (days)')) }}
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

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table id="activity_table" class="table table-striped table-hover ">
			  	<thead>
				    <tr>
				    	<th data-field="task_id">Task ID</th>
				        <th data-field="milestone">Milestone</th>
				        <th data-field="task">Task</th>
				        <th data-field="responsible">Team Responsible</th>
				        <th data-field="duration">Duration (days)</th>
				        <th data-field="depend_on">Depends On</th>
				        <th data-field="action" data-formatter="actionFormatter" data-events="actionEvents">Action</th>
				    </tr>
				</thead>
			</table> 
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<p id="totalduration"></p>
	</div>
</div>

@stop

@section('page-script')



function duration(){
	$.ajax({
		type: "GET",
		url: "network/totalduration",
		success: function(msg){
			$("#totalduration").empty();
			$("#totalduration").append( " <strong>Total Duration : "+msg+" days</strong>" );
		},
		error: function(){
			alert("failure");
		}
	});
}

duration();

$('#myModal').on('show.bs.modal', function (event) {

	var modal = $(this)
	console.log(modal)
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
});

$('button#submit').click(function(){
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
});

$('#activity_table').bootstrapTable({
    url: 'network/list'
});

$('select#depend_on').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});




@stop