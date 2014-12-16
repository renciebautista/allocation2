<?php
namespace Api;
class CustomerController extends \BaseController {


	public function index(){
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
}