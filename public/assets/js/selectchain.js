

$(document).ready(function(){

	$.fn.disableButton = function() {
      	$(this).on("submit", function () {
			if($(this).valid()){
				$(this).find(":submit").prop("disabled", true);
				$("#page").hide();
				$("#pageloading").show();
			}
		});
   }; 
   
	$.fn.select_chain = function(option)
	{	
		option.chosen = option.chosen || false;
	    $(option.child).attr("disabled","disabled");

	    setselection($(this));
	    
	    $(this).on("change",function(){
	        setselection($(this));
	    });

	    function setselection(object){
	    	if(object.val() == 0){
	            $(option.child).attr("disabled","disabled").empty();
	            $('<option />', {value: 0, text: option.default_value}).appendTo($(option.child));     
	            if(option.chosen){
	            	$(option.child).trigger("chosen:updated");   
	            }
	              
	        }else{
	            $(option.child).attr("disabled","disabled").empty();
	            $('<option />', {value: 0, text: 'RETRIEVING RECORDS..'}).appendTo($(option.child));    
	           	if(option.chosen){
	            	$(option.child).trigger("chosen:updated");   
	            }
	            $.getJSON(option.ajax_url+'?q='+object.val(), function(data) {
	            	$(option.child).removeAttr("disabled").empty();
	                $('<option />', {value: 0, text: option.default_value}).appendTo($(option.child)); 
	                if(option.chosen){
		            	$(option.child).trigger("chosen:updated");   
		            }      
	                if(data[option.child_value] != ""){
	                    $.each (data[option.child_value], function (key, val) {
	                        $('<option />', {value: key, text: val}).appendTo($(option.child));
	                        if(option.chosen){
				            	$(option.child).trigger("chosen:updated");   
				            }       
	                    });
	                }else{
	                    $(option.child).attr("disabled","disabled").empty();
	                    $('<option />', {value: 0, text: 'NO RECORD FOUND'}).appendTo($(option.child)); 
	                   	if(option.chosen){
			            	$(option.child).trigger("chosen:updated");   
			            }  
	                }
				});
	        }
	    }
	};
});