$(document).ready(function(){
	$.fn.ajax_table = function(option) {
		// init variables
		var trcopy;
		var editing = 0;
		var tdediting = 0;
		var editingtrid = 0;
		var editingtdcol = 0;
		var inputs = ':checked,:selected,:text,textarea,select';



		table  = $(this);
		blankrow = '<tr valign="top" class="inputform">';
		for(i=0;i<option.columns.length;i++){
			// Create input element as per the definition
			input = createInput(i,'',option.columns);
			blankrow += '<td class="ajaxReq">'+input+'</td>';
		}
		blankrow += '<td><a href="javascript:;" class="ajaxSave btn btn-primary btn-xs">Save</a></td></tr>';
		
		// append blank row at the end of table
		table.append(blankrow);

		// bind events
		if (option.onInitRow !== undefined) {
         	option.onInitRow();
      	}


		// Add new record
		$(".ajaxSave").on("click",function(){
			var validation = 1;

			var $inputs =
			table.find(inputs).filter(function() {
				// check if input element is blank ??
				if($.trim( this.value ) == ""){
					$(this).parent().addClass("has-error");
					validation = 0;
				}else{
					$(this).parent().removeClass("has-error");
				}
				return $.trim( this.value );
			});

			var array = $inputs.map(function(){
				//console.log(this.value);
				//console.log(this);
				return this.value;
			}).get();
			
			var serialized = $inputs.serialize();
			// alert(serialized);
			if(validation == 1){
				ajax(serialized,"save");
			}
		});
	};

	createInput = function(i,str,obj){
		str = typeof str !== 'undefined' ? str : null;
		//alert(str);
		if(obj[i].type == "text"){
			input = '<input class="form-control" type='+obj[i].type+' name='+obj[i].id+' id='+obj[i].id+' placeholder="'+obj[i].placeholder+'" value='+str+' >';
		}else if(obj[i].type == "textarea"){
			input = '<textarea name='+obj[i].id+' id='+obj[i].id+' placeholder="'+obj[i].placeholder+'">'+str+'</textarea>';
		}
		else if(obj[i].type == "select"){
			input = '<select class="form-control" name='+obj[i].id+' id='+obj[i].id+'>';
			// for(i=0;i<selectOpt.length;i++){
			// 	//console.log(selectOpt[i]);
			// 	selected = "";
			// 	if(str == selectOpt[i])
			// 		selected = "selected";
			// 	input += '<option value="'+selectOpt[i]+'" '+selected+'>'+selectOpt[i]+'</option>';
			// }
			selected = "";
			// input += '<option value="1"></option>';
			input += '</select>';
			//console.log(str);
		}
		return input;
	}


	ajax = function (params,action){
		$.ajax({
			type: "POST", 
			url: "ajax.php", 
			data : params+"&action="+action,
			dataType: "json",
			success: function(response){
			  switch(action){
				case "save":
					var seclastRow = $("."+table+" tr").length;
					if(response.success == 1){
						var html = "";
						
						html += "<td>"+parseInt(seclastRow - 1)+"</td>";
						for(i=0;i<columns.length;i++){
							html +='<td class="'+columns[i]+'">'+response[columns[i]]+'</td>';
						}
						html += '<td><a href="javascript:;" id="'+response["id"]+'" class="ajaxEdit"><img src="'+editImage+'"></a> <a href="javascript:;" id="'+response["id"]+'" class="'+deletebutton+'"><img src="'+deleteImage+'"></a></td>';
						// Append new row as a second last row of a table
						$("."+table+" tr").last().before('<tr id="'+response.id+'">'+html+'</tr>');
						
						if(effect == "slide"){
							// Little hack to animate TR element smoothly, wrap it in div and replace then again replace with td and tr's ;)
							$("."+table+" tr:nth-child("+seclastRow+")").find('td')
							 .wrapInner('<div style="display: none;" />')
							 .parent()
							 .find('td > div')
							 .slideDown(700, function(){
								  var $set = $(this);
								  $set.replaceWith($set.contents());
							});
						}
						else if(effect == "flash"){
						   $("."+table+" tr:nth-child("+seclastRow+")").effect("highlight",{color: '#acfdaa'},100);
						}else
						   $("."+table+" tr:nth-child("+seclastRow+")").effect("highlight",{color: '#acfdaa'},1000);

						// Blank input fields
						$(document).find("."+table).find(inputs).filter(function() {
							// check if input element is blank ??
							this.value = "";
							$(this).removeClass("success").removeClass("error");
						});
					}
				break;
				case "del":
					var seclastRow = $("."+table+" tr").length;
					if(response.success == 1){
						$("."+table+" tr[id='"+response.id+"']").effect("highlight",{color: '#f4667b'},500,function(){
							$("."+table+" tr[id='"+response.id+"']").remove();
						});
					}
				break;
				case "update":
					$("."+cancelbutton).trigger("click");
					for(i=0;i<columns.length;i++){
						$("tr[id='"+response.id+"'] td[class='"+columns[i]+"']").html(response[columns[i]]);
					}
				break;
				case "updatetd":
					//$("."+cancelbutton).trigger("click");
					var newval = $("."+table+" tr[id='"+editingtrid+"'] td[class='"+editingtdcol+"']").find(inputs).val();
					
					//alert($("."+table+" tr[id='"+editingtrid+"'] td[class='"+editingtdcol+"']").html());
					$("."+table+" tr[id='"+editingtrid+"'] td[class='"+editingtdcol+"']").html(newval);

					//$("."+table+" tr[id='"+editingtrid+"'] td[class='"+editingtdcol+"']").html(newval);
					// remove editing flag
					tdediting = 0;
					$("."+table+" tr[id='"+editingtrid+"'] td[class='"+editingtdcol+"']").effect("highlight",{color: '#acfdaa'},1000);
				break;
			  }
			},
			error: function(){
				alert("Unexpected error, Please try again");
			}
		});
	}
});