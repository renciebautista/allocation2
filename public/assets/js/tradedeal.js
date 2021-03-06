$(document).ready(function() {
	var hostname = 'http://' + $(location).attr('host');
	var activity_id = $('#activity_id').val();

	// $('#add_scheme').hide();
	
	// showAddScheme();

	function showAddScheme(){
		var rowCount = $('#participating_sku >tbody >tr >td.sorting_1').length;
		if(rowCount > 0){
			$('#add_scheme').show();
		}else{
			$('#add_scheme').hide();
		}
	}

	$('#copy_host').click(function(){
		$('#pre_sku').val($("#host_sku").chosen().val()).trigger('chosen:updated');
		$('#pre_cost_pcs').val($('#host_cost_pcs').val());
		$('#pre_pcs_case').val($('#host_pcs_case').val());
		$('#pre_variant').val($('#variant').val());
		$('#pre_variant').removeAttr('readonly');
		if ($('#variant').is('[readonly]')) 
		{
			$('#pre_variant').attr('readonly', '');
		}
	});

	$('#ecopy_host').click(function(){
		$('#epre_sku').val($("#ehost_sku").chosen().val()).trigger('chosen:updated');
		$('#epre_cost_pcs').val($('#ehost_cost_pcs').val());
		$('#epre_pcs_case').val($('#ehost_pcs_case').val());
		$('#epre_variant').val($('#evariant').val());
	});

	$('#host_cost_pcs, #ehost_cost_pcs').inputNumber();

	$('.cost-edit').click(function(){
		$('#host_cost_pcs').removeAttr('readonly').focus();
	})

	$('.ecost-edit').click(function(){
		$('#ehost_cost_pcs').removeAttr('readonly').focus();
	})

	$('.var-edit').click(function(){
		$('#evariant').removeAttr('readonly').focus();
	})

	$('.prevar-edit').click(function(){
		$('#epre_variant').removeAttr('readonly').focus();
	})

	$("#updateTradedeal").validate({
		errorElement: "span", 
		errorClass : "has-error",
		rules: {
			alloc_in_weeks: {
				required: true,
			},
			non_ulp_premium_desc: {
				required: {
					depends: function(){
                    	return $("input[name='non_ulp_premium']:checked").val()
                	}
                },
                maxlength: 13
			},
			non_ulp_premium_code: {
				required: {
					depends: function(){
                    	return $("input[name='non_ulp_premium']:checked").val()
                	}
                }
			},
			non_ulp_premium_cost: {
				required: {
					depends: function(){
                    	return $("input[name='non_ulp_premium']:checked").val()
                	}
                }
			},
			non_ulp_pcs_case: {
				required: {
					depends: function(){
                    	return $("input[name='non_ulp_premium']:checked").val()
                	}
                }
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


	$('.radio').click(function() {
		// alert($("input[name='non_ulp_premium']:checked").val()); 
		var non_premiun = $("input[name='non_ulp_premium']:checked").val();
	    var rowCount = $('#participating_sku >tbody >tr >td.sorting_1').length;

	    // // $this will contain a reference to the checkbox   
	    if (non_premiun == '1') {
	    	if(rowCount > 0){
	    		alert('There is an existing Non ULP Premuim on the participating sku, please remove it.');
		    	return false;
		    }else{
		    	$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').removeAttr('disabled');
		    }
	    } else {
	    	if(rowCount > 0){
	    		alert('There is an existing ULP Premuim on the participating sku, please remove it.');
		    	return false;
		    }else{
		    	// the checkbox was unchecked
	        	$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').val('').attr('disabled','disabled');
		    }
	    
	    }

	});

	// var non_ulp_premium = $('input[name="non_ulp_premium"]:checked').length > 0;
	// var non_ulp_premium = $("input[name='non_ulp_premium']:checked").val();

	


	var non_premiun = $("input[name='non_ulp_premium']:checked").val();
	if(non_premiun == '1'){
		$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').removeAttr('disabled');
	}else{
		$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').val('').attr('disabled','disabled');
	}

	$('#alloc_in_weeks, #non_ulp_premium_cost').inputNumber({ allowDecimals: true, maxDecimalDigits: 2 });
	$('#non_ulp_pcs_case, #free, #buy').inputNumber({ allowDecimals: false});
	$('#coverage').inputNumber({ allowDecimals: false});

	$('#add_sku').on('click', function(e){
		e.preventDefault(); 
		var count = $("#skus :selected").length;
		if(count > 0){
			$('#addsku').modal('show');
		}else{
			alert('No SKU Involved selected on Activity Details.');
		}
		
	})

	// var non_ulp_premium = $('input[name="non_ulp_premium"]:checked').length > 0;
	$('#addsku').on('shown.bs.modal', function(){

		var non_premiun = $("input[name='non_ulp_premium']:checked").val();
		$('#host_cost_pcs').attr('readonly', '');
		$("#host_sku").chosen({
			search_contains: true,
			allow_single_deselect: true
		}).change(function() {
		    $.ajax({
		        async: false,
		        type: "GET",
		        url: hostname + '/api/tdpricelistsku/?code='+$(this).val()+'&ac_id='+activity_id,
		        contentType: "application/json; charset=utf-8",
		        dataType: "json",
		        success: function (data) {
		        	$('#host_cost_pcs').val('');
		        	$('#host_pcs_case').val('');
		        	
		        	if(data.sku !== null){
		        		var price = data.sku.price_case/data.sku.pack_size;
		        		$('#host_cost_pcs').val(accounting.formatNumber(price, 2, ",","."));
		        		$('#host_pcs_case').val(data.sku.pack_size);
		        	}else{
		        		$('#variant').val('').removeAttr('readonly');
		        	}
		        	
		        	if(data.variant != ''){
		        		$('#variant').val(data.variant).attr('readonly', '');
		        	}
		        },
		        error: function (msg) { roles = msg; }
		    });
		});

		$("#ref_sku").chosen({
			search_contains: true,
			allow_single_deselect: true
		});

		if(non_premiun == '1'){
			$('.non_ulppremium').show();
			$('.ulppremium').show();
		}else{
			$('.ulppremium').show();
			$('.non_ulppremium').hide();
			$("#pre_sku").chosen({
				search_contains: true,
				allow_single_deselect: true
			}).change(function() {
			    $.ajax({
			        async: false,
			        type: "GET",
			        url: hostname + '/api/tdprepricelistsku/?code='+$(this).val()+'&ac_id='+activity_id,
			        contentType: "application/json; charset=utf-8",
			        dataType: "json",
			        success: function (data) { 
			        	$('#epre_cost_pcs').val('');
			        	$('#epre_pcs_case').val('');
			        	
			        	if(data.sku !== null){
			        		var price = data.sku.price_case/data.sku.pack_size;
		        			$('#pre_cost_pcs').val(accounting.formatNumber(price, 2, ",","."));
			        		$('#pre_pcs_case').val(data.sku.pack_size);
			        	}else{
			        		$('#pre_variant').val('').removeAttr('readonly');
			        	}
			        	
			        	if(data.variant != ''){
			        		$('#pre_variant').val(data.variant).attr('readonly', '');
			        	}
			        },
			        error: function (msg) { roles = msg; }
			    });
			});
		}

	}).on('hide.bs.modal', function(){
		$("#host_sku, #ref_sku, #pre_sku").val('').trigger("chosen:updated");
		$('#host_cost_pcs').val('');
		$('#host_pcs_case').val('');
		$('#addpartskus .error-msg').text('');
		$('#variant').val('');
		$('#pre_variant').val('');
		$('.ulppremium, .non_ulppremium').hide();
		$('#variant').val('').removeAttr('readonly');
		if(non_premiun == '1'){
		}else{
			$('#pre_cost_pcs').val('');
			$('#pre_pcs_case').val('');
		}
	});

	$('#editsku').on('shown.bs.modal', function(){
		$("#ehost_sku").chosen({
			search_contains: true,
			allow_single_deselect: true
		}).change(function() {
		    $.ajax({
		        async: false,
		        type: "GET",
		        url: hostname + '/api/tdpricelistsku/?code='+$(this).val()+'&ac_id='+activity_id,
		        contentType: "application/json; charset=utf-8",
		        dataType: "json",
		        success: function (data) { 
		        	$('#ehost_cost_pcs').val('');
		        	$('#ehost_pcs_case').val('');
		        	
		        	if(data.sku !== null){
		        		var price = data.sku.price_case/data.sku.pack_size;
		        		$('#ehost_cost_pcs').val(accounting.formatNumber(price, 2, ",","."));
		        		// $('#ehost_cost_pcs').val(data.sku.price);
		        		$('#ehost_pcs_case').val(data.sku.pack_size);
		        	}else{
		        		$('#evariant').val('').removeAttr('readonly');
		        	}
		        	if(data.variant != ''){
		        		$('#evariant').val(data.variant).attr('readonly', '');
		        	}else{
		        		$('#evariant').val('').removeAttr('readonly');
		        	}
		        },
		        error: function (msg) { roles = msg; }
		    });
		});

		$("#eref_sku").chosen({
			search_contains: true,
			allow_single_deselect: true
		});

		if(non_premiun == '1'){
			$('.non_ulppremium').show();
			$('.ulppremium').show();
		}else{
			$('.ulppremium').show();
			$('.non_ulppremium').hide();
			$("#epre_sku").chosen({
				search_contains: true,
				allow_single_deselect: true
			}).change(function() {
			    $.ajax({
			        async: false,
			        type: "GET",
			        url: hostname + '/api/tdprepricelistsku/?code='+$(this).val()+'&ac_id='+activity_id,
			        contentType: "application/json; charset=utf-8",
			        dataType: "json",
			        success: function (data) { 
			        	$('#epre_cost_pcs').val('');
			        	$('#epre_pcs_case').val('');
			        	if(data.sku !== null){
			        		var price = data.sku.price_case/data.sku.pack_size;
		        			$('#epre_cost_pcs').val(accounting.formatNumber(price, 2, ",","."));
			        		// $('#epre_cost_pcs').val(data.sku.price);
			        		$('#epre_pcs_case').val(data.sku.pack_size);
			        	}else{
			        		$('#epre_variant').val('').removeAttr('readonly');
			        	}
			        	
			        	if(data.variant != ''){
			        		$('#epre_variant').val(data.variant).attr('readonly', '');
			        	}else{
			        		$('#epre_variant').val('').removeAttr('readonly');
			        	}
			        },
			        error: function (msg) { roles = msg; }
			    });
			});
		}



	}).on('hide.bs.modal', function(){
		$("#ehost_sku, #eref_sku, #epre_sku").val('').trigger("chosen:updated");
		$('#ehost_cost_pcs').val('');
		$('#ehost_pcs_case').val('');
		$('#updatepartskus .error-msg').text('');
		$('#evariant').val('');
		$('#epre_variant').val('');
		$('.ulppremium, .non_ulppremium').hide();
		$('#epre_variant').val('').attr('readonly', '');
		$('#evariant').val('').attr('readonly', '');
		$('#epre_variant').val('').attr('readonly', '');
		$('#ehost_cost_pcs').val('').attr('readonly', '');
		if(non_premiun == '1'){
		}else{
			$('#epre_cost_pcs').val('');
			$('#epre_pcs_case').val('');
		}
	});


	$("form[id='updatepartskus']").on("submit",function(e){
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
					$('#editsku').modal('hide');
					reload_table();
				}else{
					var obj = data.err_msg,  
			        ul = '<ul>';         
			        var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>%s</div>'; 
			        for (var i = 0, l = obj.length; i < l; ++i) {
			        	ul = ul + "<li>" + obj[i] + "</li>";
			            
			        }
			        ul = ul + '</ul>'; 
					$('#updatepartskus .error-msg').text('').append(html.replace(/%s/g, ul));;
				}
			}
		});
		e.preventDefault();
	});

	$(document).on("click",".editsku", function (e) {
	    var id = $(this).attr('id');
        // ajax delete data to database
        $.ajax({
            url : hostname + '/activity/'+ id+'/partsku',
            type: "GET",
            dataType: "JSON",
            async: false,
            success: function(data)
            {
               	$('#editsku').modal('show');
               	$("#ehost_sku").val(data.host_code).trigger("chosen:updated");
		       	$('#ehost_cost_pcs').val(data.host_cost);
		        $('#ehost_pcs_case').val(data.host_pcs_case);
		        $('#evariant').val(data.variant);
		        $("#eref_sku").val(data.ref_code).trigger("chosen:updated");
		        $("#sku_id").val(data.id);

		        $("#epre_sku").val(data.pre_code).trigger("chosen:updated");
		       	$('#epre_cost_pcs').val(data.pre_cost);
		        $('#epre_pcs_case').val(data.pre_pcs_case);
		        $('#epre_variant').val(data.pre_variant);

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error retrieving data');
            }
        });
	 
	    
	});

	
	$(document).on("click",".deletesku", function (e) {
	    var id = $(this).attr('id');
	    if(confirm('Are you sure delete this data?'))
	    {
	        // ajax delete data to database
	        $.ajax({
	            url : hostname + '/activity/deletepartskus',
	            type: "POST",
	            data: { 
			        'd_id': id
			    },
	            dataType: "JSON",
	            success: function(data)
	            {
	            	if(data.success == 1){
	            		reload_table();
	            	}else{
	            		alert('Cannot delete sku, it is used in a scheme.');
	            	}	                
	            },
	            error: function (jqXHR, textStatus, errorThrown)
	            {
	                alert('Error deleting data');
	            }
	        });
	 
	    }
	});

	$(document).on("click",".deletescheme", function (e) {
		var del = $(this);
	    var id = $(this).attr('id');
	    if(confirm('Are you sure delete this data?'))
	    {
	        $.ajax({
	            url : hostname + '/activity/deletetradedealscheme',
	            type: "POST",
	            data: { 
			        'd_id': id
			    },
	            dataType: "JSON",
	            success: function(data)
	            {
	               	$('#scheme-table tr.cl_'+id).remove();
	            },
	            error: function (jqXHR, textStatus, errorThrown)
	            {
	                alert('Error deleting data');
	            }
	        });
	 
	    }
	});



	var table = $("#participating_sku").DataTable({
		"processing": true, //Feature control the processing indicator.
	    "serverSide": true, //Feature control DataTables' server-side processing mode.
		"scrollCollapse": true,
		"searching": false,
		"paging": false,
		"bSort": true,
		"ajax": hostname + '/activity/'+activity_id+'/getpartskustable',
		"columnDefs": [ { //this prevents errors if the data is null
			"targets": "_all",
			"defaultContent": ""
		} ]
	}).on( 'draw.dt', function () {
	    showAddScheme();
	} );

	function reload_table(){
		location.reload();
	    // table.ajax.reload(null,false); //reload datatable ajax 
	}

	var submitted = false;

	$("form[id='addpartskus']").on("submit",function(e){
		if(!submitted){
			submitted = true;
			var form = $(this);
			var method = form.find('input[name="_method"]').val() || 'POST';
			var url = form.prop('action');
			$.ajax({
				url: url,
				data: form.serialize(),
				method: "POST",
				dataType: "json",
				success: function(data){
					submitted = false;
					if(data.success == "1"){
						$('#addsku').modal('hide');
						reload_table();
						
					}else{
						var obj = data.err_msg,  
				        ul = '<ul>';         
				        var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>%s</div>'; 
				        for (var i = 0, l = obj.length; i < l; ++i) {
				        	ul = ul + "<li>" + obj[i] + "</li>";
				            
				        }
				        ul = ul + '</ul>'; 
						$('#addpartskus .error-msg').text('').append(html.replace(/%s/g, ul));;
					}
				}
			});

		}
		
		e.preventDefault();
	});

	// channels

	// $(document).on("click","#add-scheme", function (e) {
	// 	e.preventDefault(); // prevents button from submitting
	// 	$('#deal_type').val(0);
	// 	$('#uom').val(1);
	// 	$('#coverage').val(100);
	// 	$("#channel_skus > tbody").html("");
	// 	$('.buy-free').hide();
	// 	$('.premium').hide();
	//     $('#addScheme').modal('show');
	    
	// });

	// $('#addScheme').on('shown.bs.modal', function(){
	// 	$("#premium_sku").empty();
	// });

	function selected(){
		return $('#deal_type').val();
	}
	


	$('#deal_type').change(function(e) {
	  	// console.log(this.value);
	  	$('#premium_sku').empty();
	  	$('#select-all').prop('checked', false);
	  	$.ajax({
	        async: false,
	        type: "GET",
	        url: hostname + '/activity/'+activity_id+'/getpartskus',
	        contentType: "application/json; charset=utf-8",
	        dataType: "json",
	        success: function (data) { 

	        	$("#channel_skus > tbody").html("");
	        	$('#collective').hide();
	        	$('.premium').hide();
	        	// var selected = $('#deal_type').val();

	        	var option = '<select name="option[%i]"">%s</select>';
		        var option_value = '';
		        $('.buy-free').show();

	        	if(selected() == 1){

		        	$.each(data.uom, function(key, value) {
					   option_value = option_value+'<option  value="'+key+'">'+value+'</option>';
					})
					option = option.replace(/%s/g, option_value);
			        $.each(data.skus, function(key, value) {
					    var newRowContent = '<tr><td><input class="sku-checkbox"name="select[]" value="'+value.id+'" type="checkbox"></td>';
					    newRowContent = newRowContent + '<td><input name="qty['+value.id+']" style="width:40px" value="1" type="text" disabled="disabled"></td>';
					    newRowContent = newRowContent + '<td>'+value.host_sku+'</td>';
					    newRowContent = newRowContent + '<td>'+value.pre_sku+'</td></tr>';
					    $("#channel_skus tbody").append(newRowContent);
					});

	

	        	}else{
	        		$.each(data.uom, function(key, value) {
					   option_value = option_value+'<option  value="'+key+'">'+value+'</option>';
					})
					option = option.replace(/%s/g, option_value);
			        $.each(data.skus, function(key, value) {
					    var newRowContent = '<tr><td><input class="sku-checkbox"name="select[]" value="'+value.id+'" type="checkbox"></td>';
					    newRowContent = newRowContent + '<td><input name="qty['+value.id+']" class="qty" style="width:40px" value="" type="text" disabled="disabled"></td>';
					    newRowContent = newRowContent + '<td>'+value.host_sku+'</td>';
					    newRowContent = newRowContent + '<td>NOT APPLICABLE</td></tr>';
					    $("#channel_skus tbody").append(newRowContent);
					});

			        $('.premium').show();
			        
	        	}

	        	$('#scheme-table').show();

	        },
	        error: function (msg) { roles = msg; }
	    });
	});
	
	$('#channel_skus').on('click','.sku-checkbox', function(e){
		// console.log(selected());
	    var $this = $(this);
	    if(selected() == 2){
		    if ($this.is(':checked')) {
		    	$(this).closest("tr").find("input.qty").removeAttr('disabled').val('1');
		    	addPremium($(this).val());
		    } else {	
		    	$(this).closest("tr").find("input.qty").removeAttr('disabled').val('').attr('disabled','disabled');	
		        $("#premium_sku option[value='"+$(this).val()+"']").remove();
		        var length = $('#premium_sku > option').length;
		        if(length == '0'){
		        	$('#premium_sku').val(0);
		        }

		    }
		}
	})

	function addPremium(id){
		$.ajax({
	        async: false,
	        type: "GET",
	        url: hostname + '/activity/'+id+'/partsku',
	        contentType: "application/json; charset=utf-8",
	        dataType: "json",
	        success: function (data) { 
	        	$('#premium_sku').append($('<option>', {
				    value: data.id,
				    text: data.pre_desc + " - " + data.pre_code
				}));
	        },
	        error: function (msg) { }
	    });
	}

	$('#select-all').change(function() {
    	var checkboxes = $(this).closest('table').find(':checkbox');

	    if($(this).is(':checked')) {
	        checkboxes.prop('checked', true);
	        checkboxes.closest("tr").find("input.qty").removeAttr('disabled').val('1');
	        if(selected() == 2){
		        $.each(checkboxes, function(obj){
			    	// console.log($(this).attr('name'));
			    	if($(this).attr('name') == 'select[]'){
			    		addPremium($(this).val());
			    	}
			    })
	    	}
	    } else {
	        checkboxes.prop('checked', false);
	        checkboxes.closest("tr").find("input.qty").removeAttr('disabled').val('').attr('disabled','disabled');
	        $('#premium_sku').empty();
	    }
	    
	});


	$("form[id='addtradealscheme']").on("submit",function(e){
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
					location.reload();
				}else{
					var obj = data.err_msg,  
			        ul = '<ul>';         
			        var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>%s</div>'; 
			        for (var i = 0, l = obj.length; i < l; ++i) {
			        	ul = ul + "<li>" + obj[i] + "</li>";
			            
			        }
			        ul = ul + '</ul>'; 
					$('#addtradealscheme .error-msg').text('').append(html.replace(/%s/g, ul));;
				}
			}
		});
		e.preventDefault();
	});

});
