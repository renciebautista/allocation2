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

	public static function batchInsert($records){
		$records->each(function($row) {
			if(!is_null($row->cycle_name)){

				$cycle = self::where('cycle_name',$row->cycle_name)->first();
				if(!empty($cycle)){
					$cycle->cycle_name = $row->cycle_name;
					$cycle->month_year = date_format(date_create($row->month_year),'m/Y');
					$cycle->submission_deadline = $row->submission_deadline;
					$cycle->release_date = $row->release_date;
					$cycle->implemintation_date = $row->implemintation_date;
					$cycle->emergency = ($row->emergency == 'Y') ? 1 : 0;
					$cycle->approval_deadline = $row->approval_deadline;
					$cycle->pdf_deadline = $row->pdf_deadline;
					$cycle->update();
				}else{
					$cycle = new Cycle;
					$cycle->cycle_name = $row->cycle_name;
					$cycle->month_year = date_format(date_create($row->month_year),'m/Y');
					$cycle->submission_deadline = $row->submission_deadline;
					$cycle->release_date = $row->release_date;
					$cycle->implemintation_date = $row->implemintation_date;
					$cycle->emergency = ($row->emergency == 'Y') ? 1 : 0;
					$cycle->approval_deadline = $row->approval_deadline;
					$cycle->pdf_deadline = $row->pdf_deadline;
					$cycle->save();
				}

			}
			
		});
	}

	public static function search($filter){
		return self::where('cycles.cycle_name', 'LIKE' ,"%$filter%")
			->orderBy('release_date')
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
		return DB::select(DB::raw("select * from cycles WHERE '$now' = (approval_deadline + interval 1 day);"));
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
		return self::where('release_date',$today)->get();
	}

	public static function getReleasedCycles($filter){
		return DB::table('activities')
                 ->select('cycles.cycle_name', 'cycles.id',DB::raw('count(activities.id) as total'))
                 ->join('cycles','activities.cycle_id','=','cycles.id')
                 ->where('cycles.cycle_name', 'LIKE' ,"%$filter%")
                 ->where('activities.status_id',9)
                 ->groupBy('activities.cycle_id')
                 ->orderBy('cycles.release_date')
                 ->get();
	}

	public static function getAllCycles($filter){
		return DB::table('activities')
                 ->select('cycles.cycle_name', 'cycles.id',DB::raw('count(activities.id) as total'))
                 ->join('cycles','activities.cycle_id','=','cycles.id')
                 ->where('cycles.cycle_name', 'LIKE' ,"%$filter%")
                 ->groupBy('activities.cycle_id')
                 ->orderBy('cycles.release_date')
                 ->get();
	}
}