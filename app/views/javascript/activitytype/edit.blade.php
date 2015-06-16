@section('scripts')


$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		activity_type: "required",
		uom: "required",
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