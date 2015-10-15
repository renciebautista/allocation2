<?php

class SchemeAllocation extends \Eloquent {
	protected $table = 'allocations';
	protected $fillable = [];

	public static function getCustomers($id){
		return self::select('group', 'area', 'sold_to', 'ship_to', 'channel', 'outlet', 'customer_id','shipto_id')
			->join('schemes','allocations.scheme_id', '=', 'schemes.id')
			->where('schemes.activity_id', $id)
			->groupBy(array('group', 'area', 'sold_to', 'ship_to', 'channel', 'outlet'))
			->orderBy('allocations.id')
			->get();
	}

	public static function getCustomerAllocation($id){
		return self::where('scheme_id', $id)
			->get();
	}

	public static function getAllocations($id){
		return self::select('allocations.customer_id','allocations.shipto_id','allocations.group','allocations.area','allocations.sold_to',
			'allocations.ship_to', 'allocations.channel', 'allocations.outlet', 'allocations.sold_to_gsv', 
			'allocations.sold_to_gsv_p', 'allocations.sold_to_alloc', 'allocations.ship_to_gsv',
			'allocations.ship_to_alloc' ,'allocations.outlet_to_gsv', 'allocations.outlet_to_gsv_p', 'allocations.outlet_to_alloc',
			'multi','allocations.computed_alloc', 'allocations.force_alloc','allocations.final_alloc','allocations.in_deals',
			'allocations.in_cases','allocations.tts_budget','allocations.pe_budget')
		->where('scheme_id', $id)
		->orderBy('allocations.id')
		->get();
	}

	public static function getAllocationsForExport($id){
		return self::select('allocations.group','allocations.area','allocations.sold_to',
			'allocations.ship_to', 'allocations.channel', 'allocations.outlet', 'allocations.sold_to_gsv', 
			'allocations.sold_to_gsv_p', 'allocations.sold_to_alloc', 'allocations.ship_to_gsv','allocations.ship_to_gsv_p',
			'allocations.ship_to_alloc' ,'allocations.outlet_to_gsv', 'allocations.outlet_to_gsv_p', 'allocations.outlet_to_alloc',
			'multi','allocations.computed_alloc', 'allocations.force_alloc','allocations.final_alloc')
		->where('scheme_id', $id)
		->orderBy('allocations.id')
		->get();
	}

	public static function getExportAllocations($id){
		return self::select('schemes.name','allocations.*')
		->join('schemes', 'allocations.scheme_id','=','schemes.id')
		->where('activity_id', $id)
		->orderBy('allocations.id')
		->get();
	}

	public static function getAllocation($id){
		$data = array();
		$schemes = Scheme::where('activity_id', $id)
				->orderBy('created_at', 'desc')
				->get();
		if(count($schemes)>0){
			foreach ($schemes as $scheme) {
				$alloc = 
				$data[$scheme->id] = self::getAlloc($scheme->id);
			}
		}
		return $data;
	}

	private static function getAlloc($scheme_id){
		$data = array();
		$allocs = self::where('scheme_id', $scheme_id)
					->orderBy('allocations.id')
					->get();
		if(count($allocs)>0){
			foreach ($allocs as $alloc) {
				$_alloc = 0;
				// if((!empty($alloc->customer_id)) && (empty($alloc->shipto_id))){
				// 	echo $alloc->group.".".$alloc->area.".".$alloc->sold_to.".".$alloc->ship_to.".".$alloc->channel.".".$alloc->outlet;
				// 	echo  $alloc->ship_to_alloc;
				// 	echo '<br>';
				// }
				if((empty($alloc->customer_id)) && (empty($alloc->shipto_id))){
					$_alloc = $alloc->sold_to_alloc;
				}

				if((!empty($alloc->customer_id)) && (empty($alloc->shipto_id))){
					$_alloc = $alloc->ship_to_alloc;
				}

				if((!empty($alloc->customer_id)) && (!empty($alloc->shipto_id))){
					$_alloc = $alloc->outlet_to_alloc;
				}
				
				$data[md5($alloc->group.".".$alloc->area.".".$alloc->sold_to.".".$alloc->ship_to.".".$alloc->channel.".".$alloc->outlet)] = $_alloc;
			}
		}
		return $data;
	}

