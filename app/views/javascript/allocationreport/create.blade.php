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
		onDropdownHide: function(event) {
			$.ajax({
				type: "POST",
				data: {divisions: GetSelectValues($('select#division :selected'))},
				url: "{{ URL::action('api\SkuController@category') }}",
				success: function(data){
					$('select#category').empty();
					$.each(data, function(i, text) {
						$('<option />', {value: i, text: text}).appendTo($('select#category')); 
					});
				$('select#category').multiselect('rebuild');
			   }
			});
		}
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
				url: "{{ URL::action('api\SkuController@brand') }}",
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

@stop