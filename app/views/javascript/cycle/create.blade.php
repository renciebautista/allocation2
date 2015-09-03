@section('scripts')

$('#start_date, #end_date, #submission_deadline, #approval_deadline, #pdf_deadline, #release_date, #implemintation_date').datetimepicker({
		pickTime: false
});

$('#start_date, #end_date, #submission_deadline, #approval_deadline, #pdf_deadline, #release_date, #implemintation_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});


$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		cycle_name: "required",
		start_date: "required",
		end_date: "required",
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