	public static function recomputeAlloc($alloc,$scheme){
		
		if(is_null($alloc->customer_id)){
			$customer_id = $alloc->id;
			$customer_alloc = $alloc->final_alloc;

			$childs = self::where('customer_id',$customer_id)
				->where('shipto_id',null)
				->get();

			if(!empty($childs)){
				foreach ($childs as $child) {
					$child->final_alloc = $alloc->final_alloc * $child->multi;

					$c_in_deals = 0;
					$c_in_cases = 0;
					if($scheme->activity->activitytype->uom == 'CASES'){
						$c_in_deals = $child->final_alloc * $scheme->deals;
						$c_in_cases = $child->final_alloc;
						$tts_budget =$child->final_alloc * $scheme->deals * $scheme->srp_p; 
					}else{
						if($child->final_alloc > 0){
							$c_in_cases = round($child->final_alloc/$scheme->deals);
							$c_in_deals =  $child->final_alloc;
						}
						$tts_budget = $child->final_alloc * $scheme->srp_p;
					}

					$child->in_deals = $c_in_deals;
					$child->in_cases = $c_in_cases;
					$child->tts_budget = $tts_budget;
					$child->pe_budget = $child->final_alloc *  $scheme->other_cost;

					$child->update();

					$outlets = self::where('customer_id',$customer_id)
						->where('shipto_id',$child->id)
						->get();

					$others_alloc = $child->final_alloc;
					if(!empty($outlets)){
						foreach ($outlets as $outlet) {
							$outlet_final_alloc = $child->final_alloc * $outlet->multi;
							if($outlet->outlet == 'OTHERS'){
								$outlet->final_alloc = $others_alloc;
							}else{
								$outlet->final_alloc = $outlet_final_alloc;
								$others_alloc -= $outlet_final_alloc;
							}
							// $outlet->multi = $child->final_alloc/$others_alloc;

							$o_in_deals = 0;
							$o_in_cases = 0;
							if($scheme->activity->activitytype->uom == 'CASES'){
								$o_in_deals = $outlet->final_alloc * $scheme->deals;
								$o_in_cases = $outlet->final_alloc;
								$tts_budget =$outlet->final_alloc * $scheme->deals * $scheme->srp_p; 
							}else{
								if($outlet->final_alloc > 0){
									$o_in_cases = round($outlet->final_alloc/$scheme->deals);
									$o_in_deals =  $outlet->final_alloc;
								}
								$tts_budget = $outlet->final_alloc * $scheme->srp_p;
							}

							$outlet->in_deals = $o_in_deals;
							$outlet->in_cases = $o_in_cases;
							$outlet->tts_budget = $tts_budget;
							$outlet->pe_budget = $outlet->final_alloc *  $scheme->other_cost;

							$outlet->update();
						}
					}
				}
			}

		}else{
			$customer_id = $alloc->customer_id;

			$outlets = self::where('customer_id',$customer_id)
				->where('shipto_id',$alloc->id)
				->get();

			$others_alloc =  $alloc->final_alloc;

			if(!empty($outlets)){
				foreach ($outlets as $outlet) {
					$outlet_final_alloc =  $alloc->final_alloc * $outlet->multi;
					if($outlet->outlet == 'OTHERS'){
						$outlet->final_alloc = $others_alloc;
					}else{
						$outlet->final_alloc = $outlet_final_alloc;
						$others_alloc -= $outlet_final_alloc;
					}

					$o_in_deals = 0;
					$o_in_cases = 0;
					if($scheme->activity->activitytype->uom == 'CASES'){
						$o_in_deals = $outlet->final_alloc * $scheme->deals;
						$o_in_cases = $outlet->final_alloc;
						$tts_budget =$outlet->final_alloc * $scheme->deals * $scheme->srp_p; 
					}else{
						if($outlet->final_alloc > 0){
							$o_in_cases = round($outlet->final_alloc/$scheme->deals);
							$o_in_deals =  $outlet->final_alloc;
						}
						$tts_budget = $outlet->final_alloc * $scheme->srp_p;
					}

					$outlet->in_deals = $o_in_deals;
					$outlet->in_cases = $o_in_cases;
					$outlet->tts_budget = $tts_budget;
					$outlet->pe_budget = $outlet->final_alloc *  $scheme->other_cost;

					$outlet->update();
				}
			}

			$parent = self::find($alloc->customer_id);
			$parent->final_alloc = self::soldtofinalallocation($alloc->scheme_id,$parent->id);

			$p_in_deals = 0;
			$p_in_cases = 0;
			if($scheme->activity->activitytype->uom == 'CASES'){
				$p_in_deals = $parent->final_alloc * $scheme->deals;
				$p_in_cases = $parent->final_alloc;
				$tts_budget =$parent->final_alloc * $scheme->deals * $scheme->srp_p; 
			}else{
				if($parent->final_alloc > 0){
					$p_in_cases = round($parent->final_alloc/$scheme->deals);
					$p_in_deals =  $parent->final_alloc;
				}
				$tts_budget = $parent->final_alloc * $scheme->srp_p;
			}

			$parent->in_deals = $p_in_deals;
			$parent->in_cases = $p_in_cases;
			$parent->tts_budget = $tts_budget;
			$parent->pe_budget = $parent->final_alloc *  $scheme->other_cost;
			$parent->update();		
		}

		
	}


	public static function soldtofinalallocation($id,$customer_id){
		return self::where('scheme_id',$id)
			->where('customer_id',$customer_id)
			->where('shipto_id',null)
			->sum('final_alloc');;
	}

