@section('scripts')

$('#st,#cy,#sc,#ty,#pm').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

@stop