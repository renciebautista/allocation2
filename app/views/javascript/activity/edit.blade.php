@section('scripts')

function sumOfColumns(table, columnIndex) {
    var tot = 0;
    table.find("tr").children("td:nth-child(" + columnIndex + ")")
    .each(function() {
        $this = $(this);
        if (!$this.hasClass("sum") && $this.html() != "") {
            tot += parseInt($this.html());
        }
    });
    return tot;
}

function getCustomer(){
	$.ajax({
		type: "GET",
		url: "../../api/customerselected?id={{$activity->id}}",
		success: function(data){
			$.each(data, function(i, node) {
				 $("#tree3").fancytree("getTree").getNodeByKey(node).setSelected(true);
				// console.log(node);
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

if(location.hash.length > 0){
	var activeTab = $('[href=' + location.hash + ']');
	activeTab && activeTab.tab('show');
}

// Change hash for page-reload
$('.nav-tabs a').on('shown', function (e) {
    window.location.hash = e.target.hash;
})

$('.nav-tabs a').click(function (e) {
	pre = "#activity";
	if(window.location.hash.length > 0){
		pre = window.location.hash;
	}
	var target = $(this);
	target_id = $(pre).find('form').attr('id');	
	checkDirty(target_id,function(){
			$(target).tab('show');
		});
	
	
});

$(".btn-style").click(function (e) {
	e.preventDefault();
	target_id = $(this.closest('form')).attr('id');
	var target = $(".nav-tabs li.active");
	var sibbling;
	if ($(this).text() === "Next") {
		sibbling = target.next();
	} else {
		sibbling = target.prev();
	}

	if (sibbling.is("li")) {
		checkDirty(target_id,function(){
			$('#'+sibbling.children("a").attr("id")).trigger('click');
			str = sibbling.children("a").attr("href");
			location.hash = str.replace("#","");
		});
	}
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


$('#updateActivity,#updateCustomer,#updateBilling').areYouSure();


$("a[href='#customer']").on('shown.bs.tab', function(e) {
    getCustomer();
});

$("a[href='#schemes']").on('shown.bs.tab', function(e) {
    $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
});





<!-- activity details -->
function holidays(){
	var arr 
	$.ajax({
		type: "GET",
		dataType: "json",
		async: false,
		url: "../../holidays/getlist",
		success: function(msg){
			arr = $.map(msg, function(el) { return el; });
		},
		error: function(){
			alert("failure");
		}
	});
	return arr;
}
function duration(value){
	$.ajax({
		type: "GET",
		url: "../../activitytype/"+value+"/network/totalduration",
		success: function(msg){
			$('#lead_time').val(msg.days);

			//$('#implementation_date').val(moment().add(msg,'days').format('MM/DD/YYYY'));
			$('#implementation_date').val(msg.end_date);

			//$('#download_date').val(moment().format('MM/DD/YYYY'))
			$('#download_date').val(msg.start_date)

			$('#implementation_date').data("DateTimePicker").setMinDate(moment(msg.min_date).format('MM/DD/YYYY'));
			getCycle(msg.end_date,{{$activity->id}});
		},
		error: function(){
			alert("failure");
		}
	});
}


$('select#approver').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#activity_type').on("change",function(){
	duration($(this).val());
});

$('#implementation_date').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	minDate: moment(),
	daysOfWeekDisabled: [0, 6],
	disabledDates: holidays()
});

$("#implementation_date").on("dp.change",function (e) {
	$.ajax({
		type: "GET",
		url: "../../activitytype/"+$('#activity_type').val()+"/network/totalduration?sd="+moment($('#implementation_date').val()).format('DD-MM-YYYY'),
		success: function(msg){
			$('#lead_time').val(msg.days);
			$('#implementation_date').val(msg.end_date);
			$('#download_date').val(msg.start_date)
			$('#implementation_date').data("DateTimePicker").setMinDate(moment(msg.min_date).format('MM/DD/YYYY'));
			getCycle(msg.end_date,{{$activity->id}});
		},
		error: function(){
			alert("failure");
		}
	});
});
getCycle("{{ date_format(date_create($activity->eimplementation_date),'m/d/Y') }}",{{$activity->id}});

function getCycle(date,id){
	$.ajax({
		type: "GET",
		data: {date: date,id:id },
		url: "{{ URL::action('CycleController@availableCycle') }}",
		success: function(data){
			$('select#cycle').empty();
			$('<option value="0">PLEASE SELECT</option>').appendTo($('select#cycle')); 
			$.each(data.cycles, function(i, text) {
				var sel_class = '';
				if( i == data.sel){
					sel_class = 'selected="selected"';
				}
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#cycle')); 
			});
	   }
	});
}

$('#implementation_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});

function updatecategory(id){
	$.ajax({
		type: "POST",
		data: {q: id, id: {{ $activity->id }}},
		url: "../../api/category/getselected",
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
			type: "POST",
			data: {categories: GetSelectValues($('select#category :selected')),id: {{ $activity->id }}},
			url: "../../api/brand/getselected",
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


var div = $("select#division").val();
if(parseInt(div) > 0) {
   updatecategory(div);
}


$('select#division').on("change",function(){
	updatecategory($(this).val());
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


$('select#objective').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});


$("form[id='updateActivity']").on("submit",function(e){
	var form = $(this);
	var url = form.prop('action');
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize(),
			method: 'POST',
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					bootbox.alert("Activity details was successfully updated."); 
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
	}
	
	e.preventDefault();
});

$("#updateActivity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		activity_title: "required",
		scope: "is_natural_no_zero",
		activity_type: "is_natural_no_zero",
		cycle: "is_natural_no_zero",
		implementation_date: {
			required: true,
			greaterdate : true
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

$.validator.addMethod("greaterdate", function(value, element) {
	return this.optional(element) || (moment(value).isAfter(moment().format('MM/DD/YYYY')) || moment(value).isSame(moment().format('MM/DD/YYYY')));
}, "Please select from the list.");

$('#materials').ajax_table({
	add_url: "{{ URL::action('ActivityController@addmaterial', $activity->id ) }}",
	delete_url: "{{ URL::action('ActivityController@deletematerial') }}",
	update_url: "{{ URL::action('ActivityController@updatematerial') }}",
	columns: [
		{ type: "select", id: "source", ajax_url: "{{ URL::action('api\MaterialController@getsource') }}",validation: { required :true} },
		{ type: "text", id: "material", placeholder: "Remarks",validation: { required :true} }
	],onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}
});



<!-- Customer details -->

$('select#channel').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#channel').multiselect('disable');
 
// fancy tree
$("#tree3").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/customers?id={{$activity->id}}"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		// var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
		//  return node.key;
		// });
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		// $("#echoSelectionRootKeys3").text(selRootKeys.join("."));
		// $("#echoSelectionRootKeys3").text(selRootKeys.join(", "));

		var keys = selRootKeys.join(".").split(".");
		// console.log(keys);
		if($.inArray('E1397', keys) != -1){
			$('select#channel').multiselect('enable');
			updatechannel();
		}else{
			$('select#channel').multiselect('deselectAll', false);
			$('select#channel').multiselect('updateButtonText')
			$('select#channel').multiselect('disable');
		}
		$("#customers").val(selRootKeys.join(", "));
	},
	click: function(event, data) {
        $("#updateCustomer").addClass("dirty");
    },
});

function updatechannel(){
	$.ajax({
		type: "GET",
		url: "{{ URL::action('ActivityController@channels', $activity->id ) }}",
		success: function(data){
			$('select#channel').empty();
			$.each(data.selection, function(i, text) {
				var sel_class = '';
				if(data.selected.length > 0){
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
				}else{
					sel_class = 'selected="selected"';
				}
				
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#channel')); 
			});
		$('select#channel').multiselect('rebuild');
	   }
	});
}


$("form[id='updateCustomer']").on("submit",function(e){
	var form = $(this);
	var url = form.prop('action');
	$.ajax({
		url: url,
		data: form.serialize(),
		method: 'POST',
		dataType: "json",
		success: function(data){
			if(data.success == "1"){
				// bootbox.alert("Activity customers was successfully updated."); 
				location.reload();
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});

$("#force_alloc").on('click',"button",function(e) {
	e.preventDefault();
	var id = $(this).closest("tr").attr('data-link');
	var percent = $(this).closest("tr").find('td:eq(2)').text();
	$('#forcealloc td[field="area_name"]').text($(this).closest("tr").find('td:eq(1)').text());

	$('#f_id').val(id); 
	$("#f_percent").val($(this).closest("tr").find('td:eq(2)').text());
	$('#myForceAlloc').modal('show');

	$('#f_percent').rules('remove', 'max');
    $('#f_percent').rules('add', { 
    	required: true, 
    	min:0, 
    	max: function() { return 100 - sumOfColumns($('#force_alloc'), 3) + parseInt(percent) },
    	messages: {
			required: "Required force allocation percentage.",
		    max: "Total force allocation percentage is above 100"
		}
    } );

});

$("#myForceAlloc").on("hidden.bs.modal", function(){
    $('.modal-body').removeClass("has-error");
    $('.modal-body span').remove();
});


$("form[id='updateforcealloc']").on("submit",function(e){
	var form = $(this);
	var url = form.prop('action');
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize(),
			method: 'POST',
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					$('#force_alloc tr[data-link="'+data.id+'"]').find('td:eq(2)').text(data.f_percent);
					bootbox.alert("Force Allocation was successfully updated."); 
					$('#myForceAlloc').modal('hide');
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
	}
	

	e.preventDefault();
});


$("#updateforcealloc").validate({
	errorElement: "span", 
	errorClass : "has-error",
	errorPlacement: function(error, element) {            
		error.insertAfter("#forcealloc");
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	}
});

$('#f_percent').inputNumber();
<!-- schemes -->



<!-- Budget details -->

$('#billing_deadline').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
$('#billing_deadline').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	minDate: moment()
});

$("form[id='updateBilling']").on("submit",function(e){
	var form = $(this);
	var url = form.prop('action');
	$.ajax({
		url: url,
		data: form.serialize(),
		method: 'POST',
		dataType: "json",
		success: function(data){
			if(data.success == "1"){
				bootbox.alert("Budget details was successfully updated."); 
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});

$('#budget_table').ajax_table({
	add_url: "{{ URL::action('ActivityController@addbudget', $activity->id ) }}",
	delete_url: "{{ URL::action('ActivityController@deletebudget') }}",
	update_url: "{{ URL::action('ActivityController@updatebudget') }}",
	columns: [
		{ type: "select", id: "io_ttstype" , ajax_url: "{{ URL::action('api\BudgetTypeController@gettype') }}", validation: { required :true} },
		{ type: "text", id: "io_no", placeholder: "IO Number", validation: { required :true} },
		{ type: "text", id: "io_amount", placeholder: "Amount", validation: { required :true}},
		{ type: "text", id: "io_startdate", placeholder: "mm/dd/yyyy", validation: { required :true} },
		{ type: "text", id: "io_enddate", placeholder: "mm/dd/yyyy",validation: { required :true} },
		{ type: "text", id: "io_remarks", placeholder: "Remarks"},
	],
	onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	},onInitRow: function() {
		$('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");
	},onEditRow : function(){
		$('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");
	},onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}
});




$('#no_budget_table').ajax_table({
	add_url: "{{ URL::action('ActivityController@addnobudget', $activity->id ) }}",
	delete_url: "{{ URL::action('ActivityController@deletenobudget') }}",
	update_url: "{{ URL::action('ActivityController@updatenobudget') }}",
	columns: [
		{ type: "select", id: "budget_ttstype" , ajax_url: "{{ URL::action('api\BudgetTypeController@gettype') }}", validation: { required :true}},
		{ type: "text", id: "budget_no", placeholder: "Budget Number", validation: { required :true} },
		{ type: "text", id: "budget_name", placeholder: "Budget Name",validation: { required :true} },
		{ type: "text", id: "budget_amount", placeholder: "Amount",validation: { required :true} },
		{ type: "text", id: "budget_startdate", placeholder: "mm/dd/yyyy",validation: { required :true} },
		{ type: "text", id: "budget_enddate", placeholder: "mm/dd/yyyy",validation: { required :true} },
		{ type: "text", id: "budget_remarks", placeholder: "Remarks" },
	],
	onInitRow: function() {
		$('#budget_startdate, #budget_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#budget_startdate, #budget_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#budget_amount').inputNumber();
	},onEditRow : function(){
		$('#budget_startdate, #budget_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#budget_startdate, #budget_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#budget_amount').inputNumber();
	},onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}

});


<!-- activity timings -->
$('#activity_timings').bootstrapTable({
    url: '{{ URL::action('ActivityController@timings', $activity->id ) }}'
});


<!-- update activty -->

$("form[id='submitactivity']").on("submit",function(e){
	var form = $(this);
	var url = form.prop('action');
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize(),
			method: 'POST',
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					location.reload();
				}else{
				 	$("#error").text('');
					var obj = data.error,  
			        ul = $("<ul>");                    
			        for (var i = 0, l = obj.length; i < l; ++i) {
			            ul.append("<li>" + obj[i] + "</li>");
			        }
			        $("#error").append(ul);
				}
			}
		});
	}
	e.preventDefault();
});

$("#submitactivity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		submitstatus: "is_natural_no_zero",
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

$("#myAction" ).on('show.bs.modal', function(){
    $("#submitstatus").val(0);
    $("#submitremarks").val('');
     $("#error").empty();
    $('.form-group').removeClass('has-error');
});


@stop