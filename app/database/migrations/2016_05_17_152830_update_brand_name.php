<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateBrandName extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
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
		
	}

}
