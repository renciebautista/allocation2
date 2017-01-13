<?php

class SubTask extends \Eloquent {
	protected $fillable = ['task_id', 'sub_task', 'lead_time', 'weight', 'cost', 'department_id'];

	public static $rules = array(
    	'task' => 'required|integer|min:1',
		'subtask' => 'required',
		'lead_time' => 'required|integer|min:1'
	);



	public static function search($task,$search){
		return self::select('sub_tasks.id', 'tasks.task', 'sub_tasks.sub_task', 'departments.department', 'lead_time', 'cost', 'weight')
			->join('tasks', 'tasks.id', '=', 'sub_tasks.task_id')
			->join('departments', 'departments.id', '=', 'sub_tasks.department_id')
			->where(function($query) use ($search){
				$query->where('sub_tasks.sub_task', 'LIKE' ,"%$search%")
					->orwhere('tasks.task', 'LIKE' ,"%$search%");
			})
			->where(function($query) use ($task){
				if($task > 0){
					$query->where('task_id', $task);
				}
			})
			->orderBy('task_id')
			->orderBy('sub_tasks.id')
			->get();
	}

	public static function import($records){
		DB::beginTransaction();
			try {
			$records->each(function($row)  {
				if(!is_null($row->task)){
					$task = Task::firstOrCreate(['task' => $row->task]);
					$department = Department::firstOrCreate(['department' => $row->department]);
					
					$subtask = self::where('task_id', $task->id)
						->where('sub_task', $row->subtask)
						->first();
					if(empty($subtask)){
						self::create(['task_id' => $task->id,
							'sub_task' => $row->subtask,
							'lead_time' => $row->lead_time,
							'weight' => $row->weight,
							'cost' => $row->cost,
							'department_id' => $department->id]);
					}else{
						$subtask->lead_time = $row->lead_time;
						$subtask->weight = $row->weight;
						$subtask->cost = $row->cost;
						$subtask->department_id = $department->id;
						$subtask->update();
					}
				}
				
			});
			DB::commit();
		} catch (\Exception $e) {
			dd($e);
			DB::rollback();
		}
	}
}