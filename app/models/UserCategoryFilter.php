<?php

class UserCategoryFilter extends \Eloquent {
	protected $fillable = [];

	public static function selected_category(){
		$categories = self::select('category_code')->where('user_id', Auth::id())->get();
		$_categories = array();
		if(!empty($categories)){
			foreach ($categories as $category) {
				$_categories[] = $category->category_code;
			}
		}
		return $_categories;
	}
}