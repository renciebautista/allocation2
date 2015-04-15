<?php

class ScopeType extends \Eloquent {
	protected $fillable = [];

	public static function getLists(){
		return self::orderBy('scope_name')->lists('scope_name', 'id');
	}
}