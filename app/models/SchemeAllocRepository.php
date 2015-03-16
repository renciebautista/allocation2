<?php

class SchemeAllocRepository
{
    public static function saveAllocation($scheme){
        self::save($scheme);
    }

    public static function updateAllocation($scheme){
        SchemeAllocation::where('scheme_id',$scheme->id)->delete();
        self::save($scheme);
    }

    private static function save($scheme){
        $customers = ActivityCustomer::customers($scheme->activity_id);
        $_channels = ActivityChannel::channels($scheme->activity_id);

        $_allocation = new AllocationRepository;
        $allocations = $_allocation->customers(Input::get('skus'), $_channels, $customers);
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
            if($total_sales > 0){
                $_sold_to_alloc = round(($customer->gsv/$total_sales) * $scheme->quantity);
            }
            $scheme_alloc->sold_to_alloc = $_sold_to_alloc;
            $scheme_alloc->final_alloc = $_sold_to_alloc;
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
                    if(!is_null($shipto['split'])){
                        if($scheme_alloc->sold_to_alloc > 0){
                            $_shipto_alloc = round(($scheme_alloc->sold_to_alloc * $shipto['split']) / 100);
                        }
                    }else{
                        if($shipto['gsv'] >0){
                            $_shipto_alloc = round(round($shipto['gsv'] / $customer->ado_total,2) * $scheme_alloc->sold_to_alloc);
                        }
                    }
                    $shipto_alloc->ship_to_alloc = $_shipto_alloc;
                    $shipto_alloc->final_alloc = $_shipto_alloc;
                    $shipto_alloc->save();  

                    if(!empty($shipto['accounts'] )){
                        $others = $shipto_alloc->ship_to_alloc;
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
                            $account_alloc->final_alloc = $_account_alloc;
                            $account_alloc->save();
                        }
                        $_others_alloc = 0;
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
                        $others_alloc->final_alloc = $_others_alloc;
                        $others_alloc->save();
                    }
                }
            }
        }
    }
}
