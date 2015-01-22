<?php

class ActivityCustomer extends \Eloquent {
	protected $fillable = [];

	public static function customers($id){
		$customers = array();
		foreach(self::where('activity_id',$id)->get() as $customer){
			$customers[] = $customer->customer_node;
		}

		return $customers;
	}
}