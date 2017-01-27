<?php

class CustomerTree extends \Eloquent {
	protected $fillable = [];
	public $timestamps = false;

	public static function updateTree(){
		DB::table('customer_trees')->truncate();
		
		// $channels = \DB::table('mt_dt_hieracry')
		// 		->select('channels.channel_code', 'channels.channel_name')
		// 		->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
		// 		->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code')
		// 		->groupBy('channels.channel_code')
		// 		->orderBy('channels.channel_name')
		// 		->get();

		$query = sprintf('select channels.channel_code, channels.channel_name
			from mt_dt_hieracry
			inner join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
			inner join channels on channels.channel_code = sub_channels.channel_code
			group by channels.channel_code
			order by channels.channel_name');
		$channels =  DB::select(DB::raw($query));

		
		$nodes = [];

		foreach ($channels as $channel) {
			// MT /DT
			// $groups = \DB::table('mt_dt_hieracry1')
			// 	->select('groups.group_code', 'groups.group_name')
			// 	->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
			// 	->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
			// 	->join('groups', 'groups.group_code', '=', 'areas.group_code')
			// 	->where('sub_channels.channel_code', $channel->channel_code)
			// 	->groupBy('groups.group_code')
			// 	->orderBy('groups.id')
			// 	->get();


			$query = sprintf('select groups.group_code, groups.group_name
				from mt_dt_hieracry
				inner join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
				inner join channels on channels.channel_code = sub_channels.channel_code
				inner join areas on areas.area_code = mt_dt_hieracry.area_code
				inner join groups on groups.group_code = areas.group_code
				group by groups.group_code
				order by groups.id');
			$groups =  DB::select(DB::raw($query));

			foreach ($groups as $group) {
				// areas
				// $areas = \DB::table('mt_dt_hieracry')
				// 	->select('areas.area_code', 'areas.area_name')
				// 	->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
				// 	->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
				// 	->join('groups', 'groups.group_code', '=', 'areas.group_code')
				// 	->where('sub_channels.channel_code', $channel->channel_code)
				// 	->where('groups.group_code', $group->group_code)
				// 	->groupBy('areas.area_code')
				// 	->orderBy('areas.area_name')
				// 	->get();

				$query = sprintf("select areas.area_code, areas.area_name
					from mt_dt_hieracry
					inner join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
					inner join channels on channels.channel_code = sub_channels.channel_code
					inner join areas on areas.area_code = mt_dt_hieracry.area_code
					inner join groups on groups.group_code = areas.group_code
					where sub_channels.channel_code = '%s'
					and groups.group_code = '%s'
					group by areas.area_code
					order by areas.area_name",  $channel->channel_code, $group->group_code);
				$areas =  DB::select(DB::raw($query));

				foreach ($areas as $area) {

					// $distributors = \DB::table('mt_dt_hieracry')
					// 	->select('customers.customer_code', 'customers.customer_name')
					// 	->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
					// 	->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
					// 	->join('groups', 'groups.group_code', '=', 'areas.group_code')
					// 	->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code')
					// 	->where('sub_channels.channel_code', $channel->channel_code)
					// 	->where('groups.group_code', $group->group_code)
					// 	->where('areas.area_code', $area->area_code)
					// 	->where('customers.active', 1)
					// 	->groupBy('customers.customer_code')
					// 	->orderBy('customers.customer_name')
					// 	->get();
					$query = sprintf("select customers.customer_code, customers.customer_name
						from mt_dt_hieracry
						inner join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
						inner join channels on channels.channel_code = sub_channels.channel_code
						inner join areas on areas.area_code = mt_dt_hieracry.area_code
						inner join groups on groups.group_code = areas.group_code
						inner join customers on customers.customer_code = mt_dt_hieracry.customer_code
						where sub_channels.channel_code = '%s'
						and groups.group_code = '%s'
						and areas.area_code = '%s'
						and customers.active = 1
						group by customers.customer_code
						order by customers.customer_name", $channel->channel_code, $group->group_code, $area->area_code);
					$distributors =  DB::select(DB::raw($query));
					

					foreach ($distributors as $distributor) {

						// $shiptos = \DB::table('mt_dt_hieracry')
						// 	->select('ship_tos.plant_code', 'ship_tos.ship_to_name', 'ship_tos.ship_to_code')
						// 	->join('sub_channels', 'sub_channels.coc_03_code', '=', 'mt_dt_hieracry.coc_03_code')
						// 	->join('areas', 'areas.area_code', '=', 'mt_dt_hieracry.area_code')
						// 	->join('groups', 'groups.group_code', '=', 'areas.group_code')
						// 	->join('customers', 'customers.customer_code', '=', 'mt_dt_hieracry.customer_code')
						// 	->join('ship_tos', 'ship_tos.plant_code', '=', 'mt_dt_hieracry.plant_code')
						// 	->where('sub_channels.channel_code', $channel->channel_code)
						// 	->where('groups.group_code', $group->group_code)
						// 	->where('areas.area_code', $area->area_code)
						// 	->where('ship_tos.customer_code', $distributor->customer_code)
						// 	->where('ship_tos.active', 1)
						// 	->groupBy('ship_tos.plant_code')
						// 	->orderBy('ship_tos.ship_to_name')
						// 	->get();

						$query = sprintf("select ship_tos.plant_code, ship_tos.ship_to_name, ship_tos.ship_to_code
							from mt_dt_hieracry
							inner join sub_channels on (mt_dt_hieracry.coc_03_code = sub_channels.coc_03_code AND mt_dt_hieracry.coc_04_code = sub_channels.l4_code AND mt_dt_hieracry.coc_05_code = sub_channels.l5_code)
							inner join channels on channels.channel_code = sub_channels.channel_code
							inner join areas on areas.area_code = mt_dt_hieracry.area_code
							inner join groups on groups.group_code = areas.group_code
							inner join customers on customers.customer_code = mt_dt_hieracry.customer_code
							inner join ship_tos on ship_tos.plant_code = mt_dt_hieracry.plant_code
							where sub_channels.channel_code = '%s'
							and groups.group_code = '%s'
							and areas.area_code = '%s'
							and ship_tos.customer_code = '%s'
							and ship_tos.active = 1
							group by ship_tos.plant_code
							order by ship_tos.ship_to_name", $channel->channel_code, $group->group_code, $area->area_code, $distributor->customer_code);
						$shiptos =  DB::select(DB::raw($query));

						foreach ($shiptos as $shipto) {
							if($shipto->ship_to_code != '') {
								
								$accounts = \DB::table('accounts')
									->where('ship_to_code',$shipto->ship_to_code )
									->where('area_code',$area->area_code)
									->where('channel_code',$channel->channel_code)
									->where('accounts.active',1)
									->groupBy('account_name')
									->orderBy('account_name')
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
									'plant_code' => $shipto->plant_code,
									'account_id' => '']);
						}

						CustomerTree::insert(['channel_code' => $channel->channel_code,
									'group_code' => $group->group_code,
									'area_code' => $area->area_code,
									'customer_code' => $distributor->customer_code,
									'plant_code' => '',
									'account_id' => '']);
					}
				}
			}
		}
	}
}