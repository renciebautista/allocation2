<?php

class ActivityRepository extends \Eloquent {
	protected $fillable = [];

	public static function generateActivityCode($activity,$scope,$cycle,$activity_type,$division_code,$category_code,$brand_code){
		$code = date('Y').$activity->id;
		if(!empty($scope)){
			$code .= '_'.$scope->scope_name;
		}
		if(!empty($cycle)){
			$code .= '_'.$cycle->cycle_name;
		}
		if(!empty($activity_type)){
			$code .= '_'.$activity_type->activity_type;
		}

		if(count($division_code) > 1){
			$code .= '_MULTI';
		}else{
			$division = Pricelist::select('division_code', 'division_desc')->where('division_code',$division_code)->first();
			if(!empty($division)){
				$code .= '_'.$division->division_desc;
			}
		}
		
		if(!empty($category_code)){
			if(count($category_code) > 1){
				$code .= '_MULTI';
			}else{
				$category = Pricelist::select('category_code', 'category_desc')->where('category_code',$category_code[0])->first();
				$code .= '_'.$category->category_desc;
			}
		}
		if(!empty($brand_code)){
			if(count($brand_code) > 1){
				$code .= '_MULTI';
			}else{
				$brand = Pricelist::select('brand_desc')->where('brand_desc',$brand_code[0])->first();
				$code .= '_'.$brand->brand_desc;
			}
		}

		return $code;
	}

	public static function addPlanner($activity){
		ActivityPlanner::where('activity_id',$activity->id)->delete();
		if(Input::has('planner')){
			$planner = Input::get('planner');
			$user = User::find($planner);
			if(!empty($user)){
				ActivityPlanner::insert(array('activity_id' => $activity->id, 
				'user_id' => $planner,
				'planner_desc' => $user->getFullname(),
				'contact_no' => $user->contact_no));
			}
			
		}
	}

	public static function addApprovers($activity,$cycle){
		ActivityApprover::where('activity_id',$activity->id)->delete();
		if (Input::has('approver')){
			$activity_planner = array();
			foreach (Input::get('approver') as $approver) {
				$user = User::find($approver);
				if(!empty($user)){
					$activity_approver[] = array('activity_id' => $activity->id, 
						'user_id' => $approver,
						'approver_desc' => $user->getFullname(),
						'contact_no' => $user->contact_no,
						'group_id' => $user->roles[0]->id);
				}
				
			}
			if(count($activity_approver)>0){
				ActivityApprover::insert($activity_approver);
			}
		}

		if($cycle->emergency){
			$cmd = User::GetPlanners(['CMD DIRECTOR']);
			if(!ActivityApprover::ApproverExist($activity->id,$cmd[0]->user_id)){
				$user = User::find($cmd[0]->user_id);
				if(!empty($user)){
					ActivityApprover::insert(array('activity_id' => $activity->id, 
					'user_id' => $cmd[0]->user_id,
					'approver_desc' => $user->getFullname(),
					'contact_no' => $user->contact_no,
					'group_id' => $user->roles[0]->id));
				}
				
			}
		}
	}

	public static function addDivisions($activity){
		ActivityDivision::where('activity_id',$activity->id)->delete();
		if (Input::has('division')){
			$activity_division = array();
			foreach (Input::get('division') as $division){
				$activity_division = Pricelist::division($division);
				$activity_divisions[] = array('activity_id' => $activity->id, 
					'division_code' => $division,
					'division_desc' => $activity_division->division_desc);
			}
			if(count($activity_divisions)>0){
				ActivityDivision::insert($activity_divisions);
			}
			
		}
	}

	public static function addCategories($activity){
		ActivityCategory::where('activity_id',$activity->id)->delete();
		if (Input::has('category')){
			$activity_categories = array();
			foreach (Input::get('category') as $category){
				$activitycategory = Pricelist::category($category);
				$activity_categories[] = array('activity_id' => $activity->id, 
					'category_code' => $category,
					'category_desc' => $activitycategory->category_desc);
			}
			if(count($activity_categories)>0){
				ActivityCategory::insert($activity_categories);
			}
			
		}
	}

	public static function addBrands($activity){
		ActivityBrand::where('activity_id',$activity->id)->delete();
		if (Input::has('brand')){
			$activity_brands = array();
			foreach (Input::get('brand') as $brand){

				$activity_brand = Pricelist::brand($brand);
				$activity_brands[] = array('activity_id' => $activity->id, 
					'brand_code' => $brand,
					'brand_desc' => $activity_brand->brand_desc.' - '.$activity_brand->cpg_desc,
					'b_desc' => $brand);
			}
			if(count($activity_brands)>0){
				ActivityBrand::insert($activity_brands);
			}
		}
	}

	public static function addSkus($activity){
		ActivitySku::where('activity_id',$activity->id)->delete();
		if(Input::has('skus')){
			$activity_skus = array();
			foreach (Input::get('skus') as $sku){
				$activitysku = Pricelist::getSku($sku);
				$activity_skus[] = array('activity_id' => $activity->id, 
					'sap_desc' => $activitysku->sap_desc. " - ".$activitysku->sap_code,
					'sap_code' => $sku);
			}
			if(count($activity_skus)>0){
				ActivitySku::insert($activity_skus);
			}
			
		}
	}

	public static function addObjectives($activity){
		ActivityObjective::where('activity_id',$activity->id)->delete();
		if (Input::has('objective')){
			$activity_objectives = array();
			foreach (Input::get('objective') as $objective){
				$activityobjective = Objective::find($objective);
				$activity_objectives[] = array('activity_id' => $activity->id, 
					'objective_id' => $objective,
					'objective_desc' => $activityobjective->objective);
			}
			if(count($activity_objectives)>0){
				ActivityObjective::insert($activity_objectives);
			}
		}
	}
}