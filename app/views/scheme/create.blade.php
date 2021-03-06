@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			
			<h2>New Scheme</h2>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<a id="scheme_back" class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#schemes">Back to Activity Details</a>
		</div>
	</div>

</div>


@include('partials.notification')
<div class="well">
	{{ Form::open(array('action' => array('SchemeController@store', $activity->id) ,'class' => 'bs-component' ,'id' => 'myform')) }}
	{{ Form::hidden('activity_id', $activity->id) }}
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
						{{ Form::text('scheme_name','',array('id' => 'scheme_name', 'class' => 'form-control', 'placeholder' => 'Scheme Name','maxlength' => 80)) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-6">
						{{ Form::label('skus', 'Reference Sales SKU', array('class' => 'control-label')) }}
						{{ Form::select('skus[]', array('0' => '') + $skus, '', array('data-placeholder' => 'Select Reference Sales SKU','id' => 'skus', 'class' => 'form-control')) }}
					</div>
					<div class="col-lg-6">
						{{ Form::label('involve', 'Host SKU', array('class' => 'control-label')) }}
						{{ Form::select('involve[]', $host, null, array('id' => 'involve', 'class' => 'form-control multiselect', 'multiple' => 'multiple')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-6">
						{{ Form::label('premuim', 'Premium SKU', array('class' => 'control-label')) }}
						{{ Form::select('premuim[]', $premuim, null, array('id' => 'premuim', 'class' => 'form-control multiselect', 'multiple' => 'multiple')) }}
					</div>
					<div class="col-lg-6">
						{{ Form::label('ulp_premium', 'Non ULP Premium SKU', array('class' => 'control-label')) }}
						{{ Form::text('ulp_premium','',array('id' => 'ulp_premium', 'class' => 'form-control', 'placeholder' => 'Non ULP Premium SKU','maxlength' => 100)) }}
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
						{{ Form::label('item_code', 'Promo Item Code', array('class' => 'control-label')) }}
						{{ Form::text('item_code','',array('class' => 'form-control', 'placeholder' => 'Item Code','maxlength' => 8)) }}
					</div>

					<div class="col-lg-8">
						{{ Form::label('item_desc', 'Promo Item Description', array('class' => 'control-label')) }}
						{{ Form::text('item_desc','',array('id' => 'item_desc', 'class' => 'form-control', 'placeholder' => 'Item Description','maxlength' => 80)) }}
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
						{{ Form::label('item_barcode', 'Promo Item Barcode', array('class' => 'control-label')) }}
						{{ Form::text('item_barcode','',array('class' => 'form-control', 'placeholder' => 'Item Barcode','maxlength' => 13)) }}
					</div>
					<div class="col-lg-4">
						{{ Form::label('item_casecode', 'Promo Item Casecode', array('class' => 'control-label')) }}
						{{ Form::text('item_casecode','',array('class' => 'form-control', 'placeholder' => 'Item Casecode','maxlength' => 14)) }}
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
						{{ Form::label('srp_p', 'Cost of Premium (Php)', array('class' => 'control-label')) }}
						{{ Form::text('srp_p','', array('id' => 'srp_p', 'class' => 'form-control', 'placeholder' => 'Cost of Premium (Php)')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('other_cost', 'Other Cost Per Deal (Php)', array('class' => 'control-label')) }}
						{{ Form::text('other_cost','', array('class' => 'form-control', 'placeholder' => 'Other Cost Per Deal (Php)')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('pr', 'Purchase Requirement (Php)', array('class' => 'control-label')) }}
						{{ Form::text('pr', '', array('id' => 'pr', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (Php)')) }}
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
						{{ Form::text('ulp','', array('class' => 'form-control', 'placeholder' => 'Total Unilever Cost (Php)', 'id' => 'ulp', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('lpat', 'List Price After Tax Per Deal (Php)', array('class' => 'control-label')) }}
						{{ Form::text('lpat','', array('id' => 'lpat', 'class' => 'form-control', 'placeholder' => 'List Price After Tax Per Deal (Php)')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('cost_sale', 'Cost to Sales %', array('class' => 'control-label')) }}
						{{ Form::text('cost_sale','', array('class' => 'form-control', 'placeholder' => 'Cost to Sales %', 'id' => 'cost_sale', 'readonly' => '')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					
					<div class="col-lg-6">
						{{ Form::label('uom', 'UOM', array('class' => 'control-label')) }}
						{{ Form::text('uom',$activity->activitytype->uom,array('id' => 'uom', 'class' => 'form-control', 'placeholder' => 'UOM', 'readonly' => '')) }}
					</div>
					<div class="col-lg-6">
						{{ Form::label('total_alloc', 'Total Allocation', array('class' => 'control-label')) }}
						{{ Form::text('total_alloc','', array('class' => 'form-control', 'placeholder' => 'Total Allocation')) }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-8">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-3">
						{{ Form::label('deals', 'Deals Per Case', array('class' => 'control-label')) }}
						{{ Form::text('deals','', array('class' => 'form-control', 'placeholder' => 'Deals Per Case')) }}
					</div>
					<div class="col-lg-3">
						{{ Form::label('total_deals', 'Total No. of Deals', array('class' => 'control-label')) }}
						{{ Form::text('total_deals','', array('id' => 'total_deals', 'class' => 'form-control', 'placeholder' => 'Total No. of Deals','readonly' => '')) }}
					</div>
					<div class="col-lg-3">
						{{ Form::label('total_cases', 'Total No. of Cases', array('class' => 'control-label')) }}
						{{ Form::text('total_cases','', array('id' => 'total_cases', 'class' => 'form-control', 'placeholder' => 'Total No. of Cases','readonly' => '')) }}
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
		<div class="col-lg-4">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('alloc_ref', 'Allocation Reference', array('class' => 'control-label')) }}
						{{ Form::select('alloc_ref', $alloc_refs, 1, array('id' => 'alloc_ref', 'class' => 'form-control')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<br>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
					<a class="btn btn-default" id="back" href="{{action('ActivityController@edit', $activity->id);}}#schemes">Back to Activity Details</a>
					{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	{{ Form::close() }}
</div>



@include('javascript.scheme.create')

@stop

@section('page-script')

@stop