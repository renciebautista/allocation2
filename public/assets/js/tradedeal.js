var hostname = 'http://' + $(location).attr('host');
var activity_id = $('#activity_id').val();

$('#alloc_in_weeks, #coverage, #non_ulp_premium_cost').inputNumber({ allowDecimals: true, maxDecimalDigits: 2 });
$('#non_ulp_pcs_case').inputNumber({ allowDecimals: false});


$('#participating_sku').ajax_table({
	add_url: "http://localhost:8000/activity/515/addbudget",
	delete_url: "http://localhost:8000/activity/deletebudget",
	update_url: "http://localhost:8000/activity/updatebudget",
	columns: [
		{ type: "select", id: "io_ttstype" , ajax_url: "http://localhost:8000/api/budgettype", validation: { required :true} },
		{ type: "text", id: "io_no", placeholder: "IO Number", validation: { required :true} },
		{ type: "text", id: "io_amount", placeholder: "Amount", validation: { required :true}},
		{ type: "text", id: "io_startdate", placeholder: "mm/dd/yyyy", validation: { required :true} },
		{ type: "text", id: "io_enddate", placeholder: "mm/dd/yyyy",validation: { required :true} },
		{ type: "text", id: "io_remarks", placeholder: "Remarks"},
	],
	onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	},onInitRow: function() {
		$('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");
	},onEditRow : function(){
		$('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");
	},onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}
});

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
