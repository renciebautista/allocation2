<?php

class SubTask extends \Eloquent {
	protected $fillable = [];

	public static $rules = array(
    	'task' => 'required|integer|min:1',
		'subtask' => 'required'
	);

	public static function search($task,$search){
		return self::select('sub_tasks.id', 'tasks.task', 'sub_tasks.sub_task')
			->join('tasks', 'tasks.id', '=', 'sub_tasks.task_id')
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