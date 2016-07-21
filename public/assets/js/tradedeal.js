var hostname = 'http://' + $(location).attr('host');
var activity_id = $('#activity_id').val();

$('#alloc_in_weeks, #coverage, #non_ulp_premium_cost').inputNumber({ allowDecimals: true, maxDecimalDigits: 2 });
$('#host_qty').inputNumber({ allowDecimals: false });
var cnt = 0;
var arr = new Array();

$('#add-host-sku').on('click', function(){
	var host = $('#host_skus').val();
	var qty = $('#host_qty').val();
	if((host == 0) || (qty < 1)){
		var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>%s</div>'; 
		if(host == 0){
			ul = '<ul><li>No selected Host SKU</li></ul>';     
		}else{
			ul = '<ul><li>Host qty must be greater than 0</li></ul>';     
		}
		    
		$("#td-error").text('').append(html.replace(/%s/g, ul));
	}else{
		$.ajax({
	        async: false,
	        type: "GET",
	        url: hostname +"/api/pricelistsku?code="+host,
	        contentType: "application/json; charset=utf-8",
	        dataType: "json",
	        success: function (data) { 
	        	var pur_req = accounting.formatNumber(qty * data.price,2) || 0;
	        	var html = '<tr><th scope="row">%s</th><td>'+data.sap_desc+' - '+data.sap_code+'</td><td class="right">'+data.price+'</td><td class="right">'+data.pack_size+'</td><td class="right">'+qty+'</td><td class="right">'+pur_req+'</td><td ><a href="" id="'+data.sap_code+":"+qty.replace(",", "")+'" class="deletehost">Delete</a></td></tr>';
	        	$("#set_sku tbody").append(html.replace(/%s/g, cnt));
	        	var x = accounting.unformat(pur_req) || 0;
				var y = accounting.unformat($('#total_req').text()) || 0;
	        	arr.push(data.sap_code+":"+qty.replace(",", ""));
	        	purreq_total();
	        	cnt++;	
	        }
	    });
	}
	
});

function purreq_total(){
	var total = 0.00;
	$('#set_sku > tbody > tr > td:nth-child(6) ').each(function(){
		value = accounting.unformat($(this).text()) || 0;
		total = accounting.unformat(total + value,2);;
	});

	$('#total_req').text(accounting.formatNumber(total,2) || 0)
}

$(document).on("click", ".deletehost", function(e) {
	e.preventDefault(); // prevents button from submitting
	if(confirm('Are you sure delete this data?'))
    {
		$(this).closest("tr").remove();
		arr.splice( $.inArray($(this).attr('id'),arr) ,1 );
		purreq_total();
	}
});

$('#addSku').on('shown.bs.modal', function () {
	cnt = 1;
	arr.length = 0;

	var non_ulp_premium = $('input[name="non_ulp_premium"]:checked').length > 0;
	if(non_ulp_premium){
		$('#refsku').hide();
	}else{
		$('#refsku').show();
		$("#pre_skus").chosen({
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


    $("#host_skus").chosen({
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

	$("#ref_skus").chosen({
		search_contains: true,
		allow_single_deselect: true
	});
}).on('hide.bs.modal', function(){
	$('#set_name').val('');
	$('#host_qty').val('1');
	$("#host_skus, #ref_skus, #pre_skus").val('').trigger("chosen:updated");
	$('#set_sku > tbody').html("");
	$('#td-error').html("");
	$('#total_req').text("");
});



$("form[id='addpartsku']").on("submit",function(e){
	var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');

	var form = $(this);
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize()+ '&hostskus=' + arr,
			method: "POST",
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					bootbox.alert("Participating variants was successfully added."); 
					$('#addSku').modal('hide');
					reload_table();
				}else{
					var obj = data.err_msg,  
			        ul = '<ul>';         
			        var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>%s</div>'; 
			        for (var i = 0, l = obj.length; i < l; ++i) {
			        	ul = ul + "<li>" + obj[i] + "</li>";
			            
			        }
			        ul = ul + '</ul>'; 
			       	$("#td-error").text('').append(html.replace(/%s/g, ul));
				}
			}
		});
	}
	e.preventDefault();
});

