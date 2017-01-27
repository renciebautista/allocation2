$(document).ready(function() {
	
	$(".sub").hide();

	$(".treegrid > tbody tr").click(function() {
	  	var main_class = $(this).attr('class');
	  	var classess = main_class.split(" ");
	  	
	  	if(classess.length > 0){
	  		// console.log(classess);
	  		if(classess.length == 1){
	  			var m = "."+main_class+'_';
		  		if($(m).is(":visible")){
		  			$(m).each(function() {
						var sub = $(this).attr('class').split(" ");
						var a = "."+sub[1]+'_';
						$(a).each(function() {
							var sub2 = $(this).attr('class').split(" ");
							$("."+sub2[1]+'_').hide();
						});
						$(a).hide();
					});
					$(this).find('td span').text("+");
			  		$(m).hide();
			  	}else{
			  		$(this).find('td span').text("-");
			  		$(m).find('td span').text("+");
			  		$(m).show();
			  	}
			}else if (classess.length == 4){
				var x = "."+classess[1]+'_';
				if($(x).is(":visible")){
					$(x).each(function() {
						var sub = $(this).attr('class').split(" ");
						$("."+sub[1]+'_').hide();
					});
					$(this).find('td span').text("+");
			  		$(x).hide();
			  	}else{
			  		$(this).find('td span').text("-");
			  		$(x).find('td span').text("+");
			  		$(x).show();

			  	}
			}
	  	}
	  
	});

});
