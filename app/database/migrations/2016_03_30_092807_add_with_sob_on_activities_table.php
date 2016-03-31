<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddWithSobOnActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->boolean('with_sob')->after('disable');
		});

		$activities = Activity::all();
		foreach ($activities as $activity) {
			if($activity->activitytype->with_sob == 1){
				$activity->with_sob = 1;
				$activity->update();
			}
		}

		$activities_with_sob = Activity::where('with_sob',1)->get();
		foreach ($activities_with_sob as $activity) {
			$schemes = Scheme::where('activity_id',$activity->id)->get();
			foreach ($schemes as $scheme) {
				if($scheme->brand_desc != ''){

					$item = Pricelist::where('brand_code',$scheme->brand_code)
						->where('brand_desc',$scheme->brand_desc)
						->first();

					$scheme->brand_shortcut = $item->brand_shortcut;
					$scheme->scategory = $item->category_desc;
					$scheme->scategory_code = $item->category_code;
					$scheme->sdivision = $item->division_desc;
					$scheme->sdivision_code = $item->division_code;
					$scheme->update();
				}
			}
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('activities', function(Blueprint $table)
		{
			$table->dropColumn(['with_sob']);
		});
	}

}
