$(window).load(function() {
	var hostname = 'http://' + $(location).attr('host');

	var calendar = $('#calendar').fullCalendar({
      	header: {
        	left: 'prev,next today',
        	center: 'title',
        	right: 'month,listMonth'
      	},
      	selectable: false,
      	selectHelper: false,
      	editable: false,
      	events: hostname + '/api/activities'
    });

});


	

        
