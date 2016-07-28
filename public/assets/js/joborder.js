$(document).ready(function(){
	var hostname = 'http://' + $(location).attr('host');
	var createJo = $('#createJo');
	var activity_id = $('#act_id').val();

	$('#create-jo').on('click', function(){
		createJo.modal('show');
	});

	
});