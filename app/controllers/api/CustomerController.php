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

		$collection = \ActivityCutomerList::where('activity_id',$id)->orderBy('id')->get();
		
		// $channels = \ActivityCutomerList::where('activity_id',$id)
		// 	->whereNull('parent_id')
		// 	->orderBy('id')
		// 	->get();

		$channels = $collection->filter(function($node)
	    {
	        if ($node->parent_id == NULL)  {
	            return true;
	        }
	    });



		$data = array();
		foreach ($channels as $channel) {
			// $groups = \ActivityCutomerList::where('activity_id',$id)
			// 	->where('parent_id', $channel->key)
			// 	->orderBy('id')
			// 	->get();
			$groups = $collection->filter(function($node) use ($channel)
		    {
		        if ($node->parent_id == $channel->key)  {
		            return true;
		        }
		    });

			$channel_children = array();
			foreach ($groups as $group) {
				// $areas = \ActivityCutomerList::where('activity_id',$id)
				// 	->where('parent_id', $group->key)
				// 	->orderBy('id')
				// 	->get();
				$areas = $collection->filter(function($node) use ($group)
			    {
			        if ($node->parent_id == $group->key)  {
			            return true;
			        }
			    });
				$group_children = array();
				foreach ($areas as $area){
					// $customers = \ActivityCutomerList::where('activity_id',$id)
					// 	->where('parent_id', $area->key)
					// 	->orderBy('id')
					// 	->get();
					$customers = $collection->filter(function($node) use ($area)
				    {
				        if ($node->parent_id == $area->key)  {
				            return true;
				        }
				    });
					$area_children = array();
					foreach ($customers as $customer) {
						// $shiptos = \ActivityCutomerList::where('activity_id',$id)
						// 	->where('parent_id', $customer->key)
						// 	->orderBy('id')
						// 	->get();
						$shiptos = $collection->filter(function($node) use ($customer)
					    {
					        if ($node->parent_id == $customer->key)  {
					            return true;
					        }
					    });
						$customer_children = array();
						foreach ($shiptos as $shipto) {
							// $outlets = \ActivityCutomerList::where('activity_id',$id)
							// 	->where('parent_id', $shipto->key)
							// 	->orderBy('id')
							// 	->get();

							$outlets = $collection->filter(function($node) use ($shipto)
						    {
						        if ($node->parent_id == $shipto->key)  {
						            return true;
						        }
						    });
							$shipto_children = array();
							foreach ($outlets as $outlet) {
								$shipto_children[] = array(
									'title' => $outlet->title,
									'isFolder' => $outlet->isfolder,
									'key' => $outlet->key,
									'unselectable' => $outlet->unselectable,
									'selected' => $outlet->selected,
									// 'children' => $shipto_children
									);
							}

							$customer_children[] = array(
								'title' => $shipto->title,
								'isFolder' => $shipto->isfolder,
								'key' => $shipto->key,
								'unselectable' => $shipto->unselectable,
								'selected' => $shipto->selected,
								'children' => $shipto_children
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
				$channel_children[] = array(
					'title' => $group->title,
					'isFolder' => $group->isfolder,
					'key' => $group->key,
					'unselectable' => $group->unselectable,
					'selected' => $group->selected,
					'children' => $group_children
					);
			}
			$data[] = array(
				'title' => $channel->title,
				'isFolder' => $channel->isfolder,
				'key' => $channel->key,
				'unselectable' => $channel->unselectable,
				'selected' => $channel->selected,
				'children' => $channel_children
				);
		}
		return \Response::json($data,200);
	}



	public function getChannelCustomer(){
		$data = \Customer::getChannelCustomerList();
		return \Response::json($data,200);
	}

	public function getpostedchannelcustomer(){
		$id = \Input::get('id');
		
		$channels = \TradedealChannelList::where('tradedeal_scheme_id',$id)
			->whereNull('parent_id')
			->orderBy('id')
			->get();

		$data = array();
		foreach ($channels as $channel) {
			$rtms = \TradedealChannelList::where('tradedeal_scheme_id',$id)
				->where('parent_id', $channel->key)
				->orderBy('id')
				->get();
			$channel_children = array();
			foreach ($rtms as $rtm) {
				$customers = \ActivityCutomerList::where('activity_id',$id);
				$channel_children[] = array(
					'title' => $rtm->title,
					'isFolder' => $rtm->isfolder,
					'key' => $rtm->key,
					'unselectable' => $rtm->unselectable,
					'selected' => $rtm->selected					
				);
			}
			$data[] = array(
				'title' => $channel->title,
				'isFolder' => $channel->isfolder,
				'key' => $channel->key,
				'unselectable' => $channel->unselectable,
				'selected' => $channel->selected,
				'children' => $channel_children
				);
		}
		return \Response::json($data,200);
	}
}