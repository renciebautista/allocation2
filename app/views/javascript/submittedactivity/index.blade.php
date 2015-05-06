@section('scripts')

$('#st,#cy,#sc,#ty,#pr').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

@stop