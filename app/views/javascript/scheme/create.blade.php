@section('scripts')

$("#myform").disableButton();

$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		scheme_name: "required",
		item_code: "required",
		item_barcode: "required",
		item_casecode: "required",
		pr: "required",
		srp_p: "required",
		total_alloc: "required",
		deals: "required",
		'skus[]': {
                is_natural_no_zero: true
            },
        'involve[]': {
                is_natural_no_zero: true
            }
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


$("#skus,#involve,#premuim").chosen({
	search_contains: true,
	allow_single_deselect: true
});

$('#pr, #srp_p, #other_cost,#total_alloc').inputNumber();

$('#pr, #srp_p, #other_cost').blur(function() {
	var srp = accounting.unformat($('#srp_p').val()) || 0;
	var others = accounting.unformat($('#other_cost').val()) || 0;
	$('#ulp').val(accounting.formatNumber(srp+others, 2, ",","."));
	var ulp = accounting.unformat($('#ulp').val()) || 0;
	var pr = accounting.unformat($('#pr').val()) || 0;
	if(pr == 0){
		$('#cost_sale').val(0);
	}else{
		$('#cost_sale').val(accounting.formatNumber((ulp/pr) * 100 ,2));
	}
	compute_budget();
});



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

	$('#pe_r').val(accounting.formatNumber(total_deals*others, 2, ",","."));

	var tts_r = accounting.unformat($('#tts_r').val()) || 0;
	var pe_r = accounting.unformat($('#pe_r').val()) || 0;

	$('#total_cost').val(accounting.formatNumber(tts_r+pe_r, 2, ",","."));
}

$('#myform').areYouSure();
$('#back').click(function(e) {
    e.preventDefault();
    checkDirty("myform",function(){
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

@stop
