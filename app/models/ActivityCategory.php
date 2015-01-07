<?php

class ActivityCategory extends \Eloquent {
	protected $fillable = [];

	public static function selected_category($id){
		$categories = self::select('category_code')->where('activity_id', $id)->get();
		$_categories = array();
		if(!empty($categories)){
			foreach ($categories as $category) {
				$_categories[] = $category->category_code;
			}
		}
		return $_categories;
	}
}