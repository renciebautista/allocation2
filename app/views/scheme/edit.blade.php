@extends('layouts.layout')

@section('content')
<div class="page-header" id="banner">
    <div class="row">
        <div class="col-lg-8 col-md-7 col-sm-6">
            <h1>Edit {{ $scheme->name }} </h1>
            <h2>Activty : {{ $scheme->activity->circular_name }}</h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#schemes">Back to Activity Details</a>
        </div>
    </div>

</div>



@include('partials.notification')

<div class="panel panel-primary">
    <div class="panel-heading">Scheme Details</div>
    <div class="panel-body">

            {{ Form::open(array('action' => array('SchemeController@update', $scheme->id), 'method' => 'PUT', 'class' => 'bs-component')) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
                                {{ Form::text('scheme_name', $scheme->name, array('id' => 'scheme_name', 'class' => 'form-control', 'placeholder' => 'Scheme Name')) }}
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
                                {{ Form::select('skus[]', $skus, $sel_skus, array('data-placeholder' => 'Select Reference SKUS','id' => 'skus', 'class' => 'form-control')) }}
                            </div>
                            <div class="col-lg-6">
                                {{ Form::label('involve', 'Host SKU', array('class' => 'control-label')) }}
                                {{ Form::select('involve[]', $skus, $sel_skus, array('data-placeholder' => 'Select Reference SKUS','id' => 'involve', 'class' => 'form-control')) }}
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
                                {{ Form::text('item_code',$scheme->item_code, array('class' => 'form-control', 'placeholder' => 'Item Code')) }}
                            </div>
                            <div class="col-lg-4">
                                {{ Form::label('item_barcode', 'Item Barcode', array('class' => 'control-label')) }}
                                {{ Form::text('item_barcode',$scheme->item_barcode, array('class' => 'form-control', 'placeholder' => 'Item Barcode')) }}
                            </div>
                            <div class="col-lg-4">
                                {{ Form::label('item_casecode', 'Item Casecode', array('class' => 'control-label')) }}
                                {{ Form::text('item_casecode',$scheme->item_casecode,array('class' => 'form-control', 'placeholder' => 'Item Casecode')) }}
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
                                {{ Form::label('srp_p', 'SRP of Premium (Php)', array('class' => 'control-label')) }}
                                {{ Form::text('srp_p',number_format($scheme->srp_p,2), array('id' => 'srp_p', 'class' => 'form-control', 'placeholder' => 'SRP of Premium (Php)')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('other_cost', 'Other Cost Per Deal (Php)', array('class' => 'control-label')) }}
                                {{ Form::text('other_cost',number_format($scheme->other_cost,2), array('class' => 'form-control', 'placeholder' => 'Other Cost Per Deal (Php)')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('ulp', 'Total Unilever Cost (Php)', array('class' => 'control-label')) }}
                                {{ Form::text('ulp',number_format($scheme->ulp,2), array('class' => 'form-control', 'placeholder' => 'Total Unilever Cost (Php)', 'id' => 'ulp', 'readonly' => '')) }}
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
                                {{ Form::text('pr', number_format($scheme->pr,2), array('id' => 'pr', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (Php)')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('cost_sale', 'Cost to Sales %', array('class' => 'control-label')) }}
                                {{ Form::text('cost_sale',number_format($scheme->cost_sale,2), array('class' => 'form-control', 'placeholder' => 'Cost to Sales %', 'id' => 'cost_sale', 'readonly' => '')) }}
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
                                {{ Form::text('uom',$activity->activitytype->uom,array('class' => 'form-control', 'placeholder' => 'UOM', 'readonly' => '')) }}
                            </div>
                            <div class="col-lg-6">
                                {{ Form::label('total_alloc', 'Total Allocation', array('class' => 'control-label')) }}
                                {{ Form::text('total_alloc',number_format($scheme->quantity), array('class' => 'form-control', 'placeholder' => 'Total Allocation')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">
                                {{ Form::label('deals', 'No. of Deals Per Case', array('class' => 'control-label')) }}
                                {{ Form::text('deals',number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case')) }}
                            </div>
                            <div class="col-lg-3">
                                {{ Form::label('deals', 'Total No. of Deals', array('class' => 'control-label')) }}
                                {{ Form::text('deals',number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case','readonly' => '')) }}
                            </div>
                            <div class="col-lg-3">
                                {{ Form::label('deals', 'Total No. of Cases', array('class' => 'control-label')) }}
                                {{ Form::text('deals',number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case','readonly' => '')) }}
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
                                {{ Form::text('tts_r',number_format($scheme->tts_r,2), array('class' => 'form-control', 'placeholder' => 'TTS Requirement (Php)', 'readonly' => '')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('pe_r', 'PE Requirement (Php)', array('class' => 'control-label')) }}
                                {{ Form::text('pe_r',number_format($scheme->pe_r,2), array('class' => 'form-control', 'placeholder' => 'PE Requirement (Php)', 'readonly' => '')) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('total_cost', 'Total Cost (Php)', array('class' => 'control-label')) }}
                                {{ Form::text('total_cost',number_format($scheme->total_cost,2), array('class' => 'form-control', 'placeholder' => 'Total Cost (Php)', 'readonly' => '')) }}
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
                                {{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}

    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <h2>Alocation Summary</h2>
        <div id="allocation" class="table-responsive">
            <table id="customer-allocation" class="table table-condensed table-bordered display compact ">
                    <tr>
                        <th>Group</th>
                        <th>Area</th>
                        <th>Sold To</th>
                        <th>Ship To</th>
                        <th>Channel</th>

                        <th>Outlet</th>
                        <th>SOLD TO GSV</th>
                        <th>SOLD TO GSV %</th>
                        <th>SOLD TO ALLOC</th>

                        <th>SHIP TO GSV</th>
                        <th>SHIP TO ALLOC</th>
                        <th>OUTLET GSV</th>
                        <th>OUTLET ALLOC %</th>
                        <th>OUTLET ALLOC</th>
                        <th>FINAL ALLOC</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                         <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                         <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr> -->
                    <!-- @if(count($scheme_customers) == 0)
                    <tr>
                        <td colspan="15">No record found!</td>
                    </tr>
                    @endif

                    @foreach($scheme_customers as $scheme_customer)
                    <tr data-link="{{ $scheme_customer->id }}">
                        <td field="group">{{ $scheme_customer->group }}</td>
                        <td field="area">{{ $scheme_customer->area }}</td>
                        <td field="soldto">{{ $scheme_customer->sold_to }}</td>
                        <td field="shipto">{{ $scheme_customer->ship_to }}</td>
                        <td field="channel">{{ $scheme_customer->channel }}</td>
                        <td field="outlet">{{ $scheme_customer->outlet }}</td>

                        <td>
                            @if(!empty($scheme_customer->sold_to_gsv))
                            {{ number_format($scheme_customer->sold_to_gsv,2) }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($scheme_customer->sold_to_gsv_p))
                            {{ number_format($scheme_customer->sold_to_gsv_p,2) }}
                            @endif
                            
                        </td>
                        <td>
                            @if(!empty($scheme_customer->sold_to_alloc))
                            {{ number_format($scheme_customer->sold_to_alloc) }}
                            @endif
                        </td>

                        <td>
                            @if((!empty($scheme_customer->ship_to_gsv)) && ($scheme_customer->ship_to_gsv > 0))
                            {{ number_format($scheme_customer->ship_to_gsv,2) }}
                            @endif
                            
                        </td>
                        <td>
                            @if((!empty($scheme_customer->ship_to_alloc)) && ($scheme_customer->ship_to_alloc > 0))
                            {{ number_format($scheme_customer->ship_to_alloc) }}
                            @endif
                        </td>

                        <td>
                            @if((!empty($scheme_customer->outlet_to_gsv)) && ($scheme_customer->outlet_to_gsv > 0))
                            {{ number_format($scheme_customer->outlet_to_gsv,2) }}
                            @endif
                            
                        </td>
                        <td>
                            @if((!empty($scheme_customer->outlet_to_gsv_p)) && ($scheme_customer->outlet_to_gsv_p > 0))
                            {{ number_format($scheme_customer->outlet_to_gsv_p,2) }}
                            @endif
                        </td>
                        <td>
                            @if((!empty($scheme_customer->outlet_to_alloc)) && ($scheme_customer->outlet_to_alloc > 0))
                            {{ number_format($scheme_customer->outlet_to_alloc) }}
                            @endif

                            
                        </td>
                        <td field="alloc">
                            {{ number_format($scheme_customer->final_alloc) }}
                        </td>
                    </tr>
                    @endforeach -->
                <!-- </tbody> -->
            </table> 
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        {{ Form::open(array('action' => array('SchemeController@updateallocation'), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateAlloc')) }}
        {{ Form::hidden('scheme_id', '', array('id' => 'scheme_id')) }}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Udpate Allocation</h4>
            </div>
            <div class="modal-body">
                <table id="alloc_table" class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Group</td>
                            <td field="group"></td>
                        </tr>
                        <tr>
                            <td>Area</td>
                            <td field="area"></td>
                        </tr>
                        <tr>
                            <td>Sold To</td>
                            <td field="soldto"></td>
                        </tr>
                        <tr>
                            <td>Ship To</td>
                            <td field="shipto"></td>
                        </tr>
                        <tr>
                            <td>Channel</td>
                            <td field="channel"></td>
                        </tr>
                        <tr>
                            <td>Outlet</td>
                            <td field="outlet"></td>
                        </tr>
                        <tr>
                            <td>Allocation</td>
                            <td>
                                <input class="form-control" placeholder="Allocation" name="new_alloc" type="text" value="" id="new_alloc">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button class="btn btn-primary">Update</button>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

@stop


@section('page-script')



$("#skus,#involve").chosen();

$('#pr, #srp_p, #other_cost,#total_alloc,#new_alloc').inputNumber();


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



$('#total_alloc').blur(function() {
    compute_budget();
});

function compute_budget(){
    var total_alloc = accounting.unformat($('#total_alloc').val()) || 0;
    var srp = accounting.unformat($('#srp_p').val()) || 0;
    var others = accounting.unformat($('#other_cost').val()) || 0;
    $('#tts_r').val(accounting.formatNumber(total_alloc*srp, 2, ",","."));

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

var table = $("#customer-allocation").dataTable({
        "scrollY": "300px",
        "scrollX": true,
        "scrollCollapse": true,
        "paging": false,
        "bSort": false,
        "ajax": "{{ URL::action('SchemeController@allocation', $scheme->id ) }}",
        "columnDefs": [ { //this prevents errors if the data is null
            "targets": "_all",
            "defaultContent": ""
        } ],
        "columns": [
            //title will auto-generate th columns
           // { "data" : "id",               "title" : "Id", "searchable": false },
            { "data" : "group",         "title" : "Group", "searchable": true },
            { "data" : "area",         "title" : "Area", "searchable": true },
            { "data" : "sold_to",            "title" : "Sold To", "searchable": true },
            { "data" : "ship_to",     "title" : "Ship To", "searchable": true },
            { "data" : "channel",     "title" : "Channel", "searchable": true },
            { "data" : "outlet",     "title" : "Outlet", "searchable": true },
            { "data" : "sold_to_gsv",     "title" : "SOLD TO GSV", "searchable": true },
            { "data" : "sold_to_gsv_p",     "title" : "SOLD TO GSV %", "searchable": true },
            { "data" : "sold_to_alloc",     "title" : "SOLD TO ALLOC", "searchable": true },
            { "data" : "ship_to_gsv",     "title" : "SHIP TO GSV", "searchable": true },
            { "data" : "ship_to_alloc",     "title" : "SHIP TO ALLOC", "searchable": true },
            { "data" : "outlet_to_gsv",     "title" : "OUTLET GSV", "searchable": true },
            { "data" : "outlet_to_gsv_p",     "title" : "OUTLET ALLOC %", "searchable": true },
            { "data" : "outlet_to_alloc",     "title" : "OUTLET ALLOC", "searchable": true },
            { "data" : "final_alloc",     "title" : "FINAL ALLOC", "searchable": true }
        ],
        "createdRow" : function( row, data, index ) {
            $(row).attr('data-link', data.id);
            $(row).find('td').each (function(index) {
                if(index == 0){
                    $(this).attr('field', 'group');
                }
                if(index == 1){
                    $(this).attr('field', 'area');
                }
                if(index == 2){
                    $(this).attr('field', 'soldto');
                }
                if(index == 3){
                    $(this).attr('field', 'shipto');
                }
                if(index == 4){
                    $(this).attr('field', 'channel');
                }
                if(index == 5){
                    $(this).attr('field', 'outlet');
                }
                if(index == 14){
                    $(this).attr('field', 'alloc');
                }
            }); 
        }
    });
new $.fn.dataTable.FixedColumns( table, {
    leftColumns: 6
} );

table.on('dblclick',"tr[data-link]",function() {
    $(this).find('td').each (function() {
        field = $(this).attr('field');
        $('#alloc_table td[field="'+field+'"]').text($(this).text());
    });  
    var id = $(this).attr('data-link');
    alloc = $.trim($('#customer-allocation tr[data-link="'+id+'"] td[field="alloc"]').text());

    $('#updateAlloc').find('#new_alloc').val(alloc);
    $('#updateAlloc').find('#scheme_id').val(id); 

    $('#myModal').modal('show');
});


$("form[id='updateAlloc']").on("submit",function(e){
    var form = $(this);
    var method = form.find('input[name="_method"]').val() || 'POST';
    var url = form.prop('action');
    $.ajax({
        url: url,
        data: form.serialize(),
        method: method,
        dataType: "json",
        success: function(data){
            if(data.success == "1"){
                table.api().ajax.reload();
                bootbox.alert("Allocation was successfully updated."); 
                $('#myModal').modal('hide');
                
            }else{
                bootbox.alert("An error occured while updating."); 
            }
        }
    });
    e.preventDefault();
});


@stop