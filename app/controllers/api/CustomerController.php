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

	public function getChannelCustomer(){
		$data = [];
		$channels = \DB::table('channels')->get();

		foreach ($channels as $channel) {
			$channel_children = [];
			$groups = \DB::table('level5')
				->select('groups.group_code', 'groups.group_name')
				->join('level4', 'level4.l4_code', '=', 'level5.l4_code')
				->join('sub_channels', 'sub_channels.coc_03_code', '=', 'level4.coc_03_code')
				->join('accounts', 'accounts.channel_code', '=', 'sub_channels.channel_code')
				->join('ship_tos', 'ship_tos.ship_to_code', '=', 'accounts.ship_to_code')
				->join('customers', 'customers.customer_code', '=', 'ship_tos.customer_code')
				->join('areas', 'areas.area_code', '=', 'customers.area_code')
				->join('groups', 'areas.group_code', '=', 'groups.group_code')
				->where('accounts.active',1)
				->where('ship_tos.active',1)
				->where('accounts.channel_code',$channel->channel_code)
				->groupBy('groups.group_code')
				->orderBy('groups.id')
				->get();

			foreach ($groups as $group) {
				$group_children = [];
				$areas = \DB::table('level5')
					->select('customers.area_code as area_code','area_name')
					->join('level4', 'level4.l4_code', '=', 'level5.l4_code')
					->join('sub_channels', 'sub_channels.coc_03_code', '=', 'level4.coc_03_code')
					->join('accounts', 'accounts.channel_code', '=', 'sub_channels.channel_code')
					->join('ship_tos', 'ship_tos.ship_to_code', '=', 'accounts.ship_to_code')
					->join('customers', 'customers.customer_code', '=', 'ship_tos.customer_code')
					->join('areas', 'areas.area_code', '=', 'customers.area_code')
					->join('groups', 'areas.group_code', '=', 'groups.group_code')
					->where('accounts.active',1)
					->where('ship_tos.active',1)
					->where('accounts.channel_code',$channel->channel_code)
					->where('areas.group_code',$group->group_code)
					->groupBy('customers.area_code')
					->orderBy('areas.id')
					->get();
				foreach ($areas as $area) {
					$area_children = [];
					$customers = \DB::table('level5')
						->select('customers.customer_code','customer_name', 'ship_tos.ship_to_code')
						->join('level4', 'level4.l4_code', '=', 'level5.l4_code')
						->join('sub_channels', 'sub_channels.coc_03_code', '=', 'level4.coc_03_code')
						->join('accounts', 'accounts.channel_code', '=', 'sub_channels.channel_code')
						->join('ship_tos', 'ship_tos.ship_to_code', '=', 'accounts.ship_to_code')
						->join('customers', 'customers.customer_code', '=', 'ship_tos.customer_code')
						->join('areas', 'areas.area_code', '=', 'customers.area_code')
						->join('groups', 'areas.group_code', '=', 'groups.group_code')
						->where('accounts.active',1)
						->where('ship_tos.active',1)
						->where('accounts.channel_code',$channel->channel_code)
						->where('areas.group_code',$group->group_code)
						->where('customers.area_code',$area->area_code)
						->groupBy('customers.customer_code')
						->orderBy('customers.id')
						->get();
					foreach ($customers as $key => $customer) {
						$customer_children =[];
						$ship_tos = \DB::table('level5')
							->select('ship_tos.ship_to_code','ship_tos.ship_to_name')
							->join('level4', 'level4.l4_code', '=', 'level5.l4_code')
							->join('sub_channels', 'sub_channels.coc_03_code', '=', 'level4.coc_03_code')
							->join('accounts', 'accounts.channel_code', '=', 'sub_channels.channel_code')
							->join('ship_tos', 'ship_tos.ship_to_code', '=', 'accounts.ship_to_code')
							->join('customers', 'customers.customer_code', '=', 'ship_tos.customer_code')
							->join('areas', 'areas.area_code', '=', 'customers.area_code')
							->join('groups', 'areas.group_code', '=', 'groups.group_code')
							->where('accounts.active',1)
							->where('ship_tos.active',1)
							->where('accounts.channel_code',$channel->channel_code)
							->where('areas.group_code',$group->group_code)
							->where('customers.area_code',$area->area_code)
							->where('ship_tos.customer_code',$customer->customer_code)
							->groupBy('ship_tos.ship_to_code')
							->orderBy('ship_tos.id')
							->get();
						foreach ($ship_tos as $key => $ship_to) {
							$ship_to_children = [];
							$accounts = \DB::table('level5')
								->select('ship_tos.ship_to_code','accounts.account_name', 'accounts.id')
								->join('level4', 'level4.l4_code', '=', 'level5.l4_code')
								->join('sub_channels', 'sub_channels.coc_03_code', '=', 'level4.coc_03_code')
								->join('accounts', 'accounts.channel_code', '=', 'sub_channels.channel_code')
								->join('ship_tos', 'ship_tos.ship_to_code', '=', 'accounts.ship_to_code')
								->join('customers', 'customers.customer_code', '=', 'ship_tos.customer_code')
								->join('areas', 'areas.area_code', '=', 'customers.area_code')
								->join('groups', 'areas.group_code', '=', 'groups.group_code')
								->where('accounts.active',1)
								->where('ship_tos.active',1)
								->where('accounts.channel_code',$channel->channel_code)
								->where('areas.group_code',$group->group_code)
								->where('customers.area_code',$area->area_code)
								->where('ship_tos.customer_code',$customer->customer_code)
								->where('accounts.ship_to_code',$ship_to->ship_to_code)
								->groupBy('accounts.account_name')
								->get();
							
								foreach ($accounts as $account) {
									if($account->account_name != ''){
										$ship_to_children[] = array(
											'select' => true,
											'title' => $account->account_name,
											// 'isFolder' => true,
											'key' =>  $channel->channel_code.".".$group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code.".".$account->id,
											// 'unselectable' => $unselectable,
											// 'children' => $ship_to_children,
											);
									}
								}
							
							$customer_children[] = array(
								'select' => true,
								'title' => $ship_to->ship_to_name,
								'isFolder' => true,
								'key' =>  $channel->channel_code.".".$group->group_code.".".$area->area_code.".".$customer->customer_code.".".$ship_to->ship_to_code,
								// 'unselectable' => $unselectable,
								'children' => $ship_to_children,
								);
						}

						$area_children[] = array(
							'select' => true,
							'title' => $customer->customer_name,
							'isFolder' => true,
							'key' =>  $channel->channel_code.".".$group->group_code.".".$area->area_code.".".$customer->customer_code,
							// 'unselectable' => $unselectable,
							'children' => $customer_children,
							);
					}

					$group_children[] = array(
						'select' => true,
						'title' => $area->area_name,
						'isFolder' => true,
						'key' =>  $channel->channel_code.".".$group->group_code.".".$area->area_code,
						// 'unselectable' => $unselectable,
						'children' => $area_children,
						);
				}

				// group
				$channel_children[] = array(
					'select' => true,
					'title' => $group->group_name,
					'isFolder' => true,
					'key' => $channel->channel_code.".".$group->group_code,
					// 'unselectable' => $unselectable,
					'children' => $group_children,
					);
			}

			// channels
			$data[] = array(
				'title' => $channel->channel_name,
				'isFolder' => true,
				'key' => $channel->channel_code,
				// 'unselectable' => $unselectable,
				'children' => $channel_children,
				);
		}

		return \Response::json($data,200);
	}

}