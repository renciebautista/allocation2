<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class SchemesFieldsTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('allocation_report_scheme_fields')->truncate();

		DB::statement("INSERT INTO allocation_report_scheme_fields (field_name,desc_name) VALUES
			('cycle_desc','Cycle Name'),
			('circular_name',' Circular Name'),
			('status','Status'),
			('scope_desc','Scope Type'),
			('proponent_name','Proponent'),
			('planner_name','Planner'),
			('approvers','Approvers'),
			('activitytype_desc','Activity Type'),
			('divisions','Division'),
			('categories','Category'),
			('brands','Brand'),
			('scheme_name','Scheme Name'),
			('sku_desc','Scheme Reference SKU/s'),
			('hostskus','Scheme Host SKU/s'),
			('premiumskus','Scheme Premium SKU/s'),
			('non_ulp_premium','Scheme Non-ULP Premium SKU/s'),
			('item_code','Scheme Item Code'),
			('item_barcode','Scheme Item Barcode'),
			('item_casecode','Scheme Item Casecode'),
			('cost_of_premium','Scheme Cost of Premium (Php)'),
			('other_cost_per_deal','Scheme Other Cost Per Deal (Php)'),
			('purchase_requirement','Scheme Purchase Requirement (Php)'),
			('total_unilever_cost','Scheme Total Unilever Cost (Php)'),
			('list_price_after_tax','Scheme List Price After Tax Per Deal (Php)'),
			('list_price_before_tax','Scheme List Price Before Tax Per Deal (Php)'),
			('cost_to_sale','Scheme Cost to Sales %'),
			('group','Scheme Group'),
			('area','Scheme Area'),
			('sold_to','Scheme Sold To'),
			('ship_to','Scheme Ship To'),
			('channel','Scheme Channel'),
			('outlet','Scheme Outlet'),
			('sold_to_sales','Scheme Sold To Sales'),
			('sold_to_sales_p','Scheme Sold To Sales Percentage'),
			('sold_to_alloc','Scheme Sold To Allcoation'),
			('ship_to_sales','Scheme Ship To Sales'),
			('ship_to_sales_p','Scheme Ship To Sales Percentage'),
			('ship_to_alloc','Scheme Ship To Allcoation'),
			('outlet_sales','Scheme Outlet Sales'),
			('outlet_sales_p','Scheme Outlet Sales Percentage'),
			('outlet_alloc','Scheme Outlet Allocation'),
			('uom_desc','Scheme Unit of Measurement'),
			('computed_alloc','Scheme Computed Allocation'),
			('force_alloc','Scheme Forced Allocation'),
			('final_alloc','Scheme Final Allocation'),
			('no_of_deals','Scheme No. of Deals'),
			('no_of_cases','Scheme No. of Cases'),
			('tts_requirement','Scheme TTS Requirement (Php)'),
			('pe_requirement','Scheme PE Requirement (Php)'),
			('total_cost','Scheme Total Cost (Php)');");
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}