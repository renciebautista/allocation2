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
		Input::flash();
		// $launchskus = Pricelist::where('launch',1)->where('active',1)->get();
		$launchskus = Pricelist::search(Input::get('s'));
		foreach ($launchskus as $sku) {
			$users = LaunchSkuAccess::select(DB::raw('CONCAT(first_name, " ", last_name) AS full_name'))
				->where('sku_code',$sku->sap_code)
				->join('users', 'users.id', '=', 'launch_sku_access.user_id')
				->get();
			$data = array();
			foreach ($users as $user) {
				$data[] = $user->full_name;
			}
			$sku->users = implode(", ", $data);
		}
		$proponents = User::getApprovers(['PROPONENT']);
		return View::make('launchsku.index',compact('launchskus','proponents'));
	}


	public function upload(){
		return View::make('launchsku.upload');
	}

	public function export(){
		$data = ['cpg_code', 'cpg_desc', 'sap_code', 'sap_desc', 'division_code', 'division_desc',
			'category_code', 'category_desc', 'brand_code', 'brand_desc', 'brand_shortcut', 'pack_size',
			'barcode', 'case_code', 'price_case', 'price_case_tax', 'price', 'srp'];

		Excel::create("Laucnh SKU/s Template", function($excel) use($data){
			$excel->sheet('Sheet1', function($sheet) use($data) {
				$sheet->fromModel($data,null, 'A1', true);

			})->download('xls');

		});
	}

	public function doupload(){
		$file_path = Input::file('file')->move(storage_path().'/uploads/temp/',Input::file('file')->getClientOriginalName());
		$cnt = 0;
		Excel::selectSheets('Sheet1')->load($file_path, function($reader) {
			$cnt = Pricelist::insertLaunch($reader->get());
		});
		return Redirect::action('LaunchSkuController@index')
				->with('class', 'alert-success')
				->with('message', 'Launch SKU/s successfuly updated');
	}

	public function assignaccess(){
		if(Request::ajax()){
			$skus = Input::get('skus');
			$users = Input::get('users');
			if((!empty($skus)) && (!empty($users))){
				$data = array();
				foreach ($users as $user) {
					foreach ($skus as $sku) {
						$skuaccess = LaunchSkuAccess::where('sku_code',$sku)
							->where('user_id',$user)
							->first();
						if(empty($skuaccess)){
							LaunchSkuAccess::insert(array('sku_code' => $sku, 'user_id' => $user));
						}
					}
				}
				Session::flash('class', 'alert-success');
				Session::flash('message', 'User sku access is successfuly udpated.');

				return Response::json(array('success' => 1));
			}else{
				Session::flash('class', 'alert-danger');
				Session::flash('message', 'Error updating user sku access!');
				return Response::json(array('success' => 0));
			}
		}
	}

	public function removeaccess(){
		if(Request::ajax()){
			$skus = Input::get('skus');
			$users = Input::get('users');
			if((!empty($skus)) && (!empty($users))){
				$data = array();
				foreach ($users as $user) {
					foreach ($skus as $sku) {
						LaunchSkuAccess::where('sku_code',$sku)
							->where('user_id',$user)->delete();
					}
				}
				Session::flash('class', 'alert-success');
				Session::flash('message', 'User sku access is successfuly udpated.');

				return Response::json(array('success' => 1));
			}else{
				Session::flash('class', 'alert-danger');
				Session::flash('message', 'Error updating user sku access!');
				return Response::json(array('success' => 0));
			}
		}
	}

	public function removeskus(){
		if(Request::ajax()){
			$skus = Input::get('skus');
			if(!empty($skus)){
				foreach ($skus as $sku) {
					if(ActivitySku::alreadyUsed($sku) || SchemeHostSku::alreadyUsed($sku) || SchemePremuimSku::alreadyUsed($sku)){
						Session::flash('class', 'alert-danger');
						Session::flash('message', "Error deleting sku code '".$sku. "', already used in a transaction.' ");
						return Response::json(array('success' => 0));
					}else{
						LaunchSkuAccess::where('sku_code',$sku)->delete();
						Pricelist::where('sap_code',$sku)->delete();
						Session::flash('class', 'alert-success');
						Session::flash('message', 'Launch SKU/s is successfuly updated.');

						return Response::json(array('success' => 1));
					}

				}
				
			}else{
				Session::flash('class', 'alert-danger');
				Session::flash('message', 'Error deleting SKU/s!');
				return Response::json(array('success' => 0));
			}
		}
	}


		
	// }
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
		// var_dump(Input::all());
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
		// $sku = Sku::getLaunchSku($id);
		// if(!empty($sku)){	
		// 	LaunchSkuAccess::where('sku_code',$id)->delete();
		// 	if(Input::has('proponent')){
		// 		$user = array();
		// 		foreach (Input::get('proponent') as $proponent) {
		// 			$user[] = array('sku_code' => $id, 'user_id' => $proponent);
		// 		}

		// 		if(count($user) > 0){
		// 			LaunchSkuAccess::insert($user);
		// 		}
		// 	}
			

		// 	return Redirect::action('LaunchSkuController@access',$id)
		// 		->with('class', 'alert-success')
		// 		->with('message', 'Sku access successfuly updated.');
		// }else{
		// 	return Redirect::action('LaunchSkuController@index')
		// 		->with('class', 'alert-danger')
		// 		->with('message', 'Sku not found!');
		// }
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
		
	}

}