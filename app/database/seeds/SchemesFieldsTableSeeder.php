<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class SchemesFieldsTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('allocation_report_scheme_fields')->truncate();

		DB::statement("INSERT INTO allocation_report_scheme_fields (field_name,desc_name) VALUES
			('activities.eimplementation_date','START DATE'),
			('activities.end_date','END DATE'),
			('activities.billing_date','BILLING DATE'),
			('activities.cycle_desc','TOP CYCLE'),
			('activities.circular_name','ACTIVITY TITLE'),
			('activity_statuses.status','STATUS'),
			('activities.scope_desc','SCOPE'),
			('activities.proponent_name','PROPONENT'),
			('planner_tbl.planner_desc as planner_name','PLANNER'),
			('approver_tbl.approvers','APPROVERS'),
			('activities.activitytype_desc','ACTIVITY TYPE'),
			('divison_tbl.divisions','DIVISION'),
			('category_tbl.categories','CATEGORY'),
			('brands_tbl.brands','BRAND'),
			('schemes.name as scheme_name','SCHEME'),
			('scheme_skus.sku as ref_sku','REF SKU CODE'),
			('scheme_skus.sku_desc','REF SKU DESC'),
			('hostsku_tbl.hostsku_codes','HOST SKU CODE'),
			('hostsku_tbl.hostskus','HOST SKU DESC'),
			('premiumsku_tbl.premiumsku_codes','PREMIUM SKU CODE'),
			('premiumsku_tbl.premiumskus','PREMIUM SKU DESC'),
			('schemes.ulp_premium as non_ulp_premium','NON-ULP PREMIUM'),
			('schemes.item_code','ITEM CODE'),
			('schemes.item_barcode','BARCODE'),
			('schemes.item_casecode','CASECODE'),
			('schemes.srp_p as cost_of_premium','COST OF PREMIUM'),
			('schemes.other_cost as other_cost_per_deal','OTHER COST/DEAL'),
			('schemes.pr as purchase_requirement','SRP'),
			('schemes.ulp as total_unilever_cost','UNILEVER COST/DEAL'),
			('schemes.lpat as list_price_after_tax','LPAT'),
			('schemes.lpat/1.12 as list_price_before_tax','LPBT'),
			('schemes.cost_sale as cost_to_sale','COST TO SALES'),
			('allocations.group','GROUP'),
			('allocations.area','AREA'),
			('allocations.sold_to','SOLD TO'),
			('allocations.ship_to_code','SHIP TO CODE'),
			('allocations.ship_to','CUSTOMER SHIP TO NAME'),
			('allocations.channel','CHANNEL'),
			('allocations.outlet','ACCOUNT NAME'),
			('allocations.sold_to_gsv as sold_to_sales','SOLD TO SALES'),
			('allocations.sold_to_gsv_p as sold_to_sales_p','SOLD TO SALES PERCENTAGE'),
			('allocations.sold_to_alloc','SOLD TO ALLOC'),
			('allocations.ship_to_gsv as ship_to_sales','SHIP TO SALES'),
			('allocations.ship_to_gsv_p as ship_to_sales_p','SHIP TO SALES PERCENTAGE'),
			('allocations.ship_to_alloc','SHIP TO ALLOC'),
			('allocations.outlet_to_gsv as outlet_sales','ACCOUNT SALES'),
			('allocations.outlet_to_gsv_p as outlet_sales_p','ACCOUNT SALES PERCENTAGE'),
			('allocations.outlet_to_alloc as outlet_alloc','ACCOUNT ALLOC'),
			('activities.uom_desc','UOM'),
			('allocations.computed_alloc','INITIAL ALLOC'),
			('allocations.force_alloc','FORCED ALLOC'),
			('allocations.final_alloc','FINAL ALLOC'),
			('allocations.in_deals as no_of_deals','DEALS'),
			('allocations.in_cases as no_of_cases','CASES'),
			('allocations.tts_budget as tts_requirement','TTS BUDGET'),
			('allocations.pe_budget as pe_requirement','TTS BUDGET'),
			('allocations.pe_budget + allocations.pe_budget as total_cost','TOTAL COST');");

		DB::table('alloc_report_per_groups')->truncate();

		// insert admin
		$fields = DB::table('allocation_report_scheme_fields')->count();
		$admin_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			$admin_filter[] = array('role_id' => 1, 'filter_id' => $i);
		}
		AllocReportPerGroup::insert($admin_filter);

		// insert proponent
		$proponent_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 36) && ($i != 40) &&
				($i != 41) &&
				($i != 42) &&
				($i != 43) &&
				($i != 44) &&
				($i != 45) &&
				($i != 46) &&
				($i != 47) &&
				($i != 48) &&
				($i != 50) &&
				($i != 51) &&
				($i != 52)){
				$proponent_filter[] = array('role_id' => 2, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($proponent_filter);

		// insert pmog
		$pmog_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 36) && ($i != 40) &&
				($i != 41) &&
				($i != 42) &&
				($i != 43) &&
				($i != 44) &&
				($i != 45) &&
				($i != 46) &&
				($i != 47) &&
				($i != 48) &&
				($i != 50) &&
				($i != 51) &&
				($i != 52)){
				$pmog_filter[] = array('role_id' => 3, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($pmog_filter);

		// insert field
		$field_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 10) && ($i != 16) && ($i != 17) &&
				($i != 30) && ($i != 31) && ($i != 32) &&
				($i != 36) && ($i != 40) && ($i != 41) &&
				($i != 42) && ($i != 43) && ($i != 44) &&
				($i != 45) && ($i != 46) && ($i != 47) &&
				($i != 48) && ($i != 50) && ($i != 51) &&
				($i != 52) ){
				$field_filter[] = array('role_id' => 7, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($field_filter);

		// insert gcom
		$gcom_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 10) && ($i != 16) && ($i != 17) &&
				($i != 30) && ($i != 31) && ($i != 32) &&
				($i != 36) && ($i != 40) && ($i != 41) &&
				($i != 42) && ($i != 43) && ($i != 44) &&
				($i != 45) && ($i != 46) && ($i != 47) &&
				($i != 48) && ($i != 50) && ($i != 51) &&
				($i != 52) ){
				$gcom_filter[] = array('role_id' => 4, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($gcom_filter);

		// insert cdops
		$cdops_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 10) && ($i != 16) && ($i != 17) &&
				($i != 30) && ($i != 31) && ($i != 32) &&
				($i != 36) && ($i != 40) && ($i != 41) &&
				($i != 42) && ($i != 43) && ($i != 44) &&
				($i != 45) && ($i != 46) && ($i != 47) &&
				($i != 48) && ($i != 50) && ($i != 51) &&
				($i != 52) ){
				$cdops_filter[] = array('role_id' => 5, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($cdops_filter);

		// insert cmd
		$cmd_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 10) && ($i != 16) && ($i != 17) &&
				($i != 30) && ($i != 31) && ($i != 32) &&
				($i != 36) && ($i != 40) && ($i != 41) &&
				($i != 42) && ($i != 43) && ($i != 44) &&
				($i != 45) && ($i != 46) && ($i != 47) &&
				($i != 48) && ($i != 50) && ($i != 51) &&
				($i != 52) ){
				$cmd_filter[] = array('role_id' => 6, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($cmd_filter);
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');


	}

}