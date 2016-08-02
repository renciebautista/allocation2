$(document).ready(function() {
	var hostname = 'http://' + $(location).attr('host');
	var activity_id = $('#activity_id').val();

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
                    	return $('#non_ulp_premium').is(':checked')
                	}
                }
			},
			non_ulp_premium_code: {
				required: {
					depends: function(){
                    	return $('#non_ulp_premium').is(':checked')
                	}
                }
			},
			non_ulp_premium_cost: {
				required: {
					depends: function(){
                    	return $('#non_ulp_premium').is(':checked')
                	}
                }
			},
			non_ulp_pcs_case: {
				required: {
					depends: function(){
                    	return $('#non_ulp_premium').is(':checked')
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


	$('#non_ulp_premium').click(function() {
	    var $this = $(this);
	    // $this will contain a reference to the checkbox   
	    if ($this.is(':checked')) {
	    	// the checkbox was checked 
	        $('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').removeAttr('disabled');
	    } else {
	    	// the checkbox was unchecked
	        $('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').val('').attr('disabled','disabled');
	       
	    }
	});

	var non_ulp_premium = $('input[name="non_ulp_premium"]:checked').length > 0;
	if(non_ulp_premium){
		$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').removeAttr('disabled');
	}else{
		$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').val('').attr('disabled','disabled');
	}

	$('#alloc_in_weeks, #non_ulp_premium_cost').inputNumber({ allowDecimals: true, maxDecimalDigits: 2 });
	$('#non_ulp_pcs_case, #free, #buy').inputNumber({ allowDecimals: false});
	$('#coverage').inputNumber({ allowDecimals: false});

	$('#add_sku').on('click', function(e){
		e.preventDefault(); // prevents button from submitting
		$('#addsku').modal('show');
	})

	var non_ulp_premium = $('input[name="non_ulp_premium"]:checked').length > 0;
	$('#addsku').on('shown.bs.modal', function(){
		$("#host_sku").chosen({
			search_contains: true,
			allow_single_deselect: true
		}).change(function() {
		    $.ajax({
		        async: false,
		        type: "GET",
		        url: hostname + '/api/pricelistsku/?code='+$(this).val(),
		        contentType: "application/json; charset=utf-8",
		        dataType: "json",
		        success: function (data) { 
		        	$('#host_cost_pcs').val('');
		        	$('#host_cost_pcs').val(data.price);
		        	$('#host_pcs_case').val('');
		        	$('#host_pcs_case').val(data.pack_size);
		        },
		        error: function (msg) { roles = msg; }
		    });
		});

		$("#ref_sku").chosen({
			search_contains: true,
			allow_single_deselect: true
		});

		if(non_ulp_premium){
			// $('.pre-sku').hide();
		}else{
			// $('.pre-sku').show();
			$("#pre_sku").chosen({
				search_contains: true,
				allow_single_deselect: true
			}).change(function() {
			    $.ajax({
			        async: false,
			        type: "GET",
			        url: hostname + '/api/pricelistsku/?code='+$(this).val(),
			        contentType: "application/json; charset=utf-8",
			        dataType: "json",
			        success: function (data) { 
			        	$('#pre_cost_pcs').val('');
			        	$('#pre_cost_pcs').val(data.price);
			        	$('#pre_pcs_case').val('');
			        	$('#pre_pcs_case').val(data.pack_size);
			        },
			        error: function (msg) { roles = msg; }
			    });
			});
		}

	}).on('hide.bs.modal', function(){
		$("#host_sku, #ref_sku ").val('').trigger("chosen:updated");
		$('#host_cost_pcs').val('');
		$('#host_pcs_case').val('');
		$('#addpartskus .error-msg').text('');
		if(non_ulp_premium){

		}else{
			$('#pre_cost_pcs').val('');
			$('#pre_pcs_case').val('');
			
		}
		
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
	                //if success reload ajax table
	                $('#modal_form').modal('hide');
	                reload_table();
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
	});

	function reload_table(){
	    table.ajax.reload(null,false); //reload datatable ajax 
	}

	$("form[id='addpartskus']").on("submit",function(e){
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
		e.preventDefault();
	});

	// channels

	$(document).on("click","#add-scheme", function (e) {
		e.preventDefault(); // prevents button from submitting
	    $('#addScheme').modal('show');
	    
	});

	


	$('#deal_type').change(function(e) {
	  	// console.log(this.value);
	  	$.ajax({
	        async: false,
	        type: "GET",
	        url: hostname + '/activity/'+activity_id+'/getpartskus',
	        contentType: "application/json; charset=utf-8",
	        dataType: "json",
	        success: function (data) { 
	        	$("#channel_skus > tbody").html("");
	        	$('#collective').hide();
	        	var selected = $('#deal_type').val();

	        	var option = '<select name="option[%i]"">%s</select>';
		        var option_value = '';

	        	if(selected == 1){

		        	$.each(data.uom, function(key, value) {
					   option_value = option_value+'<option  value="'+key+'">'+value+'</option>';
					})
					option = option.replace(/%s/g, option_value);
			        $.each(data.skus, function(key, value) {
					    var newRowContent = '<tr><td><input class="sku-checkbox"name="select[]" value="'+value.id+'" type="checkbox"></td>';
					    newRowContent = newRowContent + '<td>'+value.host_sku+'</td>';
					    newRowContent = newRowContent + '<td>'+value.pre_sku+'</td></tr>';
					    $("#channel_skus tbody").append(newRowContent);
					});

					$('#pre_details').hide();

	        	}else{
	        		$.each(data.uom, function(key, value) {
					   option_value = option_value+'<option  value="'+key+'">'+value+'</option>';
					})
					option = option.replace(/%s/g, option_value);
			        $.each(data.skus, function(key, value) {
					    var newRowContent = '<tr><td><input class="sku-checkbox"name="select[]" value="'+value.id+'" type="checkbox"></td>';
					    newRowContent = newRowContent + '<td>'+value.host_sku+'</td>';
					    newRowContent = newRowContent + '<td>NOT APPLICABLE</td></tr>';
					    $("#channel_skus tbody").append(newRowContent);
					});

			        $('#pre_details').show();
	        	}

	        	$('#scheme-table').show();

	        	$(document).on("click",".sku-checkbox", function () {
				    var $this = $(this);
				    // $this will contain a reference to the checkbox   
				    if ($this.is(':checked')) {
				    	console.log($(this).val());
				    	$.ajax({
					        async: false,
					        type: "GET",
					        url: hostname + '/activity/'+$(this).val()+'/partsku',
					        contentType: "application/json; charset=utf-8",
					        dataType: "json",
					        success: function (data) { 
					        	console.log(data);
					        	$('#premium_sku').append($('<option>', {
								    value: data.id,
								    text: data.pre_desc + " - " + data.pre_code
								}));
					        },
					        error: function (msg) { }
					    });

				    } else {		
				        $("#premium_sku option[value='"+$(this).val()+"']").remove();	       
				    }
				});

	        },
	        error: function (msg) { roles = msg; }
	    });
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


	$("form[id='updateTradedeal']").on("submit",function(e){
		var form = $(this);
		var url = form.prop('action');
		if(form.valid()){
			$.ajax({
				url: url,
				data: form.serialize(),
				method: 'POST',
				dataType: "json",
				success: function(data){
					if(data.success == "1"){
						location.reload();
					}else{
						bootbox.alert("An error occured while updating."); 
					}
				}
			});
		}
		
		e.preventDefault();
	});
});
