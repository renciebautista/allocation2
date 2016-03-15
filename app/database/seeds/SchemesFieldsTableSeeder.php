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
			('activity_statuses.status','STATUS'),
			('activities.scope_desc','SCOPE'),
			('activities.activitytype_desc','ACTIVITY TYPE'),
			('activities.circular_name','ACTIVITY TITLE'),
			('tts_budget.tts_io','TTS IO'),
			('pe_budget.pe_io','PE IO'),
			('activities.proponent_name','PROPONENT'),
			('planner_tbl.planner_desc as planner_name','PMOG PLANNER'),
			('approver_tbl.approvers','APPROVERS'),
			('divison_tbl.divisions','DIVISION'),
			('category_tbl.categories','CATEGORY'),
			('brands_tbl.brands','BRAND'),
			('schemes.name as scheme_name','SCHEME'),
			('scheme_skus.sku as ref_sku','REF SKU CODE'),
			('scheme_skus.sku_desc','REF SKU DESCRIPTION'),
			('hostsku_tbl.hostsku_codes','HOST SKU CODE'),
			('hostsku_tbl.hostskus','HOST SKU DESCRIPTION'),
			('premiumsku_tbl.premiumsku_codes','PREMIUM SKU CODE'),
			('premiumsku_tbl.premiumskus','PREMIUM SKU DESCRIPTION'),
			('schemes.ulp_premium as non_ulp_premium','NON-ULP PREMIUM'),
			('schemes.item_code','PROMO ITEM CODE'),
			('schemes.item_desc','PROMO ITEM DESCRIPTION'),
			('schemes.item_barcode','PROMO ITEM BARCODE'),
			('schemes.item_casecode','PROMO ITEM CASECODE'),
			('schemes.srp_p as cost_of_premium','COST OF PREMIUM (PHP)'),
			('schemes.other_cost as other_cost_per_deal','OTHER COST/DEAL (PHP)'),
			('schemes.pr as purchase_requirement','SRP (PHP)'),
			('schemes.ulp as total_unilever_cost','UNILEVER COST/DEAL (PHP)'),
			('schemes.lpat as list_price_after_tax','LPAT (PHP)'),
			('schemes.lpat/1.12 as list_price_before_tax','LPBT (PHP)'),
			('schemes.cost_sale as cost_to_sale','COST TO SALES %'),
			('allocations.group','GROUP (MT/DT)'),
			('allocations.area','AREA'),
			('allocations.sold_to','SOLD TO'),
			('allocations.ship_to_code','SHIP TO CODE'),
			('allocations.ship_to','CUSTOMER SHIP TO NAME'),
			('allocations.channel','CHANNEL'),
			('allocations.outlet','ACCOUNT NAME'),
			('allocations.sold_to_gsv as sold_to_sales','SOLD TO SALES'),
			('allocations.sold_to_gsv_p as sold_to_sales_p','SOLD TO % SHARE'),
			('allocations.sold_to_alloc','SOLD TO ALLOCATION'),
			('allocations.ship_to_gsv as ship_to_sales','SHIP TO SALES'),
			('allocations.ship_to_gsv_p as ship_to_sales_p','SHIP TO % SHARE'),
			('allocations.ship_to_alloc','SHIP TO ALLOCATION'),
			('allocations.outlet_to_gsv as outlet_sales','ACCOUNT SALES'),
			('allocations.outlet_to_gsv_p as outlet_sales_p','ACCOUNT % SHARE'),
			('allocations.outlet_to_alloc as outlet_alloc','ACCOUNT ALLOCATION'),
			('activities.uom_desc','UOM'),
			('allocations.computed_alloc','INITIAL ALLOCATION'),
			('allocations.force_alloc','FORCED ALLOCATION'),
			('allocations.final_alloc','FINAL ALLOCATION'),
			('allocations.in_deals as no_of_deals','ALLOCATION IN DEALS'),
			('allocations.in_cases as no_of_cases','ALLOCATION IN CASES'),
			('schemes.alloc_inweeeks as alloc_inweeeks','ALLOCATION IN WEEKS'),
			('allocations.tts_budget as tts_requirement','TTS BUDGET'),
			('allocations.pe_budget as pe_requirement','PE BUDGET'),
			('allocations.pepc_budget as pepc_equirement','PE-PC BUDGET'),
			('allocations.pe_budget + allocations.pe_budget as total_cost','TOTAL COST'),
			('activities.allow_force as forced_alloc','WITH FORCE ALLOC');");

		DB::table('alloc_report_per_groups')->truncate();

		
		$fields = DB::table('allocation_report_scheme_fields')->count();

		// insert admin
		$admin_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 58) && ($i != 61)){
				$admin_filter[] = array('role_id' => 1, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($admin_filter);

		// insert proponent
		$proponent_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 58) && ($i != 61)){
				$proponent_filter[] = array('role_id' => 2, 'filter_id' => $i);
			}
			
		}
		AllocReportPerGroup::insert($proponent_filter);

		// insert pmog
		$pmog_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 58) && ($i != 61)){
				$pmog_filter[] = array('role_id' => 3, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($pmog_filter);

		// insert gcom
		$gcom_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 58) && ($i != 61)
				&& ($i != 43)
				&& ($i != 44)
				&& ($i != 45)
				&& ($i != 46)
				&& ($i != 47)
				&& ($i != 48)
				&& ($i != 49)
				&& ($i != 50)
				&& ($i != 51)
				){
				$gcom_filter[] = array('role_id' => 4, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($gcom_filter);

		// insert cdops
		$cdops_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 58) && ($i != 61)
				&& ($i != 43)
				&& ($i != 44)
				&& ($i != 45)
				&& ($i != 46)
				&& ($i != 47)
				&& ($i != 48)
				&& ($i != 49)
				&& ($i != 50)
				&& ($i != 51)
				){
				$cdops_filter[] = array('role_id' => 5, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($cdops_filter);

		// insert cmd
		$cmd_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 58) && ($i != 61)
				&& ($i != 43)
				&& ($i != 44)
				&& ($i != 45)
				&& ($i != 46)
				&& ($i != 47)
				&& ($i != 48)
				&& ($i != 49)
				&& ($i != 50)
				&& ($i != 51)
				){
				$cmd_filter[] = array('role_id' => 6, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($cmd_filter);

		// insert field
		$field_filter = array();
		for ($i=1; $i <= $fields ; $i++) { 
			if(($i != 58) && ($i != 61)
				&& ($i != 43)
				&& ($i != 44)
				&& ($i != 45)
				&& ($i != 46)
				&& ($i != 47)
				&& ($i != 48)
				&& ($i != 49)
				&& ($i != 50)
				&& ($i != 51)
				&& ($i != 52)
				&& ($i != 53)
				&& ($i != 54)
				&& ($i != 55)
				){
				$field_filter[] = array('role_id' => 7, 'filter_id' => $i);
			}
		}
		AllocReportPerGroup::insert($field_filter);

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');


	}

}