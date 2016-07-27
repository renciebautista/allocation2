<?php

class Department extends \Eloquent {
	protected $fillable = ['department'];
	public static $rules = array(
        'department' => 'required|between:4,128|unique:departments'
    );

    public function users()
    {
        return $this->belongsToMany('User');
    }

    public static function search($filter){
		return self::where('department', 'LIKE' ,"%$filter%")
			->orderBy('department')
			->get();
	}

	public static function getLists(){
		return self::orderBy('department')->lists('department', 'id');
	}

	public static function withUsers($id){
		$user = DB::table('users')
			->where('department_id',$id)
			->get();
		if(count($users) > 0){
			return true;
		}
		return false;
	}
}