<?php

class AllocationReport extends \Eloquent {
	protected $fillable = [];

	private static function generateQuery($data_field,$field,$isset = false){
		$query = "";
		if(count($data_field) > 0){
			if($isset){
				if(count($data_field) > 1){			
					$query_list= implode("|", $data_field);
				}else{
					$query_list = $data_field[0];
				}
				// WHERE CONCAT(",", `setcolumn`, ",") REGEXP ",(val1|val2|val3),"'
				$query = ' AND CONCAT(",", '.$field.', ",") REGEXP ",('.$query_list.'),"';
			}else{
				if(count($data_field) > 1){			
					$query_list= '"'.implode('","', $data_field).'"';
					$query = " AND ".$field." IN (".$query_list.")";
				}else{
					$query = ' AND '.$field.' = "'.$data_field[0].'"';
				}
			}
		}
		return $query;
	}

	public static function getReport($data,$take,$counter,$user){
		$cycles = "";
		$status = "";
		$scopes = "";
		$proponents = "";
		$planners = "";
		$approvers = "";
		$activitytypes = "";
		$divisions = "";
		$categories = "";
		$brands = "";
		$groups = "";
		$areas = "";
		$soldtos = "";
		$shiptos = "";
		$outlets = "";
		$channels = "";
		$fields = "";
		if(count($data['cycles']) > 1){			
			$query_list= '"'.implode('","', $data['cycles']).'"';
			$cycles = " AND activities.cycle_id IN (".$query_list.")";
		}else{
			$cycles = ' AND activities.cycle_id = "'.$data['cycles'][0].'"';
		}

		if($user->inRoles(['FIELD SALES','CMD DIRECTOR','CD OPS APPROVER','GCOM APPROVER'])){
			$status = ' AND activities.status_id = "9"';
		}else{
			if(!empty($data['status'])){
				$status = self::generateQuery($data['status'],"activities.status_id");
			}
		}	
		
		if(!empty($data['scopes'])){
			$scopes = self::generateQuery($data['scopes'],"activities.scope_type_id");
		}

		if($user->inRoles(['PROPONENT'])){
			$proponents =  sprintf(' AND activities.created_by = "%s"',$user->id);
		}else{
			if(!empty($data['proponents'])){
				$proponents = self::generateQuery($data['proponents'],"activities.created_by");
			}
		}

		if($user->inRoles(['PMOG PLANNER'])){
			$planners = sprintf(' AND planner_tbl.user_id = "%s"',$user->id);
		}else{
			if(!empty($data['planners'])){
				$planners = self::generateQuery($data['planners'],"planner_tbl.user_id");
			}
		}
		
		
		if(!empty($data['approvers'])){
			$approvers = self::generateQuery($data['approvers'],"approver_tbl.approver_ids",true);
		}
		if(!empty($data['activitytypes'])){
			$activitytypes = self::generateQuery($data['activitytypes'],"activities.activity_type_id");
		}
		if(!empty($data['divisions'])){
			$divisions = self::generateQuery($data['divisions'],"divison_tbl.division_codes",true);
		}
		if(!empty($data['categories'])){
			$categories = self::generateQuery($data['categories'],"category_tbl.category_codes",true);
		}
		if(!empty($data['brands'])){
			$brands = self::generateQuery($data['brands'],"brands_tbl.brand_codes",true);
		}

		if(!empty($data['outlets'])){
			$outlets = self::generateQuery($data['outlets'],"allocations.outlet");
		}
		if(!empty($data['channels'])){
			$channels = self::generateQuery($data['channels'],"allocations.channel_code");
		}

		$_grps = array();
		$_areas = array();
		$_cust = array();
		$_shp = array();
		if(!empty($data['customers'])){
			foreach ($data['customers'] as $selected_customer) {
				$_selected_customer = explode(".", $selected_customer);
				if(count($_selected_customer)>0){
					$_grps[] = $_selected_customer[0];
				}else{
					$_grps[] = $selected_customer;
				}
				
				if(!empty($_selected_customer[1])){
					$_areas[] = $_selected_customer[1];
				}

				if(!empty($_selected_customer[2])){
					$_cust[] = $_selected_customer[2];
				}

				if(!empty($_selected_customer[3])){
					$_shp[] = $_selected_customer[3];
				}
			}
		}

		if(!empty($_grps)){
			$groups = self::generateQuery($_grps,"allocations.group_code");
		}

		if(!empty($_areas)){
			$areas = self::generateQuery($_areas,"allocations.area_code");
		}

		if(!empty($_cust)){
			$soldtos = self::generateQuery($_cust,"allocations.sold_to_code");
		}
		
		if(!empty($_shp)){
			$shiptos = self::generateQuery($_shp,"allocations.ship_to_code");
		}

		if(!empty($data['fields'])){
			$fieldList = array();
			foreach ($data['fields'] as $field) {
				$fieldList[] = $field->field_name;
			}

			$fields .= implode(",",$fieldList);
		}

		// allocations.id as alloc_id, allocations.customer_id as alloc_cid, allocations.shipto_id as alloc_sid, 
		// activities.cycle_id, activities.cycle_desc,
		// activities.circular_name,activities.id as activity_id,
		// activities.status_id, activity_statuses.status,
		// activities.scope_type_id,activities.scope_desc,
		// activities.created_by as proponent_user_id,activities.proponent_name,
		// planner_tbl.user_id as planner_user_id,planner_tbl.planner_desc as planner_name,
		// approver_tbl.approver_ids,approver_tbl.approvers,
		// activities.activity_type_id,activities.activitytype_desc,
		// divison_tbl.division_codes,divison_tbl.divisions,
		// category_tbl.category_codes,category_tbl.categories,
		// brands_tbl.brand_codes,brands_tbl.brands,
		// schemes.name as scheme_name,
		// scheme_skus.sku as ref_sku, scheme_skus.sku_desc,
		// hostsku_tbl.hostsku_codes,hostsku_tbl.hostskus,
		// premiumsku_tbl.premiumsku_codes,premiumsku_tbl.premiumskus,
		// schemes.ulp_premium as non_ulp_premium,
		// schemes.item_code,schemes.item_barcode,schemes.item_casecode,
		// schemes.srp_p as cost_of_premium,
		// schemes.other_cost as other_cost_per_deal,
		// schemes.pr as purchase_requirement,
		// schemes.ulp as total_unilever_cost,
		// schemes.lpat as list_price_after_tax,
		// schemes.lpat/1.12 as list_price_before_tax,
		// schemes.cost_sale as cost_to_sale,
		// allocations.group_code,allocations.group,
		// allocations.area_code,allocations.area,
		// allocations.sold_to_code,allocations.sold_to,
		// allocations.ship_to_code,allocations.ship_to,
		// allocations.channel_code,allocations.channel,
		// allocations.outlet,
		// allocations.sold_to_gsv as sold_to_sales,allocations.sold_to_gsv_p as sold_to_sales_p,allocations.sold_to_alloc,
		// allocations.ship_to_gsv as ship_to_sales,allocations.ship_to_gsv_p as ship_to_sales_p,allocations.ship_to_alloc,
		// allocations.outlet_to_gsv as outlet_sales,allocations.outlet_to_gsv_p as outlet_sales_p,allocations.outlet_to_alloc as outlet_alloc,
		// activities.uom_desc,
		// allocations.computed_alloc,allocations.force_alloc,allocations.final_alloc,
		// allocations.in_deals as no_of_deals, allocations.in_cases as no_of_cases,
		// allocations.tts_budget as tts_requirement,
		// allocations.pe_budget as pe_requirement,
		// allocations.pe_budget + allocations.pe_budget as total_cost


	
		$query = sprintf("SELECT %s
			FROM 
			allocations
			LEFT JOIN schemes on allocations.scheme_id = schemes.id 
			LEFT JOIN activities on schemes.activity_id = activities.id
			LEFT JOIN activity_statuses on activities.status_id = activity_statuses.id
			LEFT JOIN (
				SELECT user_id,planner_desc,activity_id FROM activity_planners
			) as planner_tbl ON activities.id = planner_tbl.activity_id
			LEFT JOIN (
				SELECT activity_id,
				GROUP_CONCAT(CONCAT(activity_approvers.user_id)) as approver_ids,
			    GROUP_CONCAT(CONCAT(activity_approvers.approver_desc)) as approvers
				FROM activity_approvers  
				GROUP BY activity_id
			) as approver_tbl ON activities.id = approver_tbl.activity_id
			LEFT JOIN (
				SELECT activity_id,
			    GROUP_CONCAT(CONCAT(activity_divisions.division_code)) as division_codes,
				GROUP_CONCAT(CONCAT(activity_divisions.division_desc)) as divisions
				FROM activity_divisions  
				GROUP BY activity_id
			)as divison_tbl ON activities.id = divison_tbl.activity_id
			LEFT JOIN (
				SELECT activity_id,
			    GROUP_CONCAT(CONCAT(activity_categories.category_code)) as category_codes,
				GROUP_CONCAT(CONCAT(activity_categories.category_desc)) as categories
				FROM activity_categories 
				GROUP BY activity_id
			)as category_tbl ON activities.id = category_tbl.activity_id
			LEFT JOIN (
			SELECT activity_id,
				GROUP_CONCAT(CONCAT(activity_brands.brand_code)) as brand_codes,
				GROUP_CONCAT(CONCAT(activity_brands.brand_desc)) as brands
				FROM activity_brands 
				GROUP BY activity_id
			) as brands_tbl ON activities.id = brands_tbl.activity_id
			LEFT JOIN scheme_skus on schemes.id = scheme_skus.scheme_id
			LEFT JOIN (
			SELECT scheme_id,
				GROUP_CONCAT(CONCAT(scheme_host_skus.sap_code)) as hostsku_codes,
				GROUP_CONCAT(CONCAT(scheme_host_skus.sap_desc)) as hostskus
				FROM scheme_host_skus 
				GROUP BY scheme_id
			) as hostsku_tbl ON schemes.id = hostsku_tbl.scheme_id
			LEFT JOIN (
			SELECT scheme_id,
				GROUP_CONCAT(CONCAT(scheme_premuim_skus.sap_code)) as premiumsku_codes,
				GROUP_CONCAT(CONCAT(scheme_premuim_skus.sap_desc)) as premiumskus
				FROM scheme_premuim_skus 
				GROUP BY scheme_id
			) as premiumsku_tbl ON schemes.id = premiumsku_tbl.scheme_id
			WHERE allocations.show = 1
			%s %s %s %s %s %s %s %s %s %s %s %s %s %s %s %s ORDER BY allocations.id LIMIT %s,%s ",
			$fields,
			$cycles,
			$status,$scopes,$proponents,$planners,$approvers,
			$activitytypes,$divisions,$categories,$brands,
			$groups,$areas,$soldtos,$shiptos,$channels,$outlets,$counter,$take);
	// var_dump($query);
		return DB::select(DB::raw($query));
	}

	
}