<?php

class SchemeAllocRepository
{
    public static function insertAlllocation($scheme){
        self::save($scheme);
    }

    public static function updateAllocation($scheme){
        SchemeAllocation::where('scheme_id',$scheme->id)->delete();
        self::save($scheme);
    }

    private static function save($scheme){
        $activity = Activity::find($scheme->activity_id);
        // $selected_areas = ForceAllocation::getAreas($scheme->activity_id);
        $areas = ForceAllocation::where('activity_id',$scheme->activity_id)->get();

        $customers = ActivityCustomer::customers($scheme->activity_id);
        $_channels = ActivityChannel::channels($scheme->activity_id);

        $_allocation = new AllocationRepository;
        $allocations = $_allocation->customers(Input::get('skus'), $_channels, $customers);
        $_areasales =  $_allocation->area_sales();
        $total_sales = $_allocation->total_sales();

        foreach ($allocations as $customer) {
            $scheme_alloc = new SchemeAllocation;
            $scheme_alloc->scheme_id = $scheme->id;
            $scheme_alloc->group = $customer->group_name;
            $scheme_alloc->area = $customer->area_name;
            $scheme_alloc->sold_to = $customer->customer_name;
            $scheme_alloc->ship_to = $customer->customer_name . ' TOTAL';
            $scheme_alloc->sold_to_gsv = $customer->gsv;    
            $sold_to_gsv_p = 0;
            if($customer->gsv > 0){
                if($total_sales > 0){
                    $sold_to_gsv_p = round(($customer->gsv/$total_sales) * 100,2);
                }
            }
            $scheme_alloc->sold_to_gsv_p = $sold_to_gsv_p;
            $_sold_to_alloc = 0;
            $c_multi = $customer->gsv/$total_sales;
            if($total_sales > 0){
                $_sold_to_alloc = round($c_multi * $scheme->quantity);
            }
            $scheme_alloc->sold_to_alloc = $_sold_to_alloc;
            $scheme_alloc->multi = $c_multi;
            $scheme_alloc->computed_alloc = $_sold_to_alloc;
            if(!$activity->allow_force){
                $scheme_alloc->force_alloc = 0;
                $scheme_alloc->final_alloc = $_sold_to_alloc;
            }else{
                foreach ($areas as $area) {
                    if($area->area_code == $customer->area_code){
                        $area_alloc = round(($scheme->quantity * $area->multi)/100);
                        $area_multiplier = $customer->gsv/$_areasales[$customer->area_code];
                        $f_c = round($area_multiplier * $area_alloc);
                        $scheme_alloc->force_alloc = $f_c;
                        $scheme_alloc->final_alloc = $f_c;
                    }
                }
            }
            
            
            $scheme_alloc->save();

            if(!empty($customer->shiptos)){
                foreach($customer->shiptos as $shipto){
                    $shipto_alloc = 0;
                    $shipto_alloc = new SchemeAllocation;
                    $shipto_alloc->scheme_id = $scheme->id;
                    $shipto_alloc->customer_id = $scheme_alloc->id;
                    $shipto_alloc->group = $customer->group_name;
                    $shipto_alloc->area = $customer->area_name;
                    $shipto_alloc->sold_to = $customer->customer_name;
                    $shipto_alloc->ship_to = $shipto['ship_to_name'];
                    $shipto_alloc->ship_to_gsv = $shipto['gsv'];
                    $_shipto_alloc = 0;
                    $s_multi = 0;
                    if(!is_null($shipto['split'])){
                        if($scheme_alloc->sold_to_alloc > 0){
                            $s_multi = $shipto['split'] / 100;
                        }
                    }else{
                        if($shipto['gsv'] >0){
                            $s_multi = round($shipto['gsv'] / $customer->ado_total,2);
                        }
                    }
                    $_shipto_alloc = round($s_multi  * $scheme_alloc->sold_to_alloc);
                    $shipto_alloc->ship_to_alloc = $_shipto_alloc;
                    $shipto_alloc->multi = $s_multi;
                    $shipto_alloc->computed_alloc = $_shipto_alloc;
                    if(!$activity->allow_force){
                        $shipto_alloc->force_alloc = 0;
                        $shipto_alloc->final_alloc = $_shipto_alloc;
                    }else{
                        if(!is_null($shipto['split'])){
                            if($scheme_alloc->sold_to_alloc > 0){
                                $fs_multi = $shipto['split'] / 100;
                            }
                        }else{
                            if($shipto['gsv'] >0){
                                $fs_multi = round($shipto['gsv'] / $customer->ado_total,2);
                            }
                        }
                        $f_shipto_alloc = round($fs_multi  * $scheme_alloc->force_alloc);
                        $shipto_alloc->force_alloc = $f_shipto_alloc;
                        $shipto_alloc->final_alloc = $f_shipto_alloc;
                    }
                  
                    
                   
                    $shipto_alloc->save();  

                    if(!empty($shipto['accounts'] )){
                        $others = $shipto_alloc->ship_to_alloc;
                        $fothers = $shipto_alloc->force_alloc;
                        foreach($shipto['accounts'] as $account){
                            $account_alloc = new SchemeAllocation;
                            $account_alloc->scheme_id = $scheme->id;
                            $account_alloc->customer_id = $scheme_alloc->id;
                            $account_alloc->shipto_id = $shipto_alloc->id;
                            $account_alloc->group = $customer->group_name;
                            $account_alloc->area = $customer->area_name;
                            $account_alloc->sold_to = $customer->customer_name;
                            $account_alloc->ship_to = $shipto['ship_to_name'];
                            $account_alloc->channel = $account['channel_name'];
                            $account_alloc->outlet = $account['account_name'];
                            $account_alloc->outlet_to_gsv = $account['gsv'];
                            $p = 0;
                            if($customer->gsv > 0){
                                $p = round($account['gsv']/$customer->gsv * 100,2);
                            }
                            $account_alloc->outlet_to_gsv_p = $p;
                            $_account_alloc = round(($p * $shipto_alloc->ship_to_alloc)/100);
                            $account_alloc->outlet_to_alloc = $_account_alloc;
                            if($_account_alloc > 0){
                                $others -= $_account_alloc;
                            }
                            $account_alloc->multi = $p/100;
                            $account_alloc->computed_alloc = $_account_alloc;

                            if(!$activity->allow_force){
                                $account_alloc->force_alloc = $_account_alloc;
                            }else{
                                $f_account_alloc = round(($p * $shipto_alloc->force_alloc)/100);
                                $account_alloc->force_alloc = $f_account_alloc;
                                if($f_account_alloc > 0){
                                    $fothers -= $f_account_alloc;
                                }
                            }
                            
                            $account_alloc->final_alloc = $_account_alloc;
                            $account_alloc->save();
                        }

                        $_others_alloc = 0;
                        $f_others_alloc = 0;
                        $others_alloc = new SchemeAllocation;
                        $others_alloc->scheme_id = $scheme->id;
                        $others_alloc->customer_id = $scheme_alloc->id;
                        $others_alloc->shipto_id = $shipto_alloc->id;
                        $others_alloc->group = $customer->group_name;
                        $others_alloc->area = $customer->area_name;
                        $others_alloc->sold_to = $customer->customer_name;
                        $others_alloc->ship_to = $shipto['ship_to_name'];
                        $others_alloc->outlet = 'OTHERS';
                        $others_alloc->outlet_to_gsv = $account['gsv'];

                        if($others > 0){
                            $_others_alloc = $others;
                        }
                        $others_alloc->outlet_to_alloc = $_others_alloc;

                        if(($_others_alloc > 0) && ($account_alloc->final_alloc > 0)){
                            $others_alloc->multi = $_others_alloc/$account_alloc->final_alloc;
                        }else{
                            $others_alloc->multi = 0;
                        }
                        $others_alloc->computed_alloc = $_others_alloc;
                        if(!$activity->allow_force){
                            $others_alloc->force_alloc = 0;
                            $others_alloc->final_alloc = $_others_alloc;
                        }else{
                            if($fothers > 0){
                                $f_others_alloc = $fothers;
                            }

                            $others_alloc->force_alloc = $f_others_alloc;
                            $others_alloc->final_alloc = $f_others_alloc;
                            
                        }
                        
                        $others_alloc->save();
                    }
                }
            }
        }
    }
}
