$(document).ready(function() {

	$("#btnCDeselectAll").click(function(){

	  	$("#tdtree").fancytree("getTree").visit(function(node){
	    	node.setSelected(false);
	  	});

	  	$("#updateCustomer").addClass("dirty");

  		return false;
	});
	$("#btnCSelectAll").click(function(){
	  	$("#tdtree").fancytree("getTree").visit(function(node){
	    	node.setSelected(true);
	  	});
	  	$("#updateCustomer").addClass("dirty");
	  	return false;
	});


	var hostname = 'http://' + $(location).attr('host');
	var activity_id = $('#activity_id').val();

	$('.qty, #coverage').inputNumber({ allowDecimals: false});

	function selected(){
		return $('#deal_type').val();
	}

	$('#select-all').change(function() {
    	var checkboxes = $(this).closest('table').find(':checkbox');
	    if($(this).is(':checked')) {
	        checkboxes.prop('checked', true);
	    } else {
	        checkboxes.prop('checked', false);
	    }
	});

	$('#select-all-host').change(function() {
    	var checkboxes = $(this).closest('table').find(':checkbox');
	    if($(this).is(':checked')) {
	        checkboxes.prop('checked', true);
	        if(selected() == 2){
		        $.each(checkboxes, function(obj){
			    	if($(this).attr('name') == 'skus[]'){
			    		addPremium($(this).val());
			    	}
			    })
	    	}
	    	changeName();
	    } else {
	        checkboxes.prop('checked', false);
	        $('#premium_sku').empty();
	    }
	});

	$(document).on("click",".sku-checkbox", function () {
	    changeName();
	});

	$('#deal_type, #uom').change(function() {
		changeName();
	});
	$('#buy, #additional_name, #free, .qty').on('blur',function() {
		changeName();
	})

	

	function changeName(){
		var type = $("#deal_type option:selected").text();
		var buy = $('#buy').val();
		var free = $('#free').val();
		var uom = $("#uom option:selected").text();
		var add_name = $("#additional_name").val();
		if(!!add_name){
			add_name = add_name.toUpperCase();
		}
		$('.input-pcs').text(uom);
		$('#scheme_name').val(type+" "+buy +"+"+free+" "+uom + " "+add_name);
		var individual = $("#deal_type option:selected").val();
		if(individual == 1){
			$('.collective').hide();
			$('.individual').show();
			$('#p_req').val('N/A').attr('disabled','disabled');
			$('#premium_sku').val('').attr('disabled','disabled');
			$('#non_premium_sku').val('N/A');
			$('#premium_sku_ind').show();
			$('#premium_sku_txt').show();
			$('#premium_sku').hide();
			$('#buy').removeAttr('readonly');
			buy = $('#buy').val() || 0;
			$('#participating_sku').find(' tbody tr').each(function () {
		        var row = $(this);
		        var chekcbox = $(this).find('input:checked');
		        if (chekcbox.is(':checked')){
			        row.closest("tr").find("input.qty").attr('disabled','disabled').val('1');
			       
			        var cost = accounting.unformat(row.find('td:eq(4)').text()) || 0;

			        var pr = 0;
			        if(uom == 'PIECES'){
			        	pr = buy * cost;
			        }
			        if(uom == 'DOZENS'){
			        	pr = buy * cost * 12;
			        }
			        if(uom == 'CASES'){
			        	pr = buy * cost * accounting.unformat(row.find('td:eq(5)').text()) || 0;
			        }
			        row.find('td:eq(9)').text(accounting.formatNumber(pr,2) || 0);
			    }else{
			    	row.find('td:eq(9)').text(accounting.formatNumber(0.00,2) || 0);
			    }
	    	});

		}else if(individual == 2){
			$('.individual').hide();
			$('.collective').show();
			$('#premium_sku_txt').hide();
			$('#premium_sku').show();
			$('#premium_sku').removeAttr('disabled');

			$('#buy').attr('readonly','readonly');

			$('#non_premium_sku').val($('#pre').val());
			$('#premium_sku_ind').hide();

			var total_cost = 0;
			var total_buy = 0;

			$('#participating_sku').find(' tbody tr').each(function () {
				var row = $(this);
				row.find('td:eq(13)').text(accounting.formatNumber(0.00,2) || 0);
		        var chekcbox = $(this).find('input:checked');
		        if (chekcbox.is(':checked')){
		        	row.closest("tr").find("input.qty").removeAttr('disabled');
		        	var qty = accounting.unformat(row.closest("tr").find("input.qty").val());
		        	var cost = accounting.unformat(row.find('td:eq(4)').text()) || 0;
		        	var row_cost = 0;
		        	if(uom == 'PIECES'){
			        	row_cost = qty * cost;
			        }
			        if(uom == 'DOZENS'){
			        	row_cost = qty * cost * 12;
			        }
			        if(uom == 'CASES'){
			        	row_cost = qty * cost * accounting.unformat(row.find('td:eq(5)').text()) || 0;
			        }
			        row.find('td:eq(13)').text(accounting.formatNumber(row_cost,2) || 0);
		        	total_cost = total_cost + row_cost;  
		        	addPremium(chekcbox.val());
		        	total_buy = total_buy + qty;
		        }else{
		        	row.closest("tr").find("input.qty").attr('disabled','disabled');
		        	removePremium( $(this).find("input[type='checkbox']").val());
		        } 	


	    	});
	    	pr = total_cost;
	        $('#p_req').val(accounting.formatNumber(pr,2) || 0);
	        $('#buy').val(total_buy);

		}else if(individual == 3){
			$('.individual').hide();
			$('.collective').show();
			$('#premium_sku_txt').hide();
			$('#premium_sku').show();
			$('#premium_sku').removeAttr('disabled');

			$('#buy').removeAttr('readonly');
			buy = $('#buy').val() || 0;

			$('#non_premium_sku').val($('#pre').val());
			$('#premium_sku_ind').hide();

			var total_cost = 0;
			var total_buy = 1;

			$('#participating_sku').find(' tbody tr').each(function () {
				var row = $(this);
				row.find('td:eq(13)').text(accounting.formatNumber(0.00,2) || 0);
		        var chekcbox = $(this).find('input:checked');
		        if (chekcbox.is(':checked')){
		        	row.closest("tr").find("input.qty").attr('disabled','disabled').val('1');
		        	var cost = accounting.unformat(row.find('td:eq(4)').text()) || 0;
		        	var row_cost = 0;
		        	if(uom == 'PIECES'){
			        	row_cost = buy * cost;
			        }
			        if(uom == 'DOZENS'){
			        	row_cost = buy * cost * 12;
			        }
			        if(uom == 'CASES'){
			        	row_cost = buy * cost * accounting.unformat(row.find('td:eq(5)').text()) || 0;
			        }
			        row.find('td:eq(13)').text(accounting.formatNumber(row_cost,2) || 0);
			        if(total_cost == 0){
			        	total_cost = row_cost;
			        }else{
			        	if(total_cost > row_cost){
				        	total_cost = row_cost;
				        }
			        }

		        	addPremium(chekcbox.val());
		        }else{
		        	row.closest("tr").find("input.qty").attr('disabled','disabled');
		        	removePremium( $(this).find("input[type='checkbox']").val());
		        } 	


	    	});
	    	pr = total_cost;
	        $('#p_req').val(accounting.formatNumber(pr,2) || 0);
		}

		
	}

	function addPremium(id){
		$.ajax({
	        async: false,
	        type: "GET",
	        url: hostname + '/activity/'+id+'/partsku',
	        contentType: "application/json; charset=utf-8",
	        dataType: "json",
	        success: function (data) { 
	        	var t = $('#premium_sku').find('option[value='+id+']').length > 0;
	        	if(t == false){
	        		// console.log(id+ '->' +$('#pre_id').val());
	        		if(id == $('#pre_id').val()){
		        		$('#premium_sku').append($('<option>', {
						    value: data.id,
						    text: data.pre_desc + " - " + data.pre_code,
						    selected: 'selected'
						}));
	        		}else{
		        		$('#premium_sku').append($('<option>', {
						    value: data.id,
						    text: data.pre_desc + " - " + data.pre_code
						}));
	        		}
	        		
	        	}
	        	
	        },
	        error: function (msg) { }
	    });
	}
	function removePremium(id){
		$("#premium_sku option[value='"+id+"']").remove();
        var length = $('#premium_sku > option').length;
        if(length == '0'){
        	$('#premium_sku').val(0);
        }
	}

	

	$("#createtradedealscheme").validate({
		errorElement: "span", 
		errorClass : "has-error",
		rules: {
			scheme_name: {
				required: true,
			},
			buy: {
				required: true,
			},
			free: {
				required: true,
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

	changeName();

	

	
});
