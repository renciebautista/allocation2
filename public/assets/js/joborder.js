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

	$("#assign_to").chosen({
		search_contains: true,
		allow_single_deselect: true
	});

	$('#filer_input').filer({
	    changeInput: true,
	    showThumbs: true,
	    addMore: true
	});

	
});