@section('scripts')

$(".disable-button").disableButton();

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

function getChannel(){
	$.ajax({
		type: "GET",
		url: "../../api/channelselected?id={{$activity->id}}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree4").fancytree("getTree").getNodeByKey(node).setSelected(true);
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

  		bootbox.dialog({
		  message: "Do you want to save changes?",
		  title: "ETOP",
		  buttons: {
		    success: {
		      	label: "Yes",
		      	className: "btn btn-primary",
		      	callback: function() {
		        	if($( "#"+target_id).valid()){
			  			$( "#"+target_id).submit();
			  			$('form').areYouSure( {'silent':true} );
			  			callback();
			  		}else{
			  			$('html, body').animate({
					         scrollTop: ($('.has-error').offset().top - 300)
					    }, 500);
			  		}
		      }
		    },
		    danger: {
		      	label: "No",
		      	className: "btn btn-default",
		      	callback: function() {
		        	$('form').areYouSure( {'silent':true} );
			  		callback();
		      }
		    },
		  }
		});

	}else{
		callback();
	}
};


$('#updateActivity,#updateCustomer,#updateBilling,#updatetimings').areYouSure();


$("a[href='#customer']").on('shown.bs.tab', function(e) {
	$("#tree4").fancytree("disable");
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

			$('#end_date').val(msg.end_date);
			$('#end_date').data("DateTimePicker").setMinDate(moment(msg.end_date).format('MM/DD/YYYY'));
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

$('select#skus').multiselect({
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
	minDate: moment("{{date_format(date_create($activity->eimplementation_date),'m/d/Y')}}"),
});

$('#end_date').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	minDate: moment()
});

$("#implementation_date").on("dp.change",function (e) {
	$.ajax({
		type: "GET",
		url: "../../activitytype/"+$('#activity_type').val()+"/network/totalduration?sd="+moment($('#implementation_date').val()).format('DD-MM-YYYY'),
		success: function(msg){
			$('#lead_time').val(msg.days);
			$('#implementation_date').val(msg.end_date);
			$('#download_date').val(msg.start_date)
			//$('#implementation_date').data("DateTimePicker").setMinDate(moment(msg.min_date).format('MM/DD/YYYY'));
			$('#end_date').val(msg.end_date);
			$('#end_date').data("DateTimePicker").setMinDate(moment(msg.end_date).format('MM/DD/YYYY'));
			
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

function updatecategory(){
	$.ajax({
		type: "POST",
		data: {divisions: GetSelectValues($('select#division :selected')),id: {{ $activity->id }}},
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
			updateskus();
		   }
		});
}

function updateskus(){
	$.ajax({
			type: "POST",
			data: {brand: GetSelectValues($('select#brand :selected')),id: {{ $activity->id }}},
			url: "../../api/sku/skuselected",
			success: function(data){
				$('select#skus').empty();
				$.each(data.selection, function(i, text) {
					var sel_class = '';
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
					$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#skus'));
				});
			$('select#skus').multiselect('rebuild');
		   }
		});
}


var div = $("select#division").val();
if(parseInt(div) > 0) {
  updatecategory();
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
		updateskus();
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
					//bootbox.alert("Activity details was successfully updated."); 
					location.reload();
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
	}
	
	e.preventDefault();
});

$("#updateActivity").validate({
	ignore: ':hidden:not(".multiselect")',
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		activity_title: {
			required: true,
			maxlength: 80
			},
		scope: "is_natural_no_zero",
		activity_type: "is_natural_no_zero",
		cycle: {
			is_natural_no_zero: true,
			required: true
		},
		implementation_date: {
			required: true,
			greaterdate : true
		},
		"approver[]": {
			needsSelection: true
		},
		end_date: {
			required: true,
			greaterdate : true
		},
		"division[]": {
			needsSelection: true
		},
		"category[]": {
			needsSelection: true
		},
		"brand[]": {
			needsSelection: true
		},
		"objective[]": {
			needsSelection: true
		},
		background: {
			required: true,
		},

	},
	errorPlacement: function(error, element) {    
	
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
    	
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	},
  	invalidHandler: function(form, validator) {
        var errors = validator.numberOfInvalids();
        if (errors) {
            $("html, body").animate({ scrollTop: 0 }, "fast");
           
        }
    }
});

$.validator.addMethod("needsSelection", function (value, element) {
    var count = $(element).find('option:selected').length;
    return count > 0;
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

var selectedkeys;
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
		var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
			 return node.key;
		});
		selectedkeys = selKeys;
		//console.log(selKeys);
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
			$("#tree4").fancytree("enable");
			getChannel();
		}else{
			$("#tree4").fancytree("getTree").visit(function(node){
		        node.setSelected(false);
		    });
			$("#tree4").fancytree("disable");
		}
		$("#customers").val(selRootKeys.join(", "));
		show_alloc();
	},
	click: function(event, data) {
        $("#updateCustomer").addClass("dirty");
        if(data.targetType == "checkbox"){
        	//console.log(data.node.tree);
	        var keys = data.node.key.split(".");
	        if($.inArray('E1397', keys) != -1){
				$("#tree4").fancytree("getTree").visit(function(node){
			        node.setSelected(true);
			    });
			}
    	}
       
    }
});

