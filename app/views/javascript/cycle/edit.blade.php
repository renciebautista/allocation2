@section('scripts')
$('#vetting_deadline, #replyback_deadline, #submission_deadline, #release_date, #emergency_deadline, #emergency_release_date, #implemintation_date').datetimepicker({
		pickTime: false
	});

$('#vetting_deadline, #replyback_deadline, #submission_deadline, #release_date, #emergency_deadline, #emergency_release_date, #implemintation_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
$('#month_year').mask("99/9999",{placeholder:"mm/yyyy"});

$('#month_year').datetimepicker({
    minViewMode: 'months',
    viewMode: 'months',
    pickTime: false,
    format: "MM/YYYY"
})

$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		cycle_name: "required",
		month_year: "required",
		vetting_deadline: "required",
		replyback_deadline: "required",
		submission_deadline: "required",
		release_date: "required",
		implemintation_date: "required",

	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
		$(element.closest('div')).addClass(errorClass).removeClass(validClass);
	},
	unhighlight: function( element, errorClass, validClass ) {
		$(element.closest('div')).removeClass(errorClass).addClass(validClass);
	}
});
@stop