<?php
namespace Api;

class SubTaskController extends \BaseController {

	
	public function getsubtask(){
		if(\Request::ajax()){
			$task_id = \Input::get('task');
			$task = \Task::find($task_id);
			$data['subtasks'] = [];
			if(!empty($task)){
				$data['subtasks'] = \SubTask::where('task_id', $task->id)->lists('sub_task', 'id');
			}
			return \Response::json($data,200);
		}
	}
}