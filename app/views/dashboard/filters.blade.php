
@extends('layouts.layout')

@section('content')

@include('shared.changepassword')
<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Activity Filter Settings</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		{{ Form::open(array('action' => 'DashboardController@savefilters','class' => 'bs-component' , 'id' => 'myform')) }}
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('division', 'Divisions', array('class' => 'control-label')) }}
							{{ Form::select('division[]', $divisions, $sel_divisions, array('id' => 'division','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div id="multiselect" class="col-lg-12">
							{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
							<select class="form-control" data-placeholder="SELECT CATEGORY" id="category" name="category[]" multiple="multiple" ></select>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
							<select class="form-control" data-placeholder="SELECT BRAND" id="brand" name="brand[]" multiple="multiple" ></select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				{{ Form::label('tree3', 'Select Customers', array('class' => 'control-label' )) }}
				<div id="tree3"></div>
				{{ Form::hidden('customers', null, array('id' => 'customers')) }}
			</div>
		</div>	
		<br>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
					{{ HTML::linkAction('DashboardController@index', 'Back', array(), array('id' => 'back', 'class' => 'btn btn-default')) }}
				</div>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>


@stop

@section('page-script')

$("#myform").on("submit", function () {
    $(this).find(":submit").prop("disabled", true);
    $("#page").hide();
	$("#pageloading").show();
});

var div = $("select#division").val();
if(parseInt(div) > 0) {
   updatecategory(div);
}


$('select#division').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		updatecategory();
	}
});


$('select#category').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		updatebrand();
	}
});

$('select#brand').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
	}
});

function updatecategory(){
	$.ajax({
		type: "GET",
		data: {d: GetSelectValues($('select#division :selected'))},
		url: "{{ URL::action('DashboardController@categoryselected') }}",
		success: function(data){
			$('select#category').empty();
			$.each(data.selection, function(i, text) {
				var sel_class = '';
				if($.inArray( i,data.selected ) > -1){
					sel_class = 'selected="selected"';
				}
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#category')); 
			});
		$('select#category').multiselect('rebuild');
		updatebrand();
	   }
	});
}

function updatebrand(){
	$.ajax({
		type: "GET",
		data: {b: GetSelectValues($('select#category :selected'))},
		url: "{{ URL::action('DashboardController@brandselected') }}",
		success: function(data){
			$('select#brand').empty();
			$.each(data.selection, function(i, text) {
				var sel_class = '';
				if($.inArray( i,data.selected ) > -1){
					sel_class = 'selected="selected"';
				}
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#brand'));
			});
		$('select#brand').multiselect('rebuild');
	   }
	});
}



$("#tree3").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/customers"
	},
	select: function(event, data) {
		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		$("#customers").val(selRootKeys.join(", "));
	},
	click: function(event, data) {
        $("#updateCustomer").addClass("dirty");
    },
	click: function(event, data) {
        $("#myform").addClass("dirty");
    },
});




function getCustomer(){
	$.ajax({
		type: "GET",
		url: "{{ URL::action('DashboardController@customerselected') }}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree3").fancytree("getTree").getNodeByKey(node).setSelected(true);
				$("#tree3").fancytree("getTree").visit(function(node){
					///if(node.key == node.text){
						///console.log(node);
						//node.setSelected(true);
					//}        
				});
			});
		}
	});
}

getCustomer();

$('#myform').areYouSure();
$('#back').click(function(e) {
    e.preventDefault();
    checkDirty("myform",function(){
			var url = "{{action('DashboardController@index') }}";    
			$(location).attr('href',url);
		});
});

function checkDirty(target_id,callback) {
  	if ($('#'+target_id).hasClass('dirty')) {

  		bootbox.confirm({
		    buttons: {
		        confirm: {
		            label: 'Yes',
		            className: 'btn btn-primary'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn btn-default pull-right margin-left-5'
		        }
		    },
		    message: 'Do you want to save changes?',
		    callback: function(result) {
		        if(result){
			  		if($( "#"+target_id).valid()){
			  			$( "#"+target_id).submit();
			  			$('form').areYouSure( {'silent':true} );
			  			callback();
			  		}else{
			  			$('html, body').animate({
					         scrollTop: ($('.has-error').offset().top - 300)
					    }, 500);
			  		}
			  		
			  	}else{
			  		$('form').areYouSure( {'silent':true} );
			  		callback();
			  	}
		    }
		});

	}else{
		callback();
	}
};
@stop


