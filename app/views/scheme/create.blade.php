@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Scheme</h1>
		</div>
	</div>
</div>

@include('partials.notification')
<div class="well">
	{{ Form::open(array('action' => array('SchemeController@store', $activity->id) ,'class' => 'bs-component')) }}
	{{ Form::hidden('activity_id', $activity->id) }}
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
						{{ Form::text('scheme_name','',array('id' => 'scheme_name', 'class' => 'form-control', 'placeholder' => 'Scheme Name')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-4">
						{{ Form::label('item_code', 'Item Code', array('class' => 'control-label')) }}
						{{ Form::text('item_code','',array('class' => 'form-control', 'placeholder' => 'Item Code')) }}
					</div>
					<div class="col-lg-4">
						{{ Form::label('item_barcode', 'Item Barcode', array('class' => 'control-label')) }}
						{{ Form::text('item_barcode','',array('class' => 'form-control', 'placeholder' => 'Item Barcode')) }}
					</div>
					<div class="col-lg-4">
						{{ Form::label('item_casecode', 'Item Casecode', array('class' => 'control-label')) }}
						{{ Form::text('item_casecode','',array('class' => 'form-control', 'placeholder' => 'Item Casecode')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('pr', 'Purchase Requirement (Php)', array('class' => 'control-label')) }}
						{{ Form::text('pr','',array('id' => 'pr', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (Php)')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('srp_p', 'SRP of Premium (Php)', array('class' => 'control-label')) }}
						{{ Form::text('srp_p','',array('id' => 'srp_p', 'class' => 'form-control', 'placeholder' => 'SRP of Premium (Php)')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('other_cost', 'Other Cost Per Deal (Php)', array('class' => 'control-label')) }}
						{{ Form::text('other_cost','',array('class' => 'form-control', 'placeholder' => 'Other Cost Per Deal (Php)')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('ulp', 'Total Unilever Cost (Php)', array('class' => 'control-label')) }}
						{{ Form::text('ulp','',array('class' => 'form-control', 'placeholder' => 'Total Unilever Cost (Php)', 'id' => 'ulp', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('cost_sale', 'Cost to Sales %', array('class' => 'control-label')) }}
						{{ Form::text('cost_sale','',array('class' => 'form-control', 'placeholder' => 'Cost to Sales %', 'id' => 'cost_sale', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('total_alloc', 'Total Allocation', array('class' => 'control-label')) }}
						{{ Form::text('total_alloc','',array('class' => 'form-control', 'placeholder' => 'Total Allocation')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('uom', 'UOM', array('class' => 'control-label')) }}
						{{ Form::text('uom',$activity->activitytype->uom,array('id' => 'oum', 'class' => 'form-control', 'placeholder' => 'UOM', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('deals', 'No. of Deals Per Case', array('class' => 'control-label')) }}
						{{ Form::text('deals','',array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('tts_r', 'TTS Requirement (Php)', array('class' => 'control-label')) }}
						{{ Form::text('tts_r','',array('class' => 'form-control', 'placeholder' => 'TTS Requirement (Php)', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('pe_r', 'PE Requirement (Php)', array('class' => 'control-label')) }}
						{{ Form::text('pe_r','',array('class' => 'form-control', 'placeholder' => 'PE Requirement (Php)', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('total_cost', 'Total Cost (Php)', array('class' => 'control-label')) }}
						{{ Form::text('total_cost','',array('class' => 'form-control', 'placeholder' => 'Total Cost (Php)', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('skus', 'Reference Sales SKUS', array('class' => 'control-label')) }}
						{{ Form::select('skus[]', $skus, null, array('data-placeholder' => 'Select Reference SKUS','id' => 'skus', 'class' => 'form-control', 'multiple' => 'multiple')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
					<a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#schemes">Back to Activity Details</a>
					{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	{{ Form::close() }}
</div>



@stop

@section('page-script')




$("#skus").chosen();

$('#pr, #srp_p, #other_cost,#total_alloc').inputNumber();


$('#pr, #srp_p, #other_cost').blur(function() {
	var srp = accounting.unformat($('#srp_p').val()) || 0;
	var others = accounting.unformat($('#other_cost').val()) || 0;
	$('#ulp').val(accounting.formatNumber(srp+others, 2, ",","."));
	var ulp = accounting.unformat($('#ulp').val()) || 0;
	var pr = accounting.unformat($('#pr').val()) || 0;
	if(pr == 0){
		$('#cost_sale').val(0);
	}else{
		$('#cost_sale').val(accounting.formatNumber((ulp/pr) * 100 ,2));
	}
	compute_budget();
});



$('#total_alloc,#deals').blur(function() {
	compute_budget();
});

function compute_budget(){
	var total_alloc = accounting.unformat($('#total_alloc').val()) || 0;
	var srp = accounting.unformat($('#srp_p').val()) || 0;
	var others = accounting.unformat($('#other_cost').val()) || 0;
	var deals = accounting.unformat($('#deals').val()) || 0;
	console.log($('#oum').val() );
	if($('#oum').val() == 'CASES'){
		$('#tts_r').val(accounting.formatNumber(total_alloc*deals*srp, 2, ",","."));
	}else{
		$('#tts_r').val(accounting.formatNumber(total_alloc*srp, 2, ",","."));
	}
	

	$('#pe_r').val(accounting.formatNumber(total_alloc*others, 2, ",","."));

	var tts_r = accounting.unformat($('#tts_r').val()) || 0;
	var pe_r = accounting.unformat($('#pe_r').val()) || 0;

	$('#total_cost').val(accounting.formatNumber(tts_r+pe_r, 2, ",","."));
}

$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		scheme_name: "required",
		pr: "required",
		srp_p: "required",
		total_alloc: "required",
		deals: "required",
		'skus[]': {
                required: true
            }
	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element.closest('div')).addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element.closest('div')).removeClass(errorClass).addClass(validClass);
  	}
});

@stop


	