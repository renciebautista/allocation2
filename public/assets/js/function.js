function GetSelectValues(select) {
  var foo = []; 
	select.each(function(i, selected){ 
	  foo[i] = $(selected).val(); 
	});
	return foo;
}

function GetSelectValue(select) {
  var foo = []; 
	select.each(function(i, selected){ 
		foo[i] = $(selected).text(); 
	});
	if(foo.length > 1){
		return 'MULTI';
	}else{
		return foo[0];
	}
}

function GetSelectText(select) {
  var foo = []; 
	select.each(function(i, selected){ 
		foo[i] = $(selected).text(); 
	});
	return foo;
}

(function( $ ){
   $.fn.disableButton = function() {
      	$(this).on("submit", function () {
			if($(this).valid()){
				$(this).find(":submit").prop("disabled", true);
				$("#page").hide();
				$("#pageloading").find("h3").text("Please wait while page is submitting....");
				$("#pageloading").show();
			}
		});
   }; 
})( jQuery );