@section('scripts')

var hostname = 'http://' + $(location).attr('host');
var activity_id = $('#act_id').val();


if(location.hash.length > 0){
	var activeTab = $('[href=' + location.hash + ']');
	activeTab && activeTab.tab('show');
}

// Change hash for page-reload
$('.nav-tabs a').on('shown', function (e) {
    window.location.hash = e.target.hash;
})

$('.nav-tabs a').click(function (e) {
	pre = "#activity";
	if(window.location.hash.length > 0){
		pre = window.location.hash;
	}
	var target = $(this);
	target_id = $(pre).find('form').attr('id');	
	$(target).tab('show');
});

$(".btn-style").click(function (e) {
	e.preventDefault();
	target_id = $(this.closest('form')).attr('id');
	var target = $(".nav-tabs li.active");
	var sibbling;
	if ($(this).text() === "Next") {
		sibbling = target.next();
	} else {
		sibbling = target.prev();
	}

	if (sibbling.is("li")) {
		$('#'+sibbling.children("a").attr("id")).trigger('click');
		str = sibbling.children("a").attr("href");
		location.hash = str.replace("#","");
	}
});



$("a[href='#customer']").on('shown.bs.tab', function(e) {
    getCustomer();
});

$("a[href='#schemes']").on('shown.bs.tab', function(e) {
    $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
});




<!-- activity details -->
$('select#approver').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#approver option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    }
});


function updatecategory(){
	$.ajax({
		type: "POST",
		data: {divisions: GetSelectValues($('select#division :selected')),id: {{ $activity->id }}},
		url: "../../api/category/getselected",
		success: function(data){
			$('select#category').empty();
			$.each(data.selection, function(i, text) {
				var sel_class = '';
				if($.inArray( i,data.selected ) > -1){
					sel_class = 'selected="selected"';
				}
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#category')); 
			});
		$('select#category').multiselect('rebuild');
		updatebrand();
	   }
	});
}

function updatebrand(){
	$.ajax({
			type: "POST",
			data: {categories: GetSelectValues($('select#category :selected')),id: {{ $activity->id }}},
			url: "../../api/brand/getselected",
			success: function(data){
				$('select#brand').empty();
				$.each(data.selection, function(i, text) {
					var sel_class = '';
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
					$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#brand'));
				});
			$('select#brand').multiselect('rebuild');
			updateskus();
		   }
		});
}

function updateskus(){
	$.ajax({
			type: "POST",
			data: {brand: GetSelectValues($('select#brand :selected')),id: {{ $activity->id }}},
			url: "../../api/sku/skuselected",
			success: function(data){
				$('select#skus').empty();
				$.each(data.selection, function(i, text) {
					var sel_class = '';
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
					$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#skus'));
				});
			$('select#skus').multiselect('rebuild');
		   }
		});
}

var div = $("select#division").val();
if(parseInt(div) > 0) {
  updatecategory();
}


$('select#division').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#division option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    },
	onDropdownHide: function(event) {
		updatecategory();
	}
});


$('select#category').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#category option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    },
	onDropdownHide: function(event) {
		updatebrand();
	}
});

$('select#brand').multiselect({
	maxHeight: 200,
	numberDisplayed: 1,
	onDropdownShow: function(event) {
        $('select#brand option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    },
	onDropdownHide: function(event) {
		updateskus();
	}
});


$('select#objective').multiselect({
	maxHeight: 200,
	numberDisplayed: 1,
	onDropdownShow: function(event) {
        $('select#objective option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    }
});

<!-- Customer details -->
$('select#channel').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#channel').multiselect('disable');
 
// fancy tree
$("#tree3").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/getpostedcustomers?id={{$activity->id}}&status=2"
	},
	select: function(event, data) {
		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		// console.log(keys);
		if($.inArray('E1397', keys) != -1){
			$("#tree4").fancytree("enable");
			getChannel();
		}else{
			$("#tree4").fancytree("getTree").visit(function(node){
		        node.setSelected(false);
		    });
			$("#tree4").fancytree("disable");
		}
	}
});

function getCustomer(){
	$.ajax({
		type: "GET",
		url: "../../api/customerselected?id={{$activity->id}}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree3").fancytree("getTree").getNodeByKey(node).setSelected(true);
			});
		}
	});
}


$("#tree4").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/getpostedchannels?id={{$activity->id}}"
	}
});


function getChannel(){
	$.ajax({
		type: "GET",
		url: "../../api/channelselected?id={{$activity->id}}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree4").fancytree("getTree").getNodeByKey(node).setSelected(true);
			});
		}
	});
}

function updatechannel(){
	$.ajax({
		type: "GET",
		url: "{{ URL::action('ActivityController@channels', $activity->id ) }}",
		success: function(data){
			$('select#channel').empty();
			$.each(data.selection, function(i, text) {
				var sel_class = '';
				if(data.selected.length > 0){
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
				}else{
					sel_class = 'selected="selected"';
				}
				
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#channel')); 
			});
		$('select#channel').multiselect('rebuild');
	   }
	});
}


<!-- schemes -->

<!-- Budget details -->

<!-- activity timings -->
$('select#skus').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#skus option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    }
});

var $container = $("#roles");

function getRoles() {
    var roles = "";

    $.ajax({
        async: false,
        type: "GET",
        url: "{{ URL::action('ActivityController@activityroles', $activity->id ) }}",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (msg) { roles = msg.d; },
        error: function (msg) { roles = msg; }
    });
    return roles;
}

$container.handsontable({
	data: getRoles(),
	startRows: 5,
    minSpareRows: 1,
    rowHeaders: true,
    colHeaders: true,
    contextMenu: false,
    colWidths: [300, 300, 300],
	colHeaders: ["Process Owner", "Action Points", "Timing"],
	columns: [{
      data: "owner",
      readOnly: true
    },{
      data: "point",
      readOnly: true
    },{
      data: "timing",
     	readOnly: true
    }]
});

<!-- update activty -->

$("form[id='updateactivity']").on("submit",function(e){
	var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');
	$.ajax({
		url: url,
		data: form.serialize(),
		method: method,
		dataType: "json",
		success: function(data){
			if(data.success == "1"){
				location.reload();
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});

$("#updateactivity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		submitstatus: "is_natural_no_zero"
	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	}
});

$("#myAction" ).on('show.bs.modal', function(){
    $("#submitstatus").val(0);
    $("#submitremarks").val('');
    $('.form-group').removeClass('has-error');
});


@stop