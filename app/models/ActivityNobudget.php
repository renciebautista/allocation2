<?php

class ActivityNobudget extends \Eloquent {
	protected $fillable = [];

	public function budgettype()
    {
        return $this->belongsTo('BudgetType','budget_type_id','id');
    }
}