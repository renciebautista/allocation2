@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
    <div class="row">
        <div class="col-lg-12 col-md-7 col-sm-6">
            <h1>Bonus Buy Free : {{ $scheme->name }}</h1>
        </div>
    </div>
</div>

@include('partials.notification')

 {{ Form::open(array('action' => array('TradealSchemeController@update', $scheme->id), 'files'=>true, 'method' => 'PUT', 'id' => 'updatescheme', 'class' => 'bs-component')) }}

            {{ Form::hidden('pre_id', $scheme->pre_id, ['id' => 'pre_id']) }}
            {{ Form::hidden('pre', $scheme->pre_desc. ' - ' .$scheme->pre_code, ['id' => 'pre']) }}
            <div>
                <a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#tradedeal">Back</a>
            <button  class="btn btn-primary">Update</button>
            </div>
            <br>
<div class="panel panel-primary">
    <div class="panel-heading">Scheme Details</div>
        <div class="panel-body">

           
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
                                {{ Form::text('scheme_name', $scheme->name, array('id' => 'scheme_name', 'class' => 'form-control', 'id' => 'scheme_name', 'readonly' => '')) }}
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
                                {{ Form::label('deal_type', 'Deal Type', array('class' => 'control-label')) }}
                                {{ Form::select('deal_type',$dealtypes, $scheme->tradedeal_type_id, array('class' => 'form-control', 'id' => 'deal_type')) }}
                            </div>
                            <div class="col-lg-3">
                                {{ Form::label('uom', 'Deal UOM', array('class' => 'control-label')) }}
                                {{ Form::select('uom', $dealuoms, $scheme->tradedeal_uom_id, array('class' => 'form-control', 'id' => 'uom')) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    {{ Form::label('participating_sku', 'Host SKU', array('class' => 'control-label')) }}

                    <table id="participating_sku" class="table table-striped table-hover ">
                    <thead>
                        <tr>
                            <th><input id="select-all-host" type="checkbox"></th>
                            <th>Qty</th>
                            <th>Host SKU</th>
                            <th>Variant Shortcut</th>
                            <th class="right">Unit Cost / Piece</th>
                            <th class="right">Piece / Case</th>
                            <th>Premium SKU</th>
                            <th>Variant Shortcut</th>
                            <th class="right" style="max-width:100px;">Piece / Case</th>
                            <th class="right" style="max-width:150px;">Purchase Requirement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tradedeal_skus as $sku)
                            <tr>
                                <td>
                                    {{ Form::checkbox('skus[]', $sku->id, ((in_array($sku->id,$sel_hosts['selection'])) ? true : false), ['class' => 'sku-checkbox']) }}
                                </td>
                                <td>
                                    {{ Form::text('qty['.$sku->id.']',(isset($sel_hosts['values'][$sku->id])) ? $sel_hosts['values'][$sku->id] : 1, array('class' => 'qty', 'disabled' => 'disabled')) }}
                                </td>
                                <td>{{ $sku->hostDesc() }}</td>
                                <td>{{ $sku->variant }}</td>
                                <td class="right">{{ $sku->host_cost }}</td>
                                <td class="right">{{ $sku->host_pcs_case }}</td>
                                
                                <td class="individual">{{ $sku->preDesc() }}</td>
                                <td class="individual" >{{ $sku->pre_variant }}</td>
                                <td class="individual right">{{ $sku->pre_pcs_case }}</td>
                                <td class="individual right"></td>
                                <td class="collective">N/A</td>
                                <td class="collective">N/A</td>
                                <td class="collective right">N/A</td>
                                <td class="collective right"></td>
                                <td class="collective right"></td>
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table> 
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">
                                {{ Form::label('buy', 'Buy', array('class' => 'control-label')) }}
                                <div class="input-group"> 
                                    {{ Form::text('buy', $scheme->buy, array('id' => 'buy', 'class' => 'form-control', 'placeholder' => 'Buy', 'id' => 'buy')) }}
                                    <span class="input-group-addon">PIECES</span> 
                                </div>
                            </div>

                            <div class="col-lg-3">
                                {{ Form::label('free', 'Free', array('class' => 'control-label')) }}
                                <div class="input-group"> 
                                    {{ Form::text('free', $scheme->free, array('id' => 'free', 'class' => 'form-control', 'placeholder' => 'Free', 'id' => 'free')) }}
                                    <span class="input-group-addon">PIECES</span> 
                                </div>
                            </div>

                            

                            <div class="col-lg-3">
                                {{ Form::label('p_req', 'Total Purchase Requirement (for Collective)', array('class' => 'control-label')) }}
                                {{ Form::text('p_req', null, array('id' => 'p_req', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (for Collective)', 'id' => 'p_req', 'disabled' =>'disabled')) }}
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
                                {{ Form::label('premium_sku', 'Premium SKU (for Collective)', array('class' => 'control-label')) }}
                                @if($tradedeal->non_ulp_premium)
                                {{ Form::text('non_premium_sku', 'N/A', array('id' => 'non_premium_sku', 'class' => 'form-control', 'readonly' => '')) }}
                                @else
                                <input id="premium_sku_ind" class="form-control" readonly="" name="premium_sku_ind" type="text" value="N/A" aria-invalid="false">
                                <select class="form-control" id="premium_sku" name="premium_sku" disabled="disabled">
                                </select>

                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>


<div class="panel panel-primary">
    <div class="panel-heading">Channels Involved</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <a href="#" id="btnCSelectAll">Select all</a> |
                    <a href="#" id="btnCDeselectAll">Deselect all</a>
                    <div id="tdtree"></div>
                    {{ Form::hidden('channels', null, array('id' => 'channels')) }}
                </div>
            </div> 
        </div>
    </div>
</div>


<a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#tradedeal">Back</a>
            <button  class="btn btn-primary">Update</button>

            {{ Form::close() }}
@stop

@section('add-script')
    {{ HTML::script('assets/js/tradedealscheme.js') }}
@stop


@section('page-script')
    

    $("#tdtree").fancytree({
        extensions: [],
        checkbox: true,
        selectMode: 3,
        source: {
            url: "../../api/tdchannels?id={{$activity->id}}&sc={{$scheme->id}}"
        },
        select: function(event, data) {
            // Get a list of all selected nodes, and convert to a key array:
            var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
                 return node.key;
            });
            selectedkeys = selKeys;
            // Get a list of all selected TOP nodes
            var selRootNodes = data.tree.getSelectedNodes(true);
            // ... and convert to a key array:
            var selRootKeys = $.map(selRootNodes, function(node){
              return node.key;
            });
            $("#channels").val(selRootKeys.join(", "));
        }
    });


    function getselectedChannels(){
        $.ajax({
            type: "GET",
            url: "../../api/selectedtdchannels?id={{$scheme->id}}",
            success: function(data){
                $.each(data, function(i, node) {
                    $("#tdtree").fancytree("getTree").getNodeByKey(node).setSelected(true);
                });
            }
        });
    }

     getselectedChannels();
@stop


