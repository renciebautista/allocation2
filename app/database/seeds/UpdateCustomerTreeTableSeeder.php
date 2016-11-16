<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class UpdateCustomerTreeTableSeeder extends Seeder {

	public function run()
	{
		DB::table('customer_trees')->truncate();

		$channels = \DB::table('mt_dt_hieracry')
				->select('channels.channel_code', 'channels.channel_name')
				->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code', 'left')
				->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code','left')
				->groupBy('channels.channel_code')
				->orderBy('channels.channel_name')
				->get();

		
		$nodes = [];

		foreach ($channels as $channel) {

			$groups = \DB::table('mt_dt_hieracry')
				->select('groups.group_code', 'groups.group_name')
				->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code','left')
				->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code','left')
				->join('groups', 'groups.group_code', '=', 'areas.group_code','left')
				->where('sub_channels.channel_code', $channel->channel_code)
				->groupBy('groups.group_code')
				->orderBy('groups.id')
				->get();


			foreach ($groups as $group) {

				$areas = \DB::table('mt_dt_hieracry')
					->select('areas.area_code', 'areas.area_name')
					->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code','left')
					->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code','left')
					->join('groups', 'groups.group_code', '=', 'areas.group_code','left')
					->where('sub_channels.channel_code', $channel->channel_code)
					->where('groups.group_code', $group->group_code)
					->groupBy('areas.area_code')
					->orderBy('areas.area_name')
					->get();

				foreach ($areas as $area) {

					$distributors = \DB::table('mt_dt_hieracry')
						->select('customers.customer_code', 'customers.customer_name')
						->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code','left')
						->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code','left')
						->join('groups', 'groups.group_code', '=', 'areas.group_code','left')
						->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code','left')
						->where('sub_channels.channel_code', $channel->channel_code)
						->where('groups.group_code', $group->group_code)
						->where('areas.area_code', $area->area_code)
						->where('customers.active', 1)
						->groupBy('customers.customer_code')
						->orderBy('customers.customer_name')
						->get();

					

					foreach ($distributors as $distributor) {

						$shiptos = \DB::table('mt_dt_hieracry')
							->select('ship_tos.plant_code', 'ship_tos.ship_to_name', 'ship_tos.ship_to_code')
							->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code','left')
							->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code','left')
							->join('groups', 'groups.group_code', '=', 'areas.group_code','left')
							->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code','left')
							->join('ship_tos', 'ship_tos.plant_code', '=', 'mt_dt_hieracry.plant_code','left')
							->where('sub_channels.channel_code', $channel->channel_code)
							->where('groups.group_code', $group->group_code)
							->where('areas.area_code', $area->area_code)
							->where('ship_tos.customer_code', $distributor->customer_code)
							->where('ship_tos.active', 1)
							->groupBy('ship_tos.plant_code')
							->orderBy('ship_tos.ship_to_name')
							->get();

						foreach ($shiptos as $shipto) {
							if($shipto->ship_to_code != '') {
								$accounts = \DB::table('accounts')
									->where('ship_to_code',$shipto->ship_to_code )
									->where('area_code',$area->area_code)
									->where('channel_code',$channel->channel_code)
									->where('accounts.active',1)
									->get();

								foreach ($accounts as $account) {
									CustomerTree::insert(['channel_code' => $channel->channel_code,
									'group_code' => $group->group_code,
									'area_code' => $area->area_code,
									'customer_code' => $distributor->customer_code,
									'plant_code' => $shipto->plant_code,
									'account_id' => $account->id]);
								}
							}

							CustomerTree::insert(['channel_code' => $channel->channel_code,
									'group_code' => $group->group_code,
									'area_code' => $area->area_code,
									'customer_code' => $distributor->customer_code,
									'plant_code' => $shipto->plant_code]);
						}

						CustomerTree::insert(['channel_code' => $channel->channel_code,
									'group_code' => $group->group_code,
									'area_code' => $area->area_code,
									'customer_code' => $distributor->customer_code]);

					}


				}
			}


		}

	}

}