@section('scripts')

	$('#st,#scope,#pro,#planner,#app,#type').multiselect({
		maxHeight: 200,
		includeSelectAllOption: true,
		enableCaseInsensitiveFiltering: true,
		enableFiltering: true
	});

	$('select#division').multiselect({
		maxHeight: 200,
		includeSelectAllOption: true,
		enableCaseInsensitiveFiltering: true,
		enableFiltering: true,
		
	});


	$('select#category').multiselect({
		maxHeight: 200,
		includeSelectAllOption: true,
		enableCaseInsensitiveFiltering: true,
		enableFiltering: true,
		
	});

	$('select#brand').multiselect({
		maxHeight: 200,
		includeSelectAllOption: true,
		enableCaseInsensitiveFiltering: true,
		enableFiltering: true,
	});

@stop