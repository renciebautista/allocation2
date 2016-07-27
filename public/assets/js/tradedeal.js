$(document).ready(function() {
	var hostname = 'http://' + $(location).attr('host');
	var activity_id = $('#activity_id').val();

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

	$('#alloc_in_weeks, #coverage, #non_ulp_premium_cost').inputNumber({ allowDecimals: true, maxDecimalDigits: 2 });
	$('#non_ulp_pcs_case, #c_free').inputNumber({ allowDecimals: false});

	$('#add_sku').on('click', function(e){
		e.preventDefault(); // prevents button from submitting
		$('#addsku').modal('show');
	})

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

		var non_ulp_premium = $('input[name="non_ulp_premium"]:checked').length > 0;
		if(non_ulp_premium){
			$('.pre-sku').hide();
		}else{
			$('.pre-sku').show();
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
		$("#host_sku, #ref_sku, #pre_sku").val('').trigger("chosen:updated");
		$('#host_cost_pcs').val('');
		$('#host_pcs_case').val('');
		$('#pre_cost_pcs').val('');
		$('#pre_pcs_case').val('');
		$('#addpartskus .error-msg').text('');
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
					console.log();
				}
			}
		});
		e.preventDefault();
	});

	// channels

	var dtchtable = $('#td-channels').DataTable({
		'ajax': {
	         'url': hostname + '/activity/'+activity_id+'/tdchannels' 
	      },
	   'columnDefs': [
	      {
	         'targets': 0,
	         'checkboxes': {
	            'selectRow': true
	         }
	      }
	   ],
	   'select': {
	      'style': 'multi'
	   },
	   'order': [[1, 'asc']]
	});

	$(document).on("click","#eChannel", function (e) {
		e.preventDefault(); // prevents button from submitting
	    var rows_selected = dtchtable.column(0).checkboxes.selected();
	    // Iterate over all selected checkboxes 
	    var chId = new Array();
	    $.each(rows_selected, function(index, rowId){
	       	chId.push(rowId);
	    });

	    if(chId.length > 0){
	    	$('#editChannel').modal('show');
	    	$('#ch_id').val(chId);
	    }else{
	    	bootbox.alert("No channel selected, select a channel to edit."); 
	    }
	    
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

	        	if(selected == 1){
	        		var option = '<select name="option[%i]"">%s</select>';
		        	var option_value = '';
		        	$.each(data.uom, function(key, value) {
					   option_value = option_value+'<option  value="'+key+'">'+value+'</option>';
					})
					option = option.replace(/%s/g, option_value);
			        $.each(data.skus, function(key, value) {
					    var newRowContent = '<tr><td><input name="select[]" value="'+value.id+'" type="checkbox"></td>';
					    newRowContent = newRowContent + '<td>'+value.host_sku+'</td>';
					    newRowContent = newRowContent + '<td><input name="buy['+value.id+']"  type="text" class="num-int"></input></td>';
					    newRowContent = newRowContent + '<td><input name="free['+value.id+']" type="text" class="num-int"></input></td>';
					    newRowContent = newRowContent + '<td>'+option.replace(/%i/g,value.id)+'</td></tr>';
					    $("#channel_skus tbody").append(newRowContent);
					})

			        $('.num-int').inputNumber({ allowDecimals: false});
	        	}else{
	        		var option = '<select name="option[%i]"">%s</select>';
		        	var option_value = '';
		        	$.each(data.uom, function(key, value) {
					   option_value = option_value+'<option  value="'+key+'">'+value+'</option>';
					})
					option = option.replace(/%s/g, option_value);
			        $.each(data.skus, function(key, value) {
					    var newRowContent = '<tr><td><input name="select[]" value="'+value.id+'" type="checkbox"></td>';
					    newRowContent = newRowContent + '<td>'+value.host_sku+'</td>';
					    newRowContent = newRowContent + '<td><input name="buy['+value.id+']"  type="text" class="num-int"></input></td>';
					    newRowContent = newRowContent + '<td>NA</td>';
					    newRowContent = newRowContent + '<td>NA</td></tr>';
					    $("#channel_skus tbody").append(newRowContent);
					})

			        $('.num-int').inputNumber({ allowDecimals: false});
			        $('#collective').show();
	        	}
	        	

	        },
	        error: function (msg) { roles = msg; }
	    });
	});

	$('#editChannel').on('shown.bs.modal', function(){
		$("#channel_skus > tbody").html("");
		$('#collective').hide();
		// <table id="channel_skus" class="table table-striped table-hover ">
		// 				<thead>
		// 					<tr>
		// 						<th><input name="select_all" value="1" type="checkbox"></th>
		// 						<th>Host SKU</th>
		// 						<th>Buy</th>
		// 						<th>Free</th>
		// 						<th>UOM</th>
		// 					</tr>
		// 				</thead>
		// 				<tbody>
		// 					<tr>
		// 						<td></td>
		// 						<td></td>
		// 						<td></td>
		// 						<td></td>
		// 						<td></td>
		// 					</tr>
		// 				</tbody>
		// 			</table> 
		// $.ajax({
	 //        async: false,
	 //        type: "GET",
	 //        url: hostname + '/activity/'+activity_id+'/getpartskus',
	 //        contentType: "application/json; charset=utf-8",
	 //        dataType: "json",
	 //        success: function (data) { 
	 //        	var option = '<select name="option[%i]"">%s</select>';
	 //        	var option_value = '';
	 //        	$.each(data.uom, function(key, value) {
		// 		   option_value = option_value+'<option  value="'+key+'">'+value+'</option>';
		// 		})
		// 		option = option.replace(/%s/g, option_value);
		//         $.each(data.skus, function(key, value) {
		// 		    var newRowContent = '<tr><td><input name="select[]" value="'+value.id+'" type="checkbox"></td>';
		// 		    newRowContent = newRowContent + '<td>'+value.host_sku+'</td>';
		// 		    newRowContent = newRowContent + '<td><input name="buy['+value.id+']"  type="text" class="num-int"></input></td>';
		// 		    newRowContent = newRowContent + '<td><input name="free['+value.id+']" type="text" class="num-int"></input></td>';
		// 		    newRowContent = newRowContent + '<td>'+option.replace(/%i/g,value.id)+'</td></tr>';
		// 		    $("#channel_skus tbody").append(newRowContent);
		// 		})

		//         $('.num-int').inputNumber({ allowDecimals: false});

	 //        },
	 //        error: function (msg) { roles = msg; }
	 //    });
	}).on('hide.bs.modal', function(){
		$("#deal_type").val('').trigger("chosen:updated");
		// $("#channel_skus > tbody").html("");
	});


	function reload_dtchtable()
	{
	    dtchtable.ajax.reload(null,false); //reload datatable ajax 
	    dtchtable.column(0).checkboxes.deselect();
	}

	$("form[id='updatedtchannel']").on("submit",function(e){
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
					$('#editChannel').modal('hide');
					reload_dtchtable();
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
		e.preventDefault();
	});

	// $("form[id='editpartsku']").on("submit",function(e){
	// 	var form = $(this);
	// 	var method = form.find('input[name="_method"]').val() || 'POST';
	// 	var url = form.prop('action');
	// 	$.ajax({
	// 		url: url,
	// 		data: form.serialize(),
	// 		method: "POST",
	// 		dataType: "json",
	// 		success: function(data){
	// 			if(data.success == "1"){
	// 				bootbox.alert("Participating variants was successfully added."); 
	// 				$('#editSku').modal('hide');
	// 				reload_table();

	// 			}else{
	// 				bootbox.alert("An error occured while updating."); 
	// 			}
	// 		}
	// 	});
	// 	e.preventDefault();
	// });

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
						//bootbox.alert("Trade deal was successfully updated."); 
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
