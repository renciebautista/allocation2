@section('scripts')

$("#updatescheme").disableButton();

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

$("#skus,#involve,#premuim").chosen({
	search_contains: true,
	allow_single_deselect: true
});


$('#pr, #srp_p, #other_cost,#total_alloc,#lpat').inputNumber({ allowDecimals: true });

$('#pr, #srp_p, #other_cost,#lpat').blur(function() {
	var srp = accounting.unformat($('#srp_p').val()) || 0;
	var others = accounting.unformat($('#other_cost').val()) || 0;
	$('#ulp').val(accounting.formatNumber(srp+others, 2, ",","."));
	var ulp = accounting.unformat($('#ulp').val()) || 0;
	var pr = accounting.unformat($('#pr').val()) || 0;
	var lpat = accounting.unformat($('#lpat').val()) || 0;
	if(lpat == 0){
		$('#cost_sale').val(0);
	}else{
		cost_sale = accounting.formatNumber((ulp/lpat) * 100 ,2);
		if(((ulp/lpat) * 100) > 30){
			$('#cost_sale').parent('div').addClass('has-error');
		}else{
			$('#cost_sale').parent('div').removeClass('has-error');
		}
		$('#cost_sale').val(cost_sale);
	}
	compute_budget();
});


$('#updatescheme').areYouSure();
$('#scheme_back').click(function(e) {
    e.preventDefault();
    console.log(this.href);
    checkDirty("updatescheme",function(){
			var url = "{{action('ActivityController@edit', $activity->id);}}#schemes";    
			$(location).attr('href',url);
		});
});

function checkDirty(target_id,callback) {
  	if ($('#'+target_id).hasClass('dirty')) {
  		bootbox.dialog({
		  message: "Do you want to save changes?",
		  title: "ETOP",
		  buttons: {
		    success: {
		      	label: "Yes",
		      	className: "btn btn-primary",
		      	callback: function() {
		        	if($( "#"+target_id).valid()){
			  			$( "#"+target_id).submit();
			  			$('form').areYouSure( {'silent':true} );
			  		}else{
			  			$('html, body').animate({
					         scrollTop: ($('.has-error').offset().top - 300)
					    }, 500);
			  		}
		      }
		    },
		    danger: {
		      	label: "No",
		      	className: "btn btn-default",
		      	callback: function() {
		        	$('form').areYouSure( {'silent':true} );
			  		callback();
		      }
		    },
		  }
		});

	}else{
		callback();
	}
};

var non_ulp = false;

$('#ulp_premium').on('change', function(){
	var value = $(this).val();
	if ( value.length > 0 ){
		$('#premuim').prop('disabled', true).trigger("chosen:updated");
		non_ulp = false;
	}else{
		$('#premuim').prop('disabled', false).trigger("chosen:updated");
		non_ulp = true;
	}

	$('#premuim').val('').trigger('chosen:updated');

	compute_budget();
});

$("#premuim").on('change', function (evt, params) {
    var SelectedIds = $(this).find('option:selected').map(function () {
        return $(this).val();
    }).get();
    
    $('#ulp_premium').val("");
    if ( SelectedIds != "0" ){
		$('#ulp_premium').prop('disabled', true);
		non_ulp = true;
	}else{
		$('#ulp_premium').prop('disabled', false);
		non_ulp = false;
	}

	compute_budget();
})

if($("#ulp_premium").val().length !=0){
	$('#ulp_premium').prop('disabled', false);
	$('#premuim').prop('disabled', true).trigger("chosen:updated");	
}else{
	$('#premuim').prop('disabled', false).trigger("chosen:updated");
	$('#ulp_premium').prop('disabled', true);
}


$('#total_alloc,#deals').blur(function() {
	compute_budget();
});

function compute_budget(){
	var total_alloc = accounting.unformat($('#total_alloc').val()) || 0;
	var srp = accounting.unformat($('#srp_p').val()) || 0;
	var others = accounting.unformat($('#other_cost').val()) || 0;
	var deals = accounting.unformat($('#deals').val()) || 0;

	if($('#uom').val() == "CASES"){
		$('#total_deals').val(accounting.formatNumber(total_alloc*deals));
		$('#total_cases').val(accounting.formatNumber(total_alloc));

		$('#tts_r').val(accounting.formatNumber(total_alloc*deals*srp, 2, ",","."));

		
	}else{
		$('#total_deals').val(accounting.formatNumber(total_alloc));
		if(deals < 1){
			$('#total_cases').val(0);
		}else{
			$('#total_cases').val(accounting.formatNumber(Math.ceil(total_alloc/deals)));
		}
		

		$('#tts_r').val(accounting.formatNumber(total_alloc*srp, 2, ",","."));
	}

	var total_deals = accounting.unformat($('#total_deals').val()) || 0;
	per = accounting.formatNumber(total_deals*others, 2, ",",".");
	var tts_r = 0;

	if(non_ulp){
		non = accounting.formatNumber(srp * total_deals, 2, ",",".");
		$('#pe_r').val(per+non);
		tts_r = 0;
		$('#tts_r').val(accounting.formatNumber(0, 2, ",","."));
	}else{
		tts_r = accounting.unformat($('#tts_r').val()) || 0;
		$('#pe_r').val(per);
	}
	
	
	

	var pe_r = accounting.unformat($('#pe_r').val()) || 0;

	$('#total_cost').val(accounting.formatNumber(tts_r+pe_r, 2, ",","."));

	getWeeks();
}

$("#updatescheme").disableButton();

$("#updatescheme").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		scheme_name: {
			required: true,
			maxlength: 80
			},
		item_code: {
			minlength: 8,
			maxlength: 8
			},
		item_barcode: {
			minlength: 13,
			maxlength: 13
			},
		item_casecode: {
			minlength: 14,
			maxlength: 14
			},
		total_alloc: "required",
		deals: "required",
		'skus[]': {
                is_natural_no_zero: true
            },
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

table.on('dblclick',"tr[data-link]",function() {
	$(this).find('td').each (function() {
		field = $(this).attr('field');
		$('#alloc_table td[field="'+field+'"]').text($(this).text());
	});  
	var id = $(this).attr('data-link');
	alloc = $.trim($('#customer-allocation tr[data-link="'+id+'"] td[field="alloc"]').text());

	$('#updateAlloc').find('#new_alloc').val(alloc);
	$('#updateAlloc').find('#scheme_id').val(id); 

	$('#myModal').modal('show');
});


$("form[id='updateAlloc']").on("submit",function(e){
	var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');
	$.ajax({
		url: url,
		data: form.serialize(),
		method: "POST",
		dataType: "json",
		success: function(data){
			if(data.success == "1"){
				bootbox.alert("Allocation was successfully updated."); 
				$('#myModal').modal('hide');

				$('#final_total_alloc').val(accounting.formatNumber(data.final_total));
				$('#final_total_deals').val(accounting.formatNumber(data.final_total_deals));
				$('#final_total_cases').val(accounting.formatNumber(data.final_total_cases));
				$('#final_tts_r').val(accounting.formatNumber(data.final_tts_r));
				$('#final_pe_r').val(accounting.formatNumber(data.final_pe_r));
				$('#final_total_cost').val(accounting.formatNumber(data.final_total_cost));

				table.api().ajax.reload();
				
				
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});
@stop
