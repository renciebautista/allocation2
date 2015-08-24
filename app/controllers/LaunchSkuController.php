<?php

class LaunchSkuController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /launchsku
	 *
	 * @return Response
	 */
	public function index()
	{
		$launchsku = Sku::where('launch',1)
			->where('active',1)
			->get();
		return View::make('launchsku.index',compact('launchsku'));
	}


	public function upload(){
		return View::make('launchsku.upload');
	}

	public function doupload(){
		$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
		$cnt = 0;
		Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
			$cnt = Sku::insertLaunch($reader->get());
		});
		return Redirect::action('LaunchSkuController@index')
				->with('class', 'alert-success')
				->with('message', 'Launch SKU/s successfuly updated');
	}

	public function access($id){
		$sku = Sku::getLaunchSku($id);
		if(!empty($sku)){	
			$proponents = User::GetPlanners(['PROPONENT']);
			$selecteduser = LaunchSkuAccess::selectedUser($id);
			return View::make('launchsku.access', compact('sku','proponents','selecteduser'));
		}else{
			return Redirect::action('LaunchSkuController@index')
				->with('class', 'alert-danger')
				->with('message', 'Sku not found!');
		}
		
	}
	/**
	 * Show the form for creating a new resource.
	 * GET /launchsku/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /launchsku
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /launchsku/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /launchsku/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /launchsku/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$sku = Sku::getLaunchSku($id);
		if(!empty($sku)){	
			LaunchSkuAccess::where('sku_code',$id)->delete();
			if(Input::has('proponent')){
				$user = array();
				foreach (Input::get('proponent') as $proponent) {
					$user[] = array('sku_code' => $id, 'user_id' => $proponent);
				}

				if(count($user) > 0){
					LaunchSkuAccess::insert($user);
				}
			}
			

			return Redirect::action('LaunchSkuController@access',$id)
				->with('class', 'alert-success')
				->with('message', 'Sku access successfuly updated.');
		}else{
			return Redirect::action('LaunchSkuController@index')
				->with('class', 'alert-danger')
				->with('message', 'Sku not found!');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /launchsku/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}