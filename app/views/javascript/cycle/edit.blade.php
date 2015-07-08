@section('scripts')
$('#submission_deadline, #approval_deadline, #pdf_deadline, #release_date, #implemintation_date').datetimepicker({
		pickTime: false
});

$('#submission_deadline, #approval_deadline, #pdf_deadline, #release_date, #implemintation_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
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
		submission_deadline: "required",
		approval_deadline: "required",
		pdf_deadline: "required",
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