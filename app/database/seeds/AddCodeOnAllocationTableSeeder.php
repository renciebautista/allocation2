<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class AddCodeOnAllocationTableSeeder extends Seeder {

	public function run()
	{
		// update group
		$groups = Group::all();
		foreach ($groups as $group) {
			$affectedRows = Allocation::where('group',$group->group_name)->update(array('group_code' => $group->group_code));
			echo $affectedRows .PHP_EOL;
		}
		
		// update areas
		$areas = Area::all();
		foreach ($areas as $area) {
			$affectedRows = Allocation::where('area',$area->area_name)->update(array('area_code' => $area->area_code));
			echo $affectedRows .PHP_EOL;
		}

		// update customers
		$customers = Customer::groupBy('customer_name')->where('active',1)->get();
		foreach ($customers as $customer) {
			$affectedRows = Allocation::where('sold_to',$customer->customer_name)->update(array('sold_to_code' => $customer->customer_code));
			echo $affectedRows .PHP_EOL;
		}

		// update ship to
		$shiptos = ShipTo::groupBy('ship_to_name')->where('active',1)->get();
		foreach ($shiptos as $shipto) {
			if($shipto->ship_to_code != null){
				$affectedRows = Allocation::where('ship_to',$shipto->ship_to_name)
				->update(array('ship_to_code' => $shipto->ship_to_code));
				echo $affectedRows .PHP_EOL;
			}
		}

		// update channel
		$channels = Channel::all();
		foreach ($channels as $channel) {
			$affectedRows = Allocation::where('channel',$channel->channel_name)->update(array('channel_code' => $channel->channel_code));
			echo $affectedRows .PHP_EOL;
		}

		$othersRows = Allocation::where('outlet','OTHERS')->update(array('channel_code' => 'OTHERS','channel' => 'OTHERS'));
		echo $othersRows .PHP_EOL;

		// udpate account group
		$accountgroups = AccountGroup::all();
		foreach ($accountgroups as $accountgroup) {
			$affectedRows = Allocation::where('account_group_name',$accountgroup->account_group_name)->update(array('account_group_code' => $accountgroup->account_group_code));
			echo $affectedRows .PHP_EOL;
		}

		// update show
		$showRows = Allocation::whereNotNull('channel')->update(array('show' => true));
		echo $showRows .PHP_EOL;

		$allocations = Allocation::whereNull('customer_id')->whereNull('shipto_id')->orderBy('id')->get();
		foreach ($allocations as $allocation) {
			$total = Allocation::where('customer_id',$allocation->id)->get();

			if(count($total) == 0){
				echo $allocation->id .PHP_EOL;
				$alloc = Allocation::find($allocation->id);
				$alloc->show = true;
				$alloc->update();
			}

		}

		$_shiptos = Allocation::whereNotNull('customer_id')->whereNull('shipto_id')->orderBy('id')->get();
		foreach ($_shiptos as $_shipto) {
			$total = Allocation::where('shipto_id',$_shipto->id)->get();
			if(count($total) == 0){
				echo $_shipto->id .PHP_EOL;
				$alloc = Allocation::find($_shipto->id);
				$alloc->show = true;
				$alloc->update();
			}

		}
	}

}