$(document).ready(function() {
	var hostname = 'http://' + $(location).attr('host');
	var activity_id = $('#activity_id').val();

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
	    } else {
	        checkboxes.prop('checked', false);
	    }

	    getcheckedhost();
	});

	$(document).on("click",".sku-checkbox", function () {
	    getcheckedhost();
	    changeName();
	});

	$('#deal_type, #uom').change(function() {
		changeName();
	});
	$('#buy').on('blur',function() {
		changeName();
	})

	

	function changeName(){
		var type = $("#deal_type option:selected").text();
		var buy = $('#buy').val();
		var free = $('#free').val();
		var uom = $("#uom option:selected").text();
		$('#scheme_name').val(type + " : " + buy +"+"+free+" "+uom);

		var individual = $("#deal_type option:selected").val();
		if(individual == 1){
			$('.collective').hide();
			$('.individual').show();
			$('#p_req').val('').attr('disabled','disabled');
			$('#premium_sku').val('').attr('disabled','disabled');
			$('#non_premium_sku').val('N/A');

			$('#participating_sku').find(' tbody tr').each(function () {
		        var row = $(this);
		        var cost = accounting.unformat(row.find('td:eq(2)').text()) || 0;
		        var pr = 0;
		        if(uom == 'PIECES'){
		        	pr = buy * cost;
		        }
		        if(uom == 'DOZENS'){
		        	pr = buy * cost * 12;
		        }
		        if(uom == 'CASES'){
		        	pr = buy * cost * accounting.unformat(row.find('td:eq(3)').text()) || 0;
		        }
		        row.find('td:eq(5)').text(accounting.formatNumber(pr,2) || 0);
	    	});

		}else{
			$('.individual').hide();
			$('.collective').show();
			$('#p_req').removeAttr('disabled');
			$('#premium_sku').removeAttr('disabled');
			$('#non_premium_sku').val($('#participating_sku').find(' tbody tr td:eq(4)').text());

			var total_cost = 0

			$('#participating_sku').find(' tbody tr').each(function () {
				var row = $(this);
		        var chekcbox = $(this).find('input:checked');
		        if (chekcbox.is(':checked')){
		        	var cost = accounting.unformat(row.find('td:eq(2)').text()) || 0;
		        	total_cost = total_cost + cost;  
		        }  	

	    	});

	    	var pr = 0;
	        if(uom == 'PIECES'){
	        	pr = buy * total_cost;
	        }
	        if(uom == 'DOZENS'){
	        	pr = buy * total_cost * 12;
	        }
	        if(uom == 'CASES'){
	        	// pr = buy * total_cost * accounting.unformat(row.find('td:eq(3)').text()) || 0;
	        }

	        $('#p_req').val(accounting.formatNumber(pr,2) || 0);

		}

		
	}


	function getcheckedhost(){
		$('#premium_sku').empty();
		$('#participating_sku').find(' tbody tr').each(function () {
	        var row = $(this);
	        var chekcbox = $(this).find('input:checked');
	        if (chekcbox.is(':checked')){
	        	var id = chekcbox.val();
	        	var desc = row.find('td:eq(1)').text();
	        	$('#premium_sku').append($('<option>', {
				    value: id,
				    text: desc
				}));   
	        }  
    	});
	}



	changeName();

	
});
