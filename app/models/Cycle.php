<?php

class Cycle extends \Eloquent {
	protected $fillable = [];

	public $timestamps = false;

	public static $rules = array(
		'cycle_name' => 'required',
		'month_year' => 'required',
		'submission_deadline' => 'required|date',
		'approval_deadline' => 'required|date',
		'pdf_deadline' => 'required|date',
		'release_date' => 'required|date',
		'implemintation_date' => 'required|date',
	);

	public static function search($filter){
		return self::where('cycles.cycle_name', 'LIKE' ,"%$filter%")
			->get();
	}

	public static function getLists(){
		return self::orderBy('cycle_name')->lists('cycle_name', 'id');
	}

	public static function getBySubmissionDeadline(){
		$now = date('Y-m-d');
		return DB::select(DB::raw("select * from cycles WHERE '$now' >=  (submission_deadline - interval 2 day) AND '$now' <= approval_deadline;"));
	}

	public static function getByApprovalDeadline(){
		$now = date('Y-m-d');
		return DB::select(DB::raw("select * from cycles WHERE '$now' >=  (approval_deadline - interval 3 day) AND '$now' <= approval_deadline;"));
	}

	public static function getByApprovalDeadlinePassed(){
		$now = date('Y-m-d');
		return DB::select(DB::raw("select * from cycles WHERE '$now' >=  (approval_deadline - interval 3 day) AND '$now' <= approval_deadline;"));
	}


	public static function getByPreApDeadline(){
		$today = date('Y-m-d');
		$deadline = date('Y-m-d', strtotime("{$today} + 1 day"));
		$records = self::select('id')
			->where('approval_deadline',$deadline)
			->get();
		$data = array();
		foreach ($records as $value) {
			$data[] = $value->id;
		}

		return $data;
	}

	public static function getByPostApDeadline(){
		$today = date('Y-m-d');
		$deadline = date('Y-m-d', strtotime("{$today} - 1 day"));
		$records = self::select('id')
			->where('approval_deadline',$deadline)
			->get();
		$data = array();
		foreach ($records as $value) {
			$data[] = $value->id;
		}

		return $data;
	}

	public static function getByReleaseDate(){
		$today = date('Y-m-d');
		// $deadline = date('Y-m-d', strtotime("{$today} - 1 day"));
		$records = self::select('id')
			->where('release_date',$today)
			->get();
		$data = array();
		foreach ($records as $value) {
			$data[] = $value->id;
		}

		return $data;
	}
}