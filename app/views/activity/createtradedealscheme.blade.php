@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
    <div class="row">
        <div class="col-lg-12 col-md-7 col-sm-6">
            <h1>Create Trade Deal Scheme</h1>
        </div>
    </div>
</div>

@include('partials.notification')


<div class="panel panel-primary">
    <div class="panel-heading">Scheme Details</div>
        <div class="panel-body">
            {{ Form::open(array('action' => array('ActivityController@storetradealscheme', $activity->id), 'id' => 'createtradedealscheme', 'class' => 'bs-component')) }}
            {{ Form::hidden('pre', $tradedeal->non_ulp_premium_desc. ' - ' .$tradedeal->non_ulp_premium_code, ['id' => 'pre']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
                                {{ Form::text('scheme_name','', array('id' => 'scheme_name', 'class' => 'form-control', 'readonly' => '', 'id' => 'scheme_name')) }}
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
                                {{ Form::select('deal_type',$dealtypes, '', array('class' => 'form-control', 'id' => 'deal_type')) }}
                            </div>
                            <div class="col-lg-3">
                                {{ Form::label('uom', 'Deal UOM', array('class' => 'control-label')) }}
                                {{ Form::select('uom', $dealuoms, '', array('class' => 'form-control', 'id' => 'uom')) }}
                            </div>
                            <div class="col-lg-3">
                                {{ Form::label('coverage', 'Coverage', array('class' => 'control-label')) }}
                                {{ Form::text('coverage', 100, array('id' => 'coverage', 'class' => 'form-control', 'placeholder' => 'Coverage')) }}
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
                            <th class="right">Cost / Pcs</th>
                            <th class="right">Pcs / Case</th>
                            <th>Variant</th>
                            <th>Premium SKU</th>
                            <th class="right" style="max-width:100px;">Pcs / Case</th>
                            <th class="right" style="max-width:150px;">Purchase Requirement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tradedeal_skus as $sku)
                            <tr>
                                <td>
                                    {{ Form::checkbox('skus[]', $sku->id, false, ['class' => 'sku-checkbox']) }}
                                </td>
                                <td>
                                    {{ Form::text('qty['.$sku->id.']', 1, array('class' => 'qty', 'disabled' => 'disabled')) }}
                                </td>

                                <td>{{ $sku->hostDesc() }}</td>
                                <td class="right">{{ $sku->host_cost }}</td>
                                <td class="right">{{ $sku->host_pcs_case }}</td>
                                <td>{{ $sku->variant }}</td>
                                <td class="individual">{{ $sku->preDesc() }}</td>
                                <td class="individual right">{{ $sku->pre_pcs_case }}</td>
                                <td class="individual right"></td>
                                <td class="collective">N/A</td>
                                <td class="collective right">N/A</td>
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
                                {{ Form::text('buy', '', array('id' => 'buy', 'class' => 'form-control', 'placeholder' => 'Buy', 'id' => 'buy')) }}
                            </div>

                            <div class="col-lg-3">
                                {{ Form::label('free', 'Free', array('class' => 'control-label')) }}
                                {{ Form::text('free', '', array('id' => 'free', 'class' => 'form-control', 'placeholder' => 'Free', 'id' => 'free')) }}
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
                                <select class="form-control" id="premium_sku" name="premium_sku" disabled="disabled">
                                </select>
                                {{ Form::text('premium_sku_txt', 'N/A', array('id' => 'premium_sku_txt', 'class' => 'form-control', 'readonly' => '')) }}

                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    {{ Form::label('channels', 'Channels', array('class' => 'control-label')) }}

                    <table id="channels" class="table table-striped table-hover ">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="select_all" value="1" id="example-select-all"></th>
                            <th>Channel Code</th>
                            <th>Channel</th>
                            <th>RTM Tag</th>
                            <th>Scheme</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($channels as $channel)
                            <tr>
                                <td>
                                    @if(empty($channel->name))
                                        {{ Form::checkbox('ch[]', $channel->id) }}
                                    @else
                                        <i class="fa fa-check"></i>
                                    @endif
                                    
                                </td>
                                <td>{{ $channel->l5_code }}</td>
                                <td>{{ $channel->l5_desc }}</td>
                                <td>{{ $channel->rtm_tag }}</td>
                                <td>
                                    @if(!empty($channel->name))
                                    {{ HTML::linkAction('TradealSchemeController@edit' , $channel->name,array('id' => $channel->scheme_id)) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table> 
                </div>
            </div>
            <a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#tradedeal">Back</a>
            <button type="submit" class="btn btn-primary">Save</button>  
            {{ Form::close() }}
    </div>
    
</div>
@stop

@section('add-script')
    {{ HTML::script('assets/js/tradedealscheme.js') }}
@stop


@section('page-script')

@stop


