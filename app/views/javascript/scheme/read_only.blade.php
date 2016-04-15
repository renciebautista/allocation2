@section('scripts')

//sob tab

$('#start_date').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	//minDate: moment(),
	daysOfWeekDisabled: [0,2,3,4,5,6]
});

$('#start_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});


$('#weeks').inputNumber({ allowDecimals: true });


if(location.hash.length > 0){
	var activeTab = $('[href=' + location.hash + ']');
	activeTab && activeTab.tab('show');
}

// Change hash for page-reload
$('.nav-tabs a').on('shown', function (e) {
    window.location.hash = e.target.hash;
})


$('.nav-tabs a').click(function (e) {
	pre = "#details";
	if(window.location.hash.length > 0){
		pre = window.location.hash;
	}
	var target = $(this);
	target_id = $(pre).find('form').attr('id');	
	$(target).tab('show');
});

// calculator
$('#calculate').on( "click", function() {
	var avg_wk_sales = accounting.unformat($('#weekly_sales').val()) || 0;
	var no_weeks = accounting.unformat($('#no_weeks').val()) || 0;
	$('#no_alloc_cases').val(accounting.formatNumber((avg_wk_sales*no_weeks)));
});

$('#myCalculator').on('show.bs.modal', function (e) {
  	$('#no_alloc_cases').val(0);
  	$('#no_weeks').val(0);
})

function getWeeks(){
	var avg_wk_sales = accounting.unformat($('#weekly_sales').val()) || 0;
	var allocs_in_cases = accounting.unformat($('#total_cases').val()) || 0;
	$('#weeks_alloc').val(accounting.formatNumber(allocs_in_cases/avg_wk_sales,2) || 0);
}

getWeeks();

$('select#involve').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#involve option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    }
});

$('select#premuim').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#premuim option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    }
});

var table = $("#customer-allocation").dataTable({
		"scrollY": "500px",
		"scrollCollapse": true,
		"paging": false,
		"bSort": false,
		"ajax": "{{ URL::action('SchemeController@allocation', $scheme->id ) }}",
		"columnDefs": [ { //this prevents errors if the data is null
			"targets": "_all",
			"defaultContent": ""
		} ],
		"columns": [
			{ "data" : "group",         "title" : "GROUP", "searchable": true ,},
			{ "data" : "area",         "title" : "AREA", "searchable": true },
			{ "data" : "sold_to",            "title" : "SOLD TO", "searchable": true },
			{ "data" : "ship_to",     "title" : "SHIP TO", "searchable": true },
			{ "data" : "channel",     "title" : "CHANNEL", "searchable": true },
			{ "data" : "outlet",     "title" : "OUTLET", "searchable": true },
			{ "data" : "computed_alloc",     "title" : "COMPUTED ALLOC", "searchable": false,"className": "right"},
			{ "data" : "force_alloc",     "title" : "FORCED ALLOC", "searchable": false,"className": "right"},
			{ "data" : "final_alloc",     "title" : "FINAL ALLOC", "searchable": false,"className": "right"}
		],
		"createdRow" : function( row, data, index ) {
			if(((data.customer_id === null) && (data.shipto_id === null)) || ((data.customer_id !== null) && (data.shipto_id === null))){
				$(row).attr('data-link', data.id);
			}

			if((data.customer_id === null) && (data.shipto_id === null)){
				$(row).addClass("light-blue");
			}

			if((data.customer_id !== null) && (data.shipto_id === null)){
				$(row).addClass("white");
			}

			if((data.customer_id !== null) && (data.shipto_id !== null)){
				$(row).addClass("light-orange");
			}
			

			$(row).find('td').each (function(index) {
				if(index == 0){
					$(this).attr('field', 'group');
				}
				if(index == 1){
					$(this).attr('field', 'area');
				}
				if(index == 2){
					$(this).attr('field', 'soldto');
				}
				if(index == 3){
					$(this).attr('field', 'shipto');
				}
				if(index == 4){
					$(this).attr('field', 'channel');
				}
				if(index == 5){
					$(this).attr('field', 'outlet');
				}
				if(index == 8){
					$(this).attr('field', 'alloc');
				}
			}); 
		}
	});

/*new $.fn.dataTable.FixedColumns( table, {
	leftColumns: 6
} );*/
@stop
