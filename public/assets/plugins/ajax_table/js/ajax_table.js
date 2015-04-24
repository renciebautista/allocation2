
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

	 	var retVal = null; 

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
		blankrow += '<td colspan="2"><a href="javascript:;" class="ajaxSave btn btn-primary btn-xs">Save</a></td></tr>';
		
		// append blank row at the end of table
		table.append(blankrow);

		// bind events
		if (option.onInitRow !== undefined) {
			option.onInitRow();
		}


		// Add new record
		// $("#"+table.attr('id')+" .ajaxSave").on("click",function(){
		table.on("click",".ajaxSave",function(){
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
				ajax("POST",option.add_url,$inputs.serialize(),table,option,effect,inputs);
			}
		});

		// Edit record
		// $("#"+table.attr('id')+" .ajaxEdit").on("click",function(){
		table.on("click",".ajaxEdit",function(){
		//$(document).on("click","."+editbutton,function(){
			var id = $(this).attr("id");
			if(id && editing == 0 && tdediting == 0){
				// hide editing row, for the time being
				// $("#"+table.attr('id')+" tr[:last-child]").fadeOut("fast");
				$("#"+table.attr('id')+" tr[class='inputform']").fadeOut("fast");
				var html = '';
				// html += "<td>"+$("."+table+" tr[id="+id+"] td:first-child").html()+"</td>";
				for(i=0;i<option.columns.length;i++){
					// fetch value inside the TD and place as VALUE in input field
					var val = $("#"+table.attr('id')+" tr[id="+id+"] td[class='"+option.columns[i].id+"']").html();
					input = createInput(i,val,option.columns);
					html +='<td>'+input+'</td>';
				}
				html += '<td><a href="javascript:;" id="'+id+'" class="ajaxUpdate btn btn-primary btn-xs">Update</a>';
				html += '<td><a href="javascript:;" id="'+id+'" class="ajaxCancel btn btn-default btn-xs">Cancel</a>';
				
				// // Before replacing the TR contents, make a copy so when user clicks on 
				trcopy = $("#"+table.attr('id')+" tr[id="+id+"]").html();
				$("#"+table.attr('id')+" tr[id="+id+"]").html(html);	
				
				// set editing flag
				editing = 1;

				if(option.onEditRow !== undefined){
					option.onEditRow();
				}
			}


		});
		
		// update record
		table.on("click",".ajaxUpdate",function(){
			id = $(this).attr("id");
			var validation = 1;

			var $inputs =
			$("#"+table.attr('id')+" tr[id='"+id+"']").find(inputs).filter(function() {
				// check if input element is blank ??
				if($.trim( this.value ) == ""){
					$(this).parent().addClass("has-error");
					validation = 0;
				}else{
					$(this).parent().removeClass("has-error");
				}

				return $.trim( this.value );
			});
			// console.log($inputs);
			if(validation == 1){
				ajax("POST",option.update_url,$inputs.serialize() + "&id=" + id,table,option,effect,inputs);				
			}

			// clear editing flag
			
		});

		// Delete record
		table.on("click",".ajaxDelete",function(){
			var id = $(this).attr("id");
			if(id){
				if(confirm("Do you really want to delete record ?")){
					ajax("DELETE",option.delete_url,{ d_id: id },table,option,effect,inputs);
				}
			}
		});

		// cancel edit
		table.on("click",".ajaxCancel",function(){
		// $(document).on("click","."+cancelbutton,function(){
			var id = $(this).attr("id");
			$("#"+table.attr('id')+" tr[id='"+id+"']").html(trcopy);
			$("#"+table.attr('id')+" tr:last-child").fadeIn("fast");
			editing = 0;
		});

	};

	createInput = function(i,str,obj){
		str = typeof str !== 'undefined' ? str : null;
		//alert(str);
		var input = '';
		if(obj[i].type == "text"){
			input = '<input class="form-control" type='+obj[i].type+' name='+obj[i].id+' id='+obj[i].id+' placeholder="'+obj[i].placeholder+'" value='+str+' >';
		}else if(obj[i].type == "textarea"){
			input = '<textarea name='+obj[i].id+' id='+obj[i].id+' placeholder="'+obj[i].placeholder+'">'+str+'</textarea>';
		}
		else if(obj[i].type == "select"){
			input = '<select class="form-control" name='+obj[i].id+' id='+obj[i].id+'>';
			selected = "";
			if (obj[i].ajax_url !== undefined) {
				$.ajax({
					type: "GET",
					async: false,
					url: obj[i].ajax_url,
					success: function(data){
						$.each(data, function(i, text) {
							selected = "";
							if(str == text){
								selected = "selected";
							}
					
							input += '<option value="'+i+'" '+selected+'>'+text+'</option>';
						});
						
				   }
				});
			}
			input += '</select>';
		}
		return input;
	}


ajax = function (type,url,params,table,option,effect,inputs){
	$.ajax({
		type: type, 
		url: url, 
		data : params,
		dataType: "json",
		success: function(response){
		  switch(type){
			case "POST":
				if(response.success == 1){
					var _table = "#"+table.attr('id')+" tr";
					// console.log(_table);
					var seclastRow = $(_table).length;
					
					var html = "";
					for(i=0;i<option.columns.length;i++){
						html +='<td class="'+option.columns[i].id+'">' +response[option.columns[i].id]+'</td>';
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

					if (option.onSuccess !== undefined) {
						option.onSuccess();
					}
								
				}else{
					if (option.onError !== undefined) {
						option.onError();
					}
					// alert("Unexpected error, Please try again");
				}
				
			break;

			case "PUT":
				if(response.success == 1){
					$(".ajaxCancel").trigger("click");

					for(i=0;i<option.columns.length;i++){
						$("tr[id='"+response.id+"'] td[class='"+option.columns[i].id+"']").html(response[option.columns[i].id]);
					}

					if (option.onSuccess !== undefined) {
						option.onSuccess();
					}
								
				}else{
					if (option.onError !== undefined) {
						option.onError();
					}
					// alert("Unexpected error, Please try again");
				}
				
			break;
			case "DELETE":
				var _table = "#"+table.attr('id')+" tr";
				var seclastRow = $(_table).length;
				if(response.success == 1){
					$("#"+table.attr('id')+" tr[id='"+response.id+"']").effect("highlight",{color: '#f4667b'},500,function(){
						$("#"+table.attr('id')+" tr[id='"+response.id+"']").remove();
					});
					if (option.onSuccess !== undefined) {
						option.onSuccess();
					}
								
				}else{
					if (option.onError !== undefined) {
						option.onError();
					}
					// alert("Unexpected error, Please try again");
				}
			break;
		  }
		},
		error: function(){
			if (option.onError !== undefined) {
				option.onError();
			}
			// alert("Unexpected error, Please try again");
		}
	});
}

});