$("#addpartsku").validate({
	ignore: ":hidden:not(select)",
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		set_name: {
			required: true,
			maxlength: 80
			},
		'ref_skus': {
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

$('#editSku').on('shown.bs.modal', function (e) {
	var skuid = $(e.relatedTarget).data('sku-id');
	$('#sku_id').val(skuid);
	$.ajax({
        async: false,
        type: "GET",
        url: hostname + '/activity/'+activity_id+'/getpartskus/?d_id='+skuid,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data) { 
        	$("#ehost_skus").chosen({
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
			        	$('#ehost_cost_pcs').val('');
			        	$('#ehost_cost_pcs').val(data.price);
			        	$('#ehost_pcs_case').val('');
			        	$('#ehost_pcs_case').val(data.pack_size);
			        },
			        error: function (msg) { roles = msg; }
			    });
			}).val(data.host_code).trigger("chosen:updated");

			$('#ehost_cost_pcs').val(data.host_cost);
			$('#ehost_pcs_case').val(data.host_pcs_case);

			$("#eref_skus").chosen({
				search_contains: true,
				allow_single_deselect: true
			}).val(data.ref_code).trigger("chosen:updated");

			$("#epre_skus").chosen({
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
			        	$('#epre_cost_pcs').val('');
			        	$('#epre_cost_pcs').val(data.price);
			        	$('#epre_pcs_case').val('');
			        	$('#epre_pcs_case').val(data.pack_size);
			        },
			        error: function (msg) { roles = msg; }
			    });
			}).val(data.pre_code).trigger("chosen:updated");
			$('#epre_cost_pcs').val(data.pre_cost);
			$('#epre_pcs_case').val(data.pre_pcs_case);
        },
        error: function (msg) { roles = msg; }
    });
}).on('hide.bs.modal', function(){
	$("#ehost_skus, #eref_skus, #epre_skus").val('').trigger("chosen:updated");
	$('#ehost_cost_pcs').val('');
	$('#ehost_pcs_case').val('');
	$('#epre_cost_pcs').val('');
	$('#epre_pcs_case').val('');
});


function reload_table(){
    table.ajax.reload(null,false); //reload datatable ajax 
}

var table = $("#participating_sku").DataTable({
	"processing": true, //Feature control the processing indicator.
    "serverSide": true, //Feature control DataTables' server-side processing mode.
	"scrollCollapse": true,
	"searching": false,
	"paging": false,
	"bSort": true,
	"ajax": hostname + '/activity/'+activity_id+'/partskus',
	"columnDefs": [ { //this prevents errors if the data is null
		"targets": "_all",
		"defaultContent": ""
	} ]
});




// var dtchtable = $("#td-channels").DataTable({
// 	"processing": true, //Feature control the processing indicator.
//     "serverSide": true, //Feature control DataTables' server-side processing mode.
// 	"bSort": true,
// 	"ajax": hostname + '/activity/'+activity_id+'/tdchannels',
// 	"columnDefs": [ 
// 		{ "targets": "_all","defaultContent": ""},
// 		{ orderable: false,className: 'select-checkbox',targets:   0},
// 	],
// 	select: {
//             style:    'multi',
//             selector: 'td:first-child'
//         },

// });

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


// $('#td-channels tbody').on('click', 'tr', function () {
//    	var data = dtchtable.row(this).data();
//    	console.log(data);
//    	$('#channel_name').val(data.l5_desc);
//    	$('#rtm_tagging').val(data.rtm_tag);
//    	$('#ch_id').val(data.id);
//    	$('#scheme').val(data.scheme);
//    	if(data.tradedeal_type == null){
//    		$("#deal_type").val(0);
//    	}else{
//    		$("#deal_type option").filter(function() {
//     		return $(this).text() == data.tradedeal_type;
// 		}).prop("selected", true);
//    	}
   	
//    	if(data.tradedeal_uom == null){
//    		$("#deal_uom").val(0);
//    	}else{
//    		$("#deal_uom option").filter(function() {
//     		return $(this).text() == data.tradedeal_uom;
// 		}).prop("selected", true);
//    	}

//    	$('#editChannel').modal('show');
// });

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

$("form[id='editpartsku']").on("submit",function(e){
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
				bootbox.alert("Participating variants was successfully added."); 
				$('#editSku').modal('hide');
				reload_table();

			}else{
				bootbox.alert("An error occured while updating."); 
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


$('#non_ulp_premium').click(function() {
    var $this = $(this);
    // $this will contain a reference to the checkbox   
    if ($this.is(':checked')) {
    	// the checkbox was checked 
        $('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost').removeAttr('disabled');
    } else {
    	// the checkbox was unchecked
        $('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost').val('').attr('disabled','disabled');
       
    }
});

var non_ulp_premium = $('input[name="non_ulp_premium"]:checked').length > 0;
if(non_ulp_premium){
	$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').removeAttr('disabled');
}else{
	$('#non_ulp_premium_desc, #non_ulp_premium_code, #non_ulp_premium_cost, #non_ulp_pcs_case').val('').attr('disabled','disabled');
}