$("#btnCDeselectAll").click(function(){

  	$("#tree3").fancytree("getTree").visit(function(node){
    	node.setSelected(false);
  	});
  	$("#updateCustomer").addClass("dirty");
  	return false;
});
$("#btnCSelectAll").click(function(){
  	$("#tree3").fancytree("getTree").visit(function(node){
    	node.setSelected(true);
  	});
  	$("#updateCustomer").addClass("dirty");
  	return false;
});

$("#tree4").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/channels?id={{$activity->id}}"
	},
	select: function(event, data) {
		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});


		var keys = selRootKeys.join(".").split(".");
		$("#channels_involved").val(selRootKeys.join(", "));
	},
	click: function(event, data) {
        $("#updateCustomer").addClass("dirty");
    },
});

$("#btnChDeselectAll").click(function(){
	if(!$("#tree4").hasClass( "ui-fancytree-disabled" )){
		$("#tree4").fancytree("getTree").visit(function(node){
	    	node.setSelected(false);
	  	});
	}
  	$("#updateCustomer").addClass("dirty");
  	return false;
});
$("#btnChSelectAll").click(function(){
	if(!$("#tree4").hasClass( "ui-fancytree-disabled" )){
		$("#tree4").fancytree("getTree").visit(function(node){
	    	node.setSelected(true);
	  	});
	}
$("#updateCustomer").addClass("dirty");
  	
  	return false;
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

<!-- force alloc -->

$('.input-number').inputmask({'mask':["9{0,5}.9{0,2}", "99999"]});


function show_alloc(){
	var force_alloc = $('input[name="allow_force"]:checked').length > 0;
	if(force_alloc){
		show_force_alloc();
	}else{
		$('#force_alloc').find('input').attr('disabled','disabled');
	}
}



$('#allow_force').click(function() {
    var $this = $(this);
    // $this will contain a reference to the checkbox   
    if ($this.is(':checked')) {
        // the checkbox was checked 
        //$('#force_alloc').find('input').removeAttr('disabled');
       	show_force_alloc();
    } else {
        // the checkbox was unchecked
        $('#force_alloc').find('input').attr('disabled','disabled');
    }
});

function show_force_alloc(){
	var a = [];
    $.each( selectedkeys, function( key, value ) {
    	var arr = value.split('.');
    	$.each( arr, function( key, value2 ) {
    		a.push(value2);
    	});
	});

	$('input', $('#force_alloc')).each(function () {
		if($.inArray($(this).attr("id"), a) != -1){
			$(this).removeAttr('disabled');
		}else{
			$(this).attr('disabled','disabled');
		}
	});
}
<!-- schemes -->



<!-- Budget details -->

$('#billing_deadline').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
$('#billing_deadline').datetimepicker({
	pickTime: false,
	calendarWeeks: true
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
			calendarWeeks: true
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");
	},onEditRow : function(){
		$('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true
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
			calendarWeeks: true
		});
		$('#budget_amount').inputNumber();
	},onEditRow : function(){
		$('#budget_startdate, #budget_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#budget_startdate, #budget_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true
		});
		$('#budget_amount').inputNumber();
	},onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}

});


<!-- activity timings -->

$('.timing_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
$('.timing_date').datetimepicker({
	pickTime: false,
	calendarWeeks: true
});


var $container = $("#roles");

function getRoles() {
    var roles = "";

    $.ajax({
        async: false,
        type: "GET",
        url: "{{ URL::action('ActivityController@activityroles', $activity->id ) }}",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (msg) { roles = msg.d; },
        error: function (msg) { roles = msg; }
    });
    return roles;
}

$container.handsontable({
	data: getRoles(),
	startRows: 5,
    minSpareRows: 1,
    rowHeaders: true,
    colHeaders: true,
    contextMenu: false,
    colWidths: [300, 300, 300],
	colHeaders: ["Process Owner", "Action Points", "Timing"],
	columns: [{
      data: "owner",
      type: 'text',
    },{
      data: "point",
      type: 'text',
    },{
      data: "timing",
      type: 'text',
    }],
	afterDeselect: function () {
		$("#updatetimings").addClass("dirty");
	}
});

var handsontable = $container.data('handsontable');

$("form[id='updatetimings']").on("submit",function(e){
	var form = $(this);
	var url = form.prop('action');
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize() + "&roles=" + encodeURIComponent(JSON.stringify(handsontable.getData())),
			method: 'POST',
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					//bootbox.alert("Activity details was successfully updated."); 
					location.reload();
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
	}
	
	e.preventDefault();
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