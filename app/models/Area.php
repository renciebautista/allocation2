<?php

class Area extends \Eloquent {
	protected $fillable = ['group_code', 'area_code', 'area_name'];
	public $timestamps = false;

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->group_code)){
				$attributes = array(
					'group_code' => $row->group_code,
					'area_code' => $row->area_code,
					'area_name' => $row->area_name);
				self::updateOrCreate($attributes, $attributes);
			}
			
		});
	}

	public static function getAreaWithGroup(){
		return self::join('groups','groups.group_code','=','areas.group_code')->get();
	}

	public static function getArea($area_code){
		return self::where('area_code',$area_code)
			->join('groups','groups.group_code','=','areas.group_code')
			->first();
	}
	

	public static function getAll(){
		return self::join('groups', 'groups.group_code','=', 'areas.group_code')
			->orderBy('areas.group_code', 'desc')
			->get();
	}

	public static function import($records){
		DB::beginTransaction();
			try {
			$records->each(function($row)  {
				if(!is_null($row->area_code)){
					$area = self::where('area_code',$row->area_code)
						->first();
					if(empty($area)){
						$area = new Area;
						$area->group_code = $row->group_code;
						$area->area_code = $row->area_code;
						$area->area_name = $row->area_name;
						$area->save();
					}else{
						$area->group_code = $row->group_code;
						$area->area_code = $row->area_code;
						$area->area_name = $row->area_name;
						$area->update();
					}
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			// dd($e);
			DB::rollback();
		}
	}
}