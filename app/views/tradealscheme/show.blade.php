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

            <div>
                <a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#tradedeal">Back</a>
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
                                {{ Form::text('scheme_name1', $scheme->name, array('class' => 'form-control', 'readonly' => '')) }}
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
                                {{ Form::text('deal_type', $scheme->dealType->tradedeal_type, array('id' => 'deal_type', 'class' => 'form-control', 'id' => 'deal_type', 'readonly' => '')) }}
                            </div>
                            <div class="col-lg-3">
                                {{ Form::label('uom', 'Deal UOM', array('class' => 'control-label')) }}
                                {{ Form::text('uom', $scheme->dealUom->tradedeal_uom, array('id' => 'uom', 'class' => 'form-control', 'id' => 'uom', 'readonly' => '')) }}
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
                            <th></th>
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
                        <?php $lowest_pre = 0; ?>
                        @foreach($tradedeal_skus as $sku)
                            <tr>
                                <td>
                                    {{ Form::checkbox('skus[]', $sku->id, ((in_array($sku->id,$sel_hosts['selection'])) ? true : false), ['class' => 'sku-checkbox' , 'disabled' => 'disabled']) }}
                                </td>
                                <td class="right">
                                    1
                                </td>
                                <td>{{ $sku->hostDesc() }}</td>
                                <td>{{ $sku->variant }}</td>
                                <td class="right">{{ $sku->host_cost }}</td>
                                <td class="right">{{ $sku->host_pcs_case }}</td>
                                @if($scheme->tradedeal_type_id == 1)
                                <td class="individual">{{ $sku->preDesc() }}</td>
                                <td class="individual" >{{ $sku->pre_variant }}</td>
                                <td class="individual right">{{ $sku->pre_pcs_case }}</td>
                                <td class="individual right"> 
                                    @if(in_array($sku->id,$sel_hosts['selection']))
                                        @if($scheme->tradedeal_uom_id == 1)
                                            {{ number_format($sku->host_cost * $scheme->buy,2)}}
                                        @elseif($scheme->tradedeal_uom_id == 2)
                                            {{ number_format($sku->host_cost *  $scheme->buy * 12,2)}}
                                        @else
                                            {{ number_format($sku->host_cost *  $scheme->buy * $sku->host_pcs_case,2)  }}
                                        @endif
                                    @else
                                    0.00
                                    @endif
                                </td>
                                @else
                                <td class="collective">N/A</td>
                                <td class="collective">N/A</td>
                                <td class="collective right">N/A</td>
                                <td class="collective right">
                                    @if(in_array($sku->id,$sel_hosts['selection']))
                                        <?php 
                                        if($lowest_pre == 0 ){
                                            $lowest_pre = $sku->host_cost;
                                        }else{
                                            if($sku->host_cost < $lowest_pre){
                                                $lowest_pre = $sku->host_cost;
                                            }
                                        }
                                        ?>
                                        @if($scheme->tradedeal_uom_id == 1)
                                            {{ number_format($sku->host_cost * $scheme->buy,2)  }}
                                        @elseif($scheme->tradedeal_uom_id == 2)
                                            {{ number_format($sku->host_cost * $scheme->buy * 12,2) }}
                                        @else
                                            {{ number_format($sku->host_cost *  $scheme->buy * $sku->host_pcs_case,2) }}
                                        @endif
                                    @else
                                    0.00
                                    @endif
                                </td>
                                @endif
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
                                    {{ Form::text('buy', $scheme->buy, array('id' => 'buy', 'class' => 'form-control', 'placeholder' => 'Buy', 'id' => 'buy', 'disabled' => 'disabled')) }}
                                    <span class="input-group-addon">{{ $scheme->dealUom->tradedeal_uom }}</span> 
                                </div>
                            </div>

                            <div class="col-lg-3">
                                {{ Form::label('free', 'Free', array('class' => 'control-label')) }}
                                <div class="input-group"> 
                                    {{ Form::text('free', $scheme->free, array('id' => 'free', 'class' => 'form-control', 'placeholder' => 'Free', 'id' => 'free', 'disabled' => 'disabled')) }}
                                    <span class="input-group-addon">{{ $scheme->dealUom->tradedeal_uom }}</span> 
                                </div>
                            </div>

                            
                            @if($scheme->tradedeal_type_id == 1)
                            <div class="col-lg-3">
                                {{ Form::label('p_req', 'Total Purchase Requirement (for Collective)', array('class' => 'control-label')) }}
                                {{ Form::text('p_req', 'N/A', array('id' => 'p_req', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (for Collective)', 'id' => 'p_req', 'disabled' =>'disabled')) }}
                            </div>
                            @else
                            <div class="col-lg-3">
                                <?php 
                                if($scheme->tradedeal_uom_id == 1){
                                    $lowest_pre = $lowest_pre * $scheme->buy;
                                }elseif($scheme->tradedeal_uom_id == 2){
                                     $lowest_pre = $lowest_pre * $scheme->buy * 12;
                                 }else{
                                    $lowest_pre = $lowest_pre * $scheme->buy * $sku->host_pcs_case;
                                 }
                                ?>
                                {{ Form::label('p_req', 'Total Purchase Requirement (for Collective)', array('class' => 'control-label')) }}
                                {{ Form::text('p_req',number_format($lowest_pre,2) , array('id' => 'p_req', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (for Collective)', 'id' => 'p_req', 'disabled' =>'disabled')) }}
                            </div>
                            @endif
                            
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
                                @if($scheme->tradedeal_type_id == 1)
                                    <input id="premium_sku_ind" class="form-control" readonly="" name="premium_sku_ind" type="text" value="N/A" aria-invalid="false">
                                @else
                                    <input id="premium_sku_ind" class="form-control" readonly="" name="premium_sku_ind" type="text" value="{{ $scheme->pre_desc }} - {{ $scheme->pre_code }}" aria-invalid="false">
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
                    <div id="tdtree"></div>
                    {{ Form::hidden('channels', null, array('id' => 'channels')) }}
                </div>
            </div> 
        </div>
    </div>
</div>


<a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#tradedeal">Back</a>
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


