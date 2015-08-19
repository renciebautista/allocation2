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

	$("#tree3").fancytree({
	checkbox: true,
	selectMode: 3,
	source: {
		url: "{{ URL::action('AllocationReportController@customer') }}"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
			 return node.key;
		});
		selectedkeys = selKeys;
		//console.log(selKeys);
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		
		$("#customers").val(selRootKeys.join(", "));
	}
});

$("#tree4").fancytree({
	checkbox: true,
	selectMode: 3,
	source: {
		url: "{{ URL::action('AllocationReportController@outlets') }}"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
			 return node.key;
		});
		selectedkeys = selKeys;
		//console.log(selKeys);
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		
		$("#outlets_involved").val(selRootKeys.join(", "));
	}
});

$("#tree5").fancytree({
	checkbox: true,
	selectMode: 3,
	source: {
		url: "{{ URL::action('AllocationReportController@channels') }}"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
			 return node.key;
		});
		selectedkeys = selKeys;
		//console.log(selKeys);
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		
		$("#channels_involved").val(selRootKeys.join(", "));
	}
});

$("#myform").validate({
	ignore: ':hidden:not(".multiselect")',
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		name: {
			required: true,
			maxlength: 80
			}
	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	},
  	invalidHandler: function(form, validator) {
        var errors = validator.numberOfInvalids();
        if (errors) {
              $("html, body").animate({ scrollTop: 0 }, "fast");
        }
    }
});


@stop