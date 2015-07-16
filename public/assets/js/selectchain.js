

$(document).ready(function(){

	$.fn.uploadifyTable  = function(methodOrOptions){
		var form  = $(this).find('form');
		var div_sel = $(this).attr('id');
		var action = form.attr('action');
		if(methodOrOptions.reload){
			$("#"+form.attr('id')).uploadify({
				'debug' : false,
				'multi' : methodOrOptions.multi,
				'fileTypeExts' : methodOrOptions.fileTypeExts,
				'method' : 'post',
				'formData' : { 'token' : '{{ csrf_token() }}', 'div_sel' : div_sel },
				'height' : 30,
				'swf'  : '/assets/plugins/uploadify/uploadify.swf',
				'uploader' : action,
				'width' : 120,
				'onUploadSuccess' : function(file, data, response) {
					location.reload();
			    },
			    'onUploadError' : function(file, errorCode, errorMsg, errorString) {
		            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
		        }
			});
		}else{
			$("#"+form.attr('id')).uploadify({
				'debug' : false,
				'multi' : methodOrOptions.multi,
				'fileTypeExts' : methodOrOptions.fileTypeExts,
				'method' : 'post',
				'formData' : { 'token' : '{{ csrf_token() }}', 'div_sel' : div_sel },
				'height' : 30,
				'swf'  : '/assets/plugins/uploadify/uploadify.swf',
				'uploader' : action,
				'width' : 120,
				'onUploadSuccess' : function(file, data, response) {
					jsonObject = jQuery.parseJSON(data);
					var _table = $("#"+jsonObject.div_sel).find('table tr:last');
					var html = "";
					html += '<td>'+jsonObject.file_name+'</td>';
					html += '<td class="upload_date">'+jsonObject.date+'</td>';
					html += '<td class="att_action">';
					html += '	<a href="'+jsonObject.download+'" class="btn btn-success btn-xs">Download</a>';
					html += '	<a href="'+jsonObject.remove+'" class="ajax_delete btn btn-danger btn-xs" id="'+jsonObject.id+'">Delete</a>';
					html += '</td>';
					$(_table).last().after('<tr>'+html+'</tr>');
			    },
			    'onUploadError' : function(file, errorCode, errorMsg, errorString) {
		            alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
		        }
			});
		}
		

		$(this).on("click",".ajax_delete",function(e){
			e.preventDefault();
			var url = $(this).attr('href');
			var id = $(this).attr('id');
			var row = $(this).closest('tr');
			if(confirm("Do you really want to delete this file?")){
				$.ajax({
					type: "POST",
					dataType: "json",
					data : {method: 'DELETE', id: id},
					url: url,
					async: false,
					success: function(data){
						if(data.success == 1){
							row.effect("highlight",{color: '#f4667b'},500,function(){
								row.remove();
							});
						}else{
							alert('Error deleting attachment!');
						}
				   	},
				   	error: function(){
						alert('Error deleting attachment!');
					}
				});
			}
		});
	}

	$.fn.disableButton = function() {
      	$(this).on("submit", function () {
			if($(this).valid()){
				// $(this).find(":submit").prop("disabled", true);
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