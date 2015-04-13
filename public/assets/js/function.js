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