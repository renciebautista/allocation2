<?php

class SubTask extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
    	'task' => 'required|integer|min:1',
		'subtask' => 'required',
		'lead_time' => 'required|integer|min:1'
	);



	public static function search($task,$search){
		return self::select('sub_tasks.id', 'tasks.task', 'sub_tasks.sub_task', 'departments.department', 'lead_time')
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
}