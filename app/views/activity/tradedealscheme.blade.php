@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
    <div class="row">
        <div class="col-lg-12 col-md-7 col-sm-6">
            <h1>Trade Deal : {{ $scheme->name }}</h1>
        </div>
    </div>
</div>

@include('partials.notification')


<div class="panel panel-primary">
    <div class="panel-heading">Scheme Details</div>
        <div class="panel-body">

            {{ Form::open(array('action' => array('ActivityController@updatetradedealscheme', $scheme->id), 'files'=>true, 'method' => 'PUT', 'id' => 'updatescheme', 'class' => 'bs-component')) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
                                {{ Form::text('scheme_name', $scheme->name, array('id' => 'scheme_name', 'class' => 'form-control', 'readonly' => '')) }}
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
                                {{ Form::select('deal_type', array('0' => 'PLEASE SELECT') + $dealtypes, $scheme->tradedeal_type_id, array('class' => 'form-control')) }}
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
                            <th><input value="1" type="checkbox"></th>
                            <th>Host SKU</th>
                            <th>Premium SKU (for Individual)</th>
                            <th>Purchase Requirement (for Individual)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tradedeal_skus as $sku)
                            <tr>
                                <td><input value="1" type="checkbox"></td>
                                <td>{{ $sku->hostDesc() }}</td>
                                <td>{{ $sku->preDesc() }}</td>
                                <td></td>
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
                                {{ Form::text('buy', $scheme->buy, array('id' => 'buy', 'class' => 'form-control', 'placeholder' => 'Buy')) }}
                            </div>

                            <div class="col-lg-3">
                                {{ Form::label('free', 'Free', array('class' => 'control-label')) }}
                                {{ Form::text('free', $scheme->free, array('id' => 'free', 'class' => 'form-control', 'placeholder' => 'Free')) }}
                            </div>

                            <div class="col-lg-3">
                                {{ Form::label('coverage', 'Coverage', array('class' => 'control-label')) }}
                                {{ Form::text('coverage', $scheme->coverage, array('id' => 'coverage', 'class' => 'form-control', 'placeholder' => 'Coverage')) }}
                            </div>

                            <div class="col-lg-3">
                                {{ Form::label('coverage', 'Purchase Requirement (for Collective)', array('class' => 'control-label')) }}
                                {{ Form::text('coverage', null, array('id' => 'coverage', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (for Collective)')) }}
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
                                <select class="form-control" id="premium_sku" name="premium_sku" >
                                </select>
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
                            <th><input id="select-all" type="checkbox"></th>
                            <th>Channel Code</th>
                            <th>Channel</th>
                            <th>RTM Tag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($channels as $channel)
                            <tr>
                                <td>
                                    {{ Form::checkbox('ch[]', $channel->id, ((in_array($channel->id,$sel_channels)) ? true : false)) }}
                                </td>
                                <td>{{ $channel->l5_code }}</td>
                                <td>{{ $channel->l5_desc }}</td>
                                <td>{{ $channel->rtm_tag }}</td>
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table> 
                </div>
            </div>
            <a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#tradedeal">Back</a>
            <button  class="btn btn-primary">Update</button>

            {{ Form::close() }}
    </div>
</div>
@stop


@section('page-script')
$('#select-all').change(function() {
    var checkboxes = $(this).closest('table').find(':checkbox');
    if($(this).is(':checked')) {
        checkboxes.prop('checked', true);
    } else {
        checkboxes.prop('checked', false);
    }
});

@stop


