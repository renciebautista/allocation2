@section('scripts')

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
			

			getCycle(msg.end_date);

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
			$('#implementation_date').data("DateTimePicker").setMinDate(moment(msg.min_date).format('MM/DD/YYYY'));
			$('#end_date').val(msg.end_date);
			$('#end_date').data("DateTimePicker").setMinDate(moment(msg.end_date).format('MM/DD/YYYY'));
			
			getCycle(msg.end_date,{{$activity->id}});
		},
		error: function(){
			alert("failure");
		}
	});

	
	
});

function getCycle(date){
	$.ajax({
		type: "GET",
		data: {date: date},
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

$("#myform").validate({
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

$('select#division').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});


$('select#division').on("change",function(){
	$.ajax({
			type: "POST",
			data: {divisions: GetSelectValues($('select#division :selected'))},
			url: "../api/category",
			success: function(data){
				$('select#category').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#category')); 
				});
			$('select#category').multiselect('rebuild');
		   }
		});
});


$('select#category').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		$.ajax({
			type: "POST",
			data: {categories: GetSelectValues($('select#category :selected'))},
			url: "../api/brand",
			success: function(data){
				$('select#brand').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#brand')); 
				});
			$('select#brand').multiselect('rebuild');
		   }
		});
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

$("#involve").chosen();

$('select#objective').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

@stop