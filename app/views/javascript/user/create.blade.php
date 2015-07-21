@section('scripts')

$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		username: "required",
		password : {
			required: true,
            minlength : 6
        },
        password_confirmation : {
        	required: true,
            minlength : 6,
            equalTo : "#password"
        },
		email: {
			required: true,
			email: true
		},
		first_name: "required",
		last_name: "required",
		group_id: {
			required: true,
			is_natural_no_zero: true,
		}

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