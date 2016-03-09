<?php

class CustomerMasterController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /customermaster
	 *
	 * @return Response
	 */
	public function index()
	{
		$exports = CustomerMasterfile::all();
		return View::make('customermaster.index',compact('exports'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /customermaster/create
	 *
	 * @return Response
	 */
	public function export()
	{
		Artisan::call('export:sales');
		return Redirect::to(URL::action('CustomerMasterController@index'))
				->with('class', 'alert-success')
				->with('message', 'Export successfuly created');
	}


	public function download($id){
		$file = CustomerMasterfile::find($id);
		if(!empty($file)){
			$path = storage_path().'/exports/'.$file->filename;
			return Response::download($path, $file->filename);
		}else{
			echo 'File not found';
		}
	}
	
}