
$(document).ready(function(){
	$.fn.ajax_table = function(option) {
		// init variables
		var trcopy;
		var editing = 0;
		var tdediting = 0;
		var editingtrid = 0;
		var editingtdcol = 0;
		var inputs = ':checked,:selected,:text,textarea,select';

		var effect = "flash"; 
		var saveAnimationDelay = 3000; 
	 	var deleteAnimationDelay = 1000;

		if(option.effect !== undefined){
			effect = option.effect;
		}


		var table  = $(this);
		var blankrow = '<tr valign="top" class="inputform">';
		for(i=0;i<option.columns.length;i++){
			// Create input element as per the definition
			var input = createInput(i,'',option.columns);
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
		$("#"+table.attr('id')+" .ajaxSave").on("click",function(){
			// console.log($(this).closest("table"));
			var validation = 1;

			var $inputs =
			$(this).closest("table").find(inputs).filter(function() {
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
			// var serialized = $inputs.serialize();
			// alert(serialized);
			if(validation == 1){
				// ajax(serialized,"save");
				$.ajax({
					type: "POST", 
					url: option.add_url,
					data : $inputs.serialize(),
					dataType: "json",
					success: function(response){
						var _table = "#"+table.attr('id')+" tr";
						console.log(_table);
						var seclastRow = $(_table).length;
						
						var html = "";
						for(i=0;i<option.columns.length;i++){
							html +='<td>'+response[option.columns[i].id]+'</td>';
						}
						// console.log(html);
						html += '<td><a href="javascript:;" id="'+response["id"]+'" class="ajaxEdit btn btn-primary btn-xs">Edit</a></td>';
						html += '<td><a href="javascript:;" id="'+response["id"]+'" class="ajaxDelete btn btn-danger btn-xs">Delete</a></td>';
						// Append new row as a second last row of a table
						$(_table).last().before('<tr id="'+response.id+'">'+html+'</tr>');
						
						if(effect == "slide"){
							// Little hack to animate TR element smoothly, wrap it in div and replace then again replace with td and tr's ;)
							$("#"+table.attr('id') +" tr:nth-child("+seclastRow+")").find('td')
							 .wrapInner('<div style="display: none;" />')
							 .parent()
							 .find('td > div')
							 .slideDown(700, function(){
								  var $set = $(this);
								  $set.replaceWith($set.contents());
							});
						}
						else if(effect == "flash"){
						   $("#"+table.attr('id')+" tr:nth-child("+seclastRow+")").effect("highlight",{color: '#acfdaa'},100);
						}else
						   $("#"+table.attr('id')+" tr:nth-child("+seclastRow+")").effect("highlight",{color: '#acfdaa'},1000);

						// Blank input fields
						table.find(inputs).filter(function() {
							// check if input element is blank ??
							// console.log(this);
							if($(this).is( ":text" )){
								this.value = "";
							}
							
							$(this).removeClass("success").removeClass("error");
						});

						if (option.onSaveRow !== undefined) {
							option.onSaveRow();
						}
					}
				});
			}
		});

		// Delete record
		table.on("click",".ajaxDelete",function(){
			var id = $(this).attr("id");
			if(id){
				if(confirm("Do you really want to delete record ?")){
					// ajax("rid="+id,"del");
					$.ajax({
						type: "DELETE", 
						url: option.delete_url,
						data : { d_id: id },
						dataType: "json",
						success: function(response){
							var _table = "#"+table.attr('id')+" tr";
							var seclastRow = $(_table).length;
							if(response.success == 1){
								$("#"+table.attr('id')+" tr[id='"+response.id+"']").effect("highlight",{color: '#f4667b'},500,function(){
									$("#"+table.attr('id')+" tr[id='"+response.id+"']").remove();
								});
							}
						}
					});
				}
					
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

});