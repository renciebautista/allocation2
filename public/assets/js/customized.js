$(document).ready(function(){
	var hostname = 'http://' + $(location).attr('host');
	var addMember = $('#addMember');
	var activity_id = $('#act_id').val();

	$('#add-member').on('click', function(){
		addMember.modal('show');
	});

	$("#activity-member").chosen({
		search_contains: true,
		allow_single_deselect: true
	});


	addMember.on('show.bs.modal', function (e) {
	  	$.ajax({
	        async: false,
	        type: "GET",
	        url: hostname + '/api/getnewmembers/?id='+activity_id,
	        contentType: "application/json; charset=utf-8",
	        dataType: "json",
	        success: function (data) { 
	        	var option = $('#activity-member');
	        	option.empty();
	        	$('<option value="0"></option>').appendTo(option);
				$.each(data.user, function(i, text) {
					$('<option value="'+i+'">'+text+'</option>').appendTo(option);
				});

				$("#activity-member").trigger("chosen:updated");

	        },
	        error: function (msg) { bootbox.alert("An error occured while getting users.");  }
	    });
	})

	var table = $("#activity-members").DataTable({
		"processing": true, //Feature control the processing indicator.
	    "serverSide": true, //Feature control DataTables' server-side processing mode.
		"scrollCollapse": true,
		"searching": false,
		"paging": false,
		"bSort": true,
		"ajax": hostname + '/activity/'+activity_id+'/members',
		"columnDefs": [ { //this prevents errors if the data is null
			"targets": "_all",
			"defaultContent": ""
		} ]
	});
});