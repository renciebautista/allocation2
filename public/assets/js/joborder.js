$(document).ready(function(){
	var hostname = 'http://' + $(location).attr('host');
	var activity_id = $('#act_id').val();

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

	$('select#task').on("change",function(){
		$.ajax({
			type: "GET",
			data: {task: $(this).val()},
			url: hostname + "/api/subtask",
			success: function(data){
				$('select#sub_task').empty();
				$('<option value="0">PLEASE SELECT</option>').appendTo($('select#sub_task')); 
				$.each(data.subtasks, function(i, text) {
					var sel_class = '';
					if( i == data.sel){
						sel_class = 'selected="selected"';
					}
					$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#sub_task')); 
				});
		   }
		});
	});

	$('#filer_input').filer({
	    changeInput: true,
	    showThumbs: true,
	    addMore: true
	});

	$("#myform").validate({
	ignore: ':hidden:not(".multiselect")',
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		task: "is_natural_no_zero",
		sub_task: "is_natural_no_zero",
		cycle: {
			is_natural_no_zero: true,
			required: true
		},
		start_date: {
			required: true,
		},
		end_date: {
			required: true,
		},
		details: {
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
	
});