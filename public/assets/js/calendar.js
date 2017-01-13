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
      	events: hostname + '/api/activities',
        displayEventEnd: false, 

        eventRender: function (event, element) {
          element.attr('href', 'javascript:void(0);');
          element.click(function() {
              $("#startTime").html(moment(event.start).format('LL'));
              $("#endTime").html(moment(event.end_date).format('LL'));
              $("#eventTitle").html(event.title);
              $("#eventInfo").html(event.description);
              $("#eventLink").attr('href', event.url);
              $("#eventContent").dialog({ modal: true, title: "ETOP Activity", width:400});
          });
      }
        // eventMouseover: function (data, event, view) {
        //     tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:#feb811;position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%;">' + 'title: ' + ': ' + data.title + '</br>' + 'start: ' + ': ' + data.start + '</div>';
        //     $("body").append(tooltip);
        //     $(this).mouseover(function (e) {
        //         $(this).css('z-index', 10000);
        //         $('.tooltiptopicevent').fadeIn('500');
        //         $('.tooltiptopicevent').fadeTo('10', 1.9);
        //     }).mousemove(function (e) {
        //         $('.tooltiptopicevent').css('top', e.pageY + 10);
        //         $('.tooltiptopicevent').css('left', e.pageX + 20);
        //     });
        // },
        // eventMouseout: function (data, event, view) {
        //     $(this).css('z-index', 8);
        //     $('.tooltiptopicevent').remove();

        // },
    });

});


	

        
