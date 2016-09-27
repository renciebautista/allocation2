<?php

class DepartmentsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /departments
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$departments = Department::search(Input::get('s'));
		return View::make('departments.index',compact('departments'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /departments/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('departments.create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /departments
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		$validation = Validator::make($input, Department::$rules);

		if($validation->passes())
		{
			$department = new Department();
			$department->department = strtoupper(Input::get('department'));
			$department->save();

			return Redirect::route('departments.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::route('departments.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /departments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Redirect::route('departments.edit',$id);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /departments/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$department = Department::findOrFail($id);
		if (is_null($department))
		{
			return Redirect::route('departments.index')
				->with('class', 'alert-danger')
				->with('message', 'Record does not exist.');
		}
		return View::make('departments.edit', compact('department'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /departments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$rules = array(
	        'department' => 'required|between:4,128|unique:departments,department,'.$id
	    );
		$validation = Validator::make($input,$rules);
		if ($validation->passes())
		{
			$department = Department::findOrFail($id);
			if (is_null($department))
			{
				return Redirect::route('departments.index')
					->with('class', 'alert-danger')
					->with('message', 'Record does not exist.');
			}
			$department->department = strtoupper(Input::get('department'));
			$department->save();
			return Redirect::route('departments.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly updated.');
		}

		return Redirect::route('departments.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /departments/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$department = Department::findOrFail($id);
		if (is_null($department))
		{
			$class = 'alert-danger';
			$message = 'Department does not exist.';
		}else{
			if(!Department::withUsers($department->id)){
				$department->delete();
				$class = 'alert-success';
				$message = 'Role successfully deleted.';
			}else{
				$class = 'alert-danger';
				$message = 'Department is already attached to a user.';
			}
			
		}
		return Redirect::route('departments.index')
				->with('class', $class )
				->with('message', $message);
	}

	public function export(){
		$departments = Department::all();
		Excel::create("Department", function($excel) use($departments){
			$excel->sheet('Sheet1', function($sheet) use($departments) {
				$sheet->fromModel($departments,null, 'A1', true);

			})->download('xls');
		});
	}

	public function upload(){
		return View::make('departments.upload');
	}

	public function uploaddepartment(){
		if(Input::hasFile('file')){
			$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
			Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
				Department::import($reader->get());
			});


			if (File::exists($file_path))
			{
			    File::delete($file_path);
			}
			
			return Redirect::action('DepartmentsController@index')
					->with('class', 'alert-success')
					->with('message', 'Department list successfuly updated');
		}else{

			return Redirect::action('DepartmentsController@upload')
				->with('class', 'alert-danger')
				->with('message', 'A file upload is required.');
		}
		
	}

}