	public static function finalallocation($id){
		return self::where('scheme_id',$id)
			->where('customer_id',null)
			->where('shipto_id',null)
			->sum('final_alloc');;
	}

	public static function totalgsv($id){
		return self::where('scheme_id',$id)
			->where('customer_id',null)
			->where('shipto_id',null)
			->sum('sold_to_gsv');;
	}

	public static function customerTree(){
		$groups = self::select('group','group_code')->groupBy('group_code')->orderBy('id')->get();
		$data = array();
		foreach ($groups as $group) {
			$areas = SchemeAllocation::select('area','area_code')
				->where('group_code',$group->group_code)
				->groupBy('area_code')
				->orderBy('id')
				->get();
			$group_children = array();
			foreach ($areas as $area) {
				$soldtos = SchemeAllocation::select('sold_to','sold_to_code')
					->where('area_code',$area->area_code)
					->groupBy('sold_to_code')
					->orderBy('id')
					->get();
				$area_children = array();
				foreach ($soldtos as $soldto){
					$shiptos = SchemeAllocation::select('ship_to','ship_to_code')
						->where('sold_to_code',$soldto->sold_to_code)
						->whereNotNull('ship_to_code')
						->groupBy('ship_to_code')
						->orderBy('id')
						->get();
					$customer_children = array();
					foreach ($shiptos as $shipto) {
						if($shipto->ship_to_code != ''){
							$customer_children[] = array(
							'title' => $shipto->ship_to,
							'key' => $group->group_code.".".$area->area_code.".".$soldto->sold_to_code.".".$shipto->ship_to_code,
							);
							
						}
						
					}
					$area_children[] = array(
					'title' => $soldto->sold_to,
					'key' => $group->group_code.".".$area->area_code.".".$soldto->sold_to_code,
					'children' => $customer_children,
					);
				}
				$group_children[] = array(
					'select' => true,
					'title' => $area->area,
					'isFolder' => true,
					'key' => $group->group_code.".".$area->area_code,
					'children' => $area_children,
					);
			}
			$data[] = array(
				'title' => $group->group,
				'isFolder' => true,
				'key' => $group->group_code,
				'children' => $group_children,
				);
		}
		return $data;
	}

	public static function outletsTree(){
		$outlets = self::select('outlet')
			->whereNotNull('outlet')
			->groupBy('outlet')
			->orderBy('id')
			->get();
		$data = array(array(
				'title' => 'NONE',
				'key' => 'NONE',
				));
		foreach ($outlets as $outlet) {
			$data[] = array(
				'title' => $outlet->outlet,
				'key' => $outlet->outlet,
				);
		}

		return $data;
	}

	public static function channelsTree(){
		$channels = self::select('channel_code', 'channel')
			->whereNotNull('channel')
			->groupBy('channel')
			->orderBy('id')
			->get();
		$data = array(array(
				'title' => 'NONE',
				'key' => 'NONE',
				));
		foreach ($channels as $channel) {
			$data[] = array(
				'title' => $channel->channel,
				'key' => $channel->channel_code,
				);
		}
		return $data;
	}

	public static function uploadAlloc($records,$scheme){
		DB::beginTransaction();
			try {
			
			$idList = array();
			foreach ($records as $row) {
				$alloc = new SchemeAllocation;
				$alloc->scheme_id = $row->scheme_id;

				if(!is_null($row->customer_id)){
					$alloc->customer_id = $idList[$row->customer_id];
				}

				if(!is_null($row->shipto_id)){
					$alloc->shipto_id = $idList[$row->shipto_id];
				}

				$alloc->group_code = $row->group_code;
				$alloc->group = $row->group;
				$alloc->area_code = $row->area_code;
				$alloc->sold_to_code = $row->sold_to_code;
				$alloc->sold_to = $row->sold_to;
				$alloc->ship_to_code = $row->ship_to_code;
				$alloc->ship_to = $row->ship_to;
				$alloc->channel_code = $row->channel_code;
				$alloc->channel = $row->channel;
				$alloc->account_group_code = $row->account_group_code;
				$alloc->account_group_name = $row->account_group_name;
				$alloc->outlet = $row->outlet;
				

				$in_deals = 0;
				$in_cases = 0;
				if($scheme->activity->activitytype->uom == 'CASES'){
					$in_deals = $row->allocation * $scheme->deals;
					$in_cases = $row->allocation;
				}else{
					if($row->allocation > 0){
						$in_deals =  $row->allocation;
						$in_cases = round($row->allocation/$scheme->deals);
						
					}
				}
				$alloc->computed_alloc = 0;
				$alloc->force_alloc = 0;
				$alloc->final_alloc = $row->allocation;
				$alloc->in_deals = $in_deals;
				$alloc->in_cases = $in_cases;
				$alloc->tts_budget = 1;
				$alloc->pe_budget = 1;
				$alloc->show = $row->show;
				$alloc->save();

				$idList[$row->id] = $alloc->id;
			}
			// dd($idList);
			
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}
}