<?php
namespace Api;
class CustomerController extends \BaseController {

	public function index_old(){
		$channels = \DB::table('channels')->get();
		$groups = \DB::table('groups')
			->get();
		$data = array();
		foreach ($groups as $group) {
			$areas = \DB::table('areas')->where('group_code',$group->group_code)->orderBy('id')->get();
			$group_children = array();
			foreach ($areas as $area) {
				$customers = \DB::table('customers')
					->where('area_code',$area->area_code)
					->where('customers.active', 1)
					->get();
				$area_children = array();
				foreach ($customers as $customer) {
					$ship_tos =  \DB::table('ship_tos')
						->where('customer_code',$customer->customer_code)
						->where('ship_tos.active', 1)
						->get();
					$customer_children = array();
					foreach ($ship_tos as $ship_to) {
						
						if($ship_to->ship_to_code != ''){
							$ship_to_children = array();
							foreach ($channels as $channel) {
								$accounts = \DB::table('accounts')
									->where('ship_to_code',$ship_to->ship_to_code )
									->where('area_code',$area->area_code)
									->where('channel_code',$channel->channel_code)
									->get();
									
								if(count($accounts)>0){
									$accouts_children = array();
									
									foreach ($accounts as $account) {
										$accouts_children[] = array(
										'title' => $account->account_name,
										'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code.".".$channel->channel_code.".".$account->id,
										);
									}

									$ship_to_children[] = array(
									'title' => $channel->channel_name,
									'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code.".".$channel->channel_code,
									'children' => $accouts_children
									);
									// $ship_tos[$key4]->channel = array('channel' => $channel->channel_name);
									// $ship_tos[$key4]->accounts = $accounts;
								}
							}

							if(count($ship_to_children) > 0){
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								'children' => $ship_to_children
								);
							}else{
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								);
							}
						}
					}
					$area_children[] = array(
					'title' => $customer->customer_name,
					'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code,
					'children' => $customer_children
					);
				}
				$group_children[] = array(
					'title' => $area->area_name,
					'isFolder' => true,
					'key' => $group->group_code.".".$area->area_code,
					'children' => $area_children
					);
			}
			$data[] = array(
				'title' => $group->group_name,
				'isFolder' => true,
				'key' => $group->group_code,
				'children' => $group_children
				);
		}
		return \Response::json($data,200);
	}

	public function index(){
		$id = \Input::get('id');
		$status = \Input::get('status');
		$unselectable = false;
		if($status == "1"){
			$unselectable = true;
		}
		$channels = \DB::table('channels')->get();
		$groups = \DB::table('groups')
			->get();
		$data = array();
		foreach ($groups as $group) {
			// $areas = \DB::table('areas')->where('group_code',$group->group_code)->orderBy('id')->get();
			$areas = \DB::table('customers')
			->select('areas.group_code as group_code','customers.area_code as area_code','area_name')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->where('areas.group_code',$group->group_code)
			->where('customers.active', 1)
			->groupBy('customers.area_code')
			->orderBy('areas.id')
			->get();

			$group_children = array();
			foreach ($areas as $area) {
				$customers = \DB::table('customers')
					->where('area_code',$area->area_code)
					->where('customers.active', 1)
					->get();
				$area_children = array();
				foreach ($customers as $customer) {
					$ship_tos =  \DB::table('ship_tos')
						->where('customer_code',$customer->customer_code)
						->where('ship_tos.active', 1)
						->get();
					$customer_children = array();
					foreach ($ship_tos as $ship_to) {
						
						if($ship_to->ship_to_code != ''){
							$ship_to_children = array();

							$accounts = \DB::table('accounts')
								->where('ship_to_code',$ship_to->ship_to_code )
								->where('area_code',$area->area_code)
								->where('accounts.active',1)
								->get();
								
							if(count($accounts)>0){
								$ship_to_children = array();
								
								foreach ($accounts as $account) {
									$ship_to_children[] = array(
									'title' => $account->account_name,
									'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code.".".$account->id,
									'unselectable' => true,
									);
								}

							}

							if(count($ship_to_children) > 0){
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								'unselectable' => $unselectable,
								'children' => $ship_to_children,
									
								);
							}else{
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								'unselectable' => $unselectable,
								);
							}
						}
					}
					$area_children[] = array(
					'title' => $customer->customer_name,
					'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code,
					'unselectable' => $unselectable,
					'children' => $customer_children,
					);
				}
				$group_children[] = array(
					'select' => true,
					'title' => $area->area_name,
					'isFolder' => true,
					'key' => $group->group_code.".".$area->area_code,
					'unselectable' => $unselectable,
					'children' => $area_children,
					);
			}
			$data[] = array(
				'title' => $group->group_name,
				'isFolder' => true,
				'key' => $group->group_code,
				'unselectable' => $unselectable,
				'children' => $group_children,
				);
		}
		return \Response::json($data,200);
	}

	public function customerselected(){
		$id = \Input::get('id');
		$data = array();
		$sel = \ActivityCustomer::where('activity_id',$id)->get();
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row->customer_node;
			}
		}
		return \Response::json($data,200);
	}

	public function getselectedcustomer(){
		$id = \Input::get('id');
		$selected_customers = array();
		$sel = \ActivityCustomer::where('activity_id',$id)->get();

		if(!empty($sel)){
			foreach ($sel as $row) {
				$selected_customers[] = $row->customer_node;
			}
		}

		$_grps = array();
		$_areas = array();
		$_cust = array();
		$_shp = array();
		$_otlts = array();
		if(!empty($selected_customers)){
			foreach ($selected_customers as $selected_customer) {
				$_selected_customer = explode(".", $selected_customer);
				$_grps[] = $_selected_customer[0];
				if(!empty($_selected_customer[1])){
					$_areas[$_selected_customer[0]][] = $_selected_customer[1];
				}

				if(!empty($_selected_customer[2])){
					$_cust[$_selected_customer[1]][] = $_selected_customer[2];
				}

				if(!empty($_selected_customer[3])){
					$_shp[$_selected_customer[2]][] = $_selected_customer[3];
				}

				if(!empty($_selected_customer[4])){
					$_otlts[$_selected_customer[3]][] = $_selected_customer[4];
				}
			}
		}


		$unselectable = false;
		$channels = \DB::table('channels')->get();
		$groups = \DB::table('groups')
			->get();
		$data = array();
		foreach ($groups as $group) {
			$areas = \DB::table('customers')
			->select('areas.group_code as group_code','customers.area_code as area_code','area_name')
			->join('areas', 'customers.area_code', '=', 'areas.area_code')
			->where('areas.group_code',$group->group_code)
			->where('customers.active', 1)
			->groupBy('customers.area_code')
			->orderBy('areas.id')
			->get();
			$group_children = array();
			foreach ($areas as $area) {
				$customers = \DB::table('customers')
					->where('area_code',$area->area_code)
					->where('customers.active', 1)
					->get();
				$area_children = array();
				foreach ($customers as $customer) {
					$ship_tos =  \DB::table('ship_tos')
						->where('customer_code',$customer->customer_code)
						->where('ship_tos.active', 1)
						->get();
					$customer_children = array();
					foreach ($ship_tos as $ship_to) {
						
						if($ship_to->ship_to_code != ''){
							$ship_to_children = array();

							$accounts = \DB::table('accounts')
								->where('ship_to_code',$ship_to->ship_to_code )
								->where('area_code',$area->area_code)
								->get();
								
							if(count($accounts)>0){
								$ship_to_children = array();
		
								foreach ($accounts as $account) {
									$ship_to_children[] = array(
									'title' => $account->account_name,
									'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code.".".$account->id,
									'unselectable' => $unselectable
									);
								}

							}

							if(count($ship_to_children) > 0){
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								'unselectable' => $unselectable,
								'children' => $ship_to_children,
									
								);
							}else{
								$customer_children[] = array(
								'title' => $ship_to->ship_to_name,
								'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								'unselectable' => $unselectable,
								);
							}
						}
					}

					$area_children[] = array(
					'title' => $customer->customer_name,
					'key' => $group->group_code.".".$area->area_code.".".$customer->customer_code,
					'unselectable' => $unselectable,
					'children' => $customer_children,
					);
				}
				$group_children[] = array(
					'select' => true,
					'title' => $area->area_name,
					'isFolder' => true,
					'key' => $group->group_code.".".$area->area_code,
					'unselectable' => $unselectable,
					'children' => $area_children,
					);
			}
			$data[] = array(
				'title' => $group->group_name,
				'isFolder' => true,
				'key' => $group->group_code,
				'unselectable' => $unselectable,
				'children' => $group_children,

				);
		}
		return \Response::json($data,200);
	}

	public function getpostedcustomers(){
		$id = \Input::get('id');
		
		$groups = \ActivityCutomerList::where('activity_id',$id)
			->whereNull('parent_id')
			->orderBy('id')
			->get();
		$data = array();
		foreach ($groups as $group) {
			$areas = \ActivityCutomerList::where('activity_id',$id)
				->where('parent_id', $group->key)
				->orderBy('id')
				->get();
			$group_children = array();
			foreach ($areas as $area) {
				$customers = \ActivityCutomerList::where('activity_id',$id)
					->where('parent_id', $area->key)
					->orderBy('id')
					->get();
				$area_children = array();
				foreach ($customers as $customer){
					$ship_tos = \ActivityCutomerList::where('activity_id',$id)
						->where('parent_id', $customer->key)
						->orderBy('id')
						->get();
					$customer_children = array();
					foreach ($ship_tos as $ship_to) {
						$outlets = \ActivityCutomerList::where('activity_id',$id)
							->where('parent_id', $ship_to->key)
							->orderBy('id')
							->get();
						$ship_to_children = array();
						foreach ($outlets as $outlet) {
							$ship_to_children[] = array(
								'title' => $outlet->title,
								'isFolder' => $outlet->isfolder,
								'key' => $outlet->key,
								'unselectable' => $outlet->unselectable,
								'selected' => $outlet->selected,
								// 'children' => $ship_to_children
								);
						}


						$customer_children[] = array(
							'title' => $ship_to->title,
							'isFolder' => $ship_to->isfolder,
							'key' => $ship_to->key,
							'unselectable' => $ship_to->unselectable,
							'selected' => $ship_to->selected,
							'children' => $ship_to_children
							);
					}
					$area_children[] = array(
						'title' => $customer->title,
						'isFolder' => $customer->isfolder,
						'key' => $customer->key,
						'unselectable' => $customer->unselectable,
						'selected' => $customer->selected,
						'children' => $customer_children
						);
				}

				$group_children[] = array(
					'title' => $area->title,
					'isFolder' => $area->isfolder,
					'key' => $area->key,
					'unselectable' => $area->unselectable,
					'selected' => $area->selected,
					'children' => $area_children
					);
			}
			$data[] = array(
				'title' => $group->title,
				'isFolder' => $group->isfolder,
				'key' => $group->key,
				'unselectable' => $group->unselectable,
				'selected' => $group->selected,
				'children' => $group_children
				);
		}
		return \Response::json($data,200);
	}

	// public function getChannelCustomerSlow(){
	// 	$data = [];
	// 	$channels = \DB::table('mt_dt_hieracry')
	// 			->select('channels.channel_code', 'channels.channel_name')
	// 			->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 			->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code')
	// 			->groupBy('channels.channel_code')
	// 			->orderBy('channels.channel_name')
	// 			->get();
	// 	foreach ($channels as $channel) {
	// 		$groups = \DB::table('mt_dt_hieracry')
	// 			->select('groups.group_code', 'groups.group_name')
	// 			->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 			->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
	// 			->join('groups', 'groups.group_code', '=', 'areas.group_code')
	// 			->where('sub_channels.channel_code', $channel->channel_code)
	// 			->groupBy('groups.group_code')
	// 			->orderBy('groups.id')
	// 			->get();

	// 		$channel_children = [];
	// 		foreach ($groups as $group) {
	// 			$areas = \DB::table('mt_dt_hieracry')
	// 				->select('areas.area_code', 'areas.area_name')
	// 				->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 				->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
	// 				->join('groups', 'groups.group_code', '=', 'areas.group_code')
	// 				->where('sub_channels.channel_code', $channel->channel_code)
	// 				->where('groups.group_code', $group->group_code)
	// 				->groupBy('areas.area_code')
	// 				->orderBy('areas.area_name')
	// 				->get();
	// 			$group_children = [];
	// 			foreach ($areas as $area) {
	// 				$distributors = \DB::table('mt_dt_hieracry')
	// 					->select('customers.customer_code', 'customers.customer_name')
	// 					->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 					->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
	// 					->join('groups', 'groups.group_code', '=', 'areas.group_code')
	// 					->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code')
	// 					->where('sub_channels.channel_code', $channel->channel_code)
	// 					->where('groups.group_code', $group->group_code)
	// 					->where('areas.area_code', $area->area_code)
	// 					->groupBy('customers.customer_code')
	// 					->orderBy('customers.customer_name')
	// 					->get();
	// 				$area_children = [];
	// 				foreach ($distributors as $distributor) {
	// 					$shiptos = \DB::table('mt_dt_hieracry')
	// 						->select('ship_tos.plant_code', 'ship_tos.ship_to_name', 'ship_tos.ship_to_code')
	// 						->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
	// 						->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
	// 						->join('groups', 'groups.group_code', '=', 'areas.group_code')
	// 						->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code')
	// 						->join('ship_tos', 'ship_tos.plant_code', '=', 'mt_dt_hieracry.plant_code')
	// 						->where('sub_channels.channel_code', $channel->channel_code)
	// 						->where('groups.group_code', $group->group_code)
	// 						->where('areas.area_code', $area->area_code)
	// 						->where('ship_tos.customer_code', $distributor->customer_code)
	// 						->groupBy('ship_tos.plant_code')
	// 						->orderBy('ship_tos.ship_to_name')
	// 						->get();
	// 					$distributor_children = [];
	// 					foreach ($shiptos as $shipto) {
	// 						if($shipto->ship_to_code != '') {
	// 							$shipto_children = array();

	// 							$accounts = \DB::table('accounts')
	// 								->where('ship_to_code',$shipto->ship_to_code )
	// 								->where('area_code',$area->area_code)
	// 								->where('channel_code',$channel->channel_code)
	// 								->where('accounts.active',1)
	// 								->get();
									
	// 							if(count($accounts)>0){
	// 								$shipto_children = array();
									
	// 								foreach ($accounts as $account) {
	// 									$shipto_children[] = array(
	// 									'title' => $account->account_name,
	// 									'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code.".".$account->id,
	// 									);
	// 								}

	// 							}

	// 							if(count($shipto_children) > 0){
	// 								$customer_children[] = array(
	// 								'title' => $shipto->ship_to_name,
	// 								'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code,
	// 								'children' => $shipto_children,
										
	// 								);
	// 							}else{
	// 								$customer_children[] = array(
	// 								'title' => $shipto->ship_to_name,
	// 								'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code,
	// 								);
	// 							}
	// 						}

	// 						$distributor_children[] = array(
	// 							'title' => $shipto->ship_to_name,
	// 							'isFolder' => true,
	// 							'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code,
	// 							'children' => $shipto_children,
	// 							);
	// 					}

	// 					$area_children[] = array(
	// 						'title' => $distributor->customer_name,
	// 						'isFolder' => true,
	// 						'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code,
	// 						'children' => $distributor_children,
	// 						);
	// 				}
	// 				$group_children[] = array(
	// 					'title' => $area->area_name,
	// 					'isFolder' => true,
	// 					'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code,
	// 					'children' => $area_children,
	// 					);
	// 			}
	// 			$channel_children[] = array(
	// 				'title' => $group->group_name,
	// 				'isFolder' => true,
	// 				'key' => $channel->channel_code.'.'.$group->group_code,
	// 				'children' => $group_children,
	// 				);
	// 		}

	// 		$data[] = array(
	// 			'title' => $channel->channel_name,
	// 			'isFolder' => true,
	// 			'key' => $channel->channel_code,
	// 			'children' => $channel_children,
	// 			);
	// 	}

	// 	return \Response::json($data,200);
	// }


	public function getChannelCustomer(){
		$data = [];
		$channels = \DB::table('customer_trees')
				->select('channels.channel_code', 'channels.channel_name')
				->join('channels', 'channels.channel_code', '=', 'customer_trees.channel_code')
				->groupBy('channels.channel_code')
				->orderBy('channels.channel_name')
				->get();
		foreach ($channels as $channel) {
			$groups = \DB::table('customer_trees')
				->select('groups.group_code', 'groups.group_name')
				->join('groups', 'groups.group_code', '=', 'customer_trees.group_code')
				->where('customer_trees.channel_code', $channel->channel_code)
				->groupBy('groups.group_code')
				->orderBy('groups.id')
				->get();

			$channel_children = [];
			foreach ($groups as $group) {
				$areas = \DB::table('customer_trees')
					->select('areas.area_code', 'areas.area_name')
					->join('areas', 'areas.area_code', '=', 'customer_trees.area_code')
					->where('customer_trees.channel_code', $channel->channel_code)
					->where('customer_trees.group_code', $group->group_code)
					->groupBy('areas.area_code')
					->orderBy('areas.area_name')
					->get();
				$group_children = [];
				foreach ($areas as $area) {
					$distributors = \DB::table('customer_trees')
						->select('customers.customer_code', 'customers.customer_name')
						->join('customers', 'customers.customer_code', '=', 'customer_trees.customer_code')
						->where('customer_trees.channel_code', $channel->channel_code)
						->where('customer_trees.group_code', $group->group_code)
						->where('customer_trees.area_code', $area->area_code)
						->groupBy('customers.customer_code')
						->orderBy('customers.customer_name')
						->get();
					$area_children = [];
					foreach ($distributors as $distributor) {
						$shiptos = \DB::table('customer_trees')
							->select('ship_tos.plant_code', 'ship_tos.ship_to_name', 'ship_tos.ship_to_code', 'ship_tos.plant_code')
							->join('ship_tos', 'ship_tos.plant_code', '=', 'customer_trees.plant_code')
							->where('customer_trees.channel_code', $channel->channel_code)
							->where('customer_trees.group_code', $group->group_code)
							->where('customer_trees.area_code', $area->area_code)
							->where('customer_trees.customer_code', $distributor->customer_code)
							->groupBy('ship_tos.plant_code')
							->orderBy('ship_tos.ship_to_name')
							->get();
						$distributor_children = [];
						foreach ($shiptos as $shipto) {
							if($shipto->ship_to_code != '') {
								$shipto_children = array();

								$accounts =  \DB::table('customer_trees')
									->select('accounts.id', 'account_name')
									->join('accounts', 'accounts.id', '=', 'customer_trees.account_id')
									->where('customer_trees.channel_code', $channel->channel_code)
									->where('customer_trees.group_code', $group->group_code)
									->where('customer_trees.area_code', $area->area_code)
									->where('customer_trees.customer_code', $distributor->customer_code)
									->where('customer_trees.plant_code', $shipto->plant_code)
									->orderBy('accounts.account_name')
									->get();
									
								if(count($accounts)>0){
									$shipto_children = array();
									
									foreach ($accounts as $account) {
										$shipto_children[] = array(
										'title' => $account->account_name,
										'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code.".".$account->id,
										);
									}

								}

								if(count($shipto_children) > 0){
									$customer_children[] = array(
									'title' => $shipto->ship_to_name,
									'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code,
									'children' => $shipto_children,
										
									);
								}else{
									$customer_children[] = array(
									'title' => $shipto->ship_to_name,
									'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code,
									);
								}
							}

							$distributor_children[] = array(
								'title' => $shipto->ship_to_name,
								'isFolder' => true,
								'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code.'.'.$shipto->plant_code,
								'children' => $shipto_children,
								);
						}

						$area_children[] = array(
							'title' => $distributor->customer_name,
							'isFolder' => true,
							'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code.'.'.$distributor->customer_code,
							'children' => $distributor_children,
							);
					}
					$group_children[] = array(
						'title' => $area->area_name,
						'isFolder' => true,
						'key' => $channel->channel_code.'.'.$group->group_code.'.'.$area->area_code,
						'children' => $area_children,
						);
				}
				$channel_children[] = array(
					'title' => $group->group_name,
					'isFolder' => true,
					'key' => $channel->channel_code.'.'.$group->group_code,
					'children' => $group_children,
					);
			}

			$data[] = array(
				'title' => $channel->channel_name,
				'isFolder' => true,
				'key' => $channel->channel_code,
				'children' => $channel_children,
				);
		}

		return \Response::json($data,200);
	}

}