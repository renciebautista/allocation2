<?php

class Week extends \Eloquent {
	protected $fillable = [];

	public static function getDays(){
		return ['0' => 'SUNDAY','1' => 'MONDAY','2' => 'TUESDAY', '3' => 'WEDNESDAY','4' => 'THURSDAY','5' => 'FRIDAY','6' => 'SATURDAY'];
	}
}