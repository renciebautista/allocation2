@section('scripts')

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
	$('#weeks_alloc').val(accounting.formatNumber(allocs_in_cases/avg_wk_sales));
}

getWeeks();

var table = $("#customer-allocation").dataTable({
		"scrollY": "500px",
		"scrollX": true,
		"scrollCollapse": true,
		"paging": false,
		"bSort": false,
		"ajax": "{{ URL::action('SchemeController@allocation', $scheme->id ) }}",
		"columnDefs": [ { //this prevents errors if the data is null
			"targets": "_all",
			"defaultContent": ""
		} ],
		"columns": [
			//title will auto-generate th columns
		   // { "data" : "id",               "title" : "Id", "searchable": false },
			{ "data" : "group",         "title" : "Group", "searchable": true },
			{ "data" : "area",         "title" : "Area", "searchable": true },
			{ "data" : "sold_to",            "title" : "Sold To", "searchable": true },
			{ "data" : "ship_to",     "title" : "Ship To", "searchable": true },
			{ "data" : "channel",     "title" : "Channel", "searchable": true },
			{ "data" : "outlet",     "title" : "Outlet", "searchable": true },
			/*{ "data" : "sold_to_gsv",     "title" : "SOLD TO GSV", "searchable": true },
			{ "data" : "sold_to_gsv_p",     "title" : "SOLD TO GSV %", "searchable": true },
			{ "data" : "sold_to_alloc",     "title" : "SOLD TO ALLOC", "searchable": true },
			{ "data" : "ship_to_gsv",     "title" : "SHIP TO GSV", "searchable": true },
			{ "data" : "ship_to_alloc",     "title" : "SHIP TO ALLOC", "searchable": true },
			{ "data" : "outlet_to_gsv",     "title" : "OUTLET GSV", "searchable": true },
			{ "data" : "outlet_to_gsv_p",     "title" : "OUTLET ALLOC %", "searchable": true },
			{ "data" : "outlet_to_alloc",     "title" : "OUTLET ALLOC", "searchable": true },
			{ "data" : "multi",     "title" : "MULTI", "searchable": false },*/
			{ "data" : "computed_alloc",     "title" : "COMPUTED ALLOC", "searchable": false },
			{ "data" : "force_alloc",     "title" : "FORCED ALLOC", "searchable": false },
			{ "data" : "final_alloc",     "title" : "FINAL ALLOC", "searchable": false }
		],
		"createdRow" : function( row, data, index ) {
			if(((data.customer_id === null) && (data.shipto_id === null)) || ((data.customer_id !== null) && (data.shipto_id === null))){
				$(row).attr('data-link', data.id);
			}

			if((data.customer_id === null) && (data.shipto_id === null)){
				$(row).addClass("blue");
			}

			if((data.customer_id !== null) && (data.shipto_id === null)){
				$(row).addClass("white");
			}

			if((data.customer_id !== null) && (data.shipto_id !== null)){
				$(row).addClass("orange");
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
				if(index == 17){
					$(this).attr('field', 'alloc');
				}
			}); 
		}
	});
new $.fn.dataTable.FixedColumns( table, {
	leftColumns: 6
} );

@stop
