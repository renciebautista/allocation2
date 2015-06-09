<?php
namespace Api;
class ChannelController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /api/network
	 *
	 * @return Response
	 */
	public function index()
	{
		$channels = \Channel::orderBy('id')->get();
		$data = array();
		foreach ($channels as $channel) {
			$subgroups = \Account::getChannelGroup($channel->channel_code);
			$group_children = array();
			foreach ($subgroups as $subgroup) {
				$group_children[] = array(
					'title' => $subgroup->account_group_name,
					'key' => $channel->channel_code.".".$subgroup->account_group_code,
					);
			}
			
			$data[] = array(
				'title' => $channel->channel_name,
				'isFolder' => true,
				'key' => $channel->channel_code,
				'children' => $group_children
				);
		}
		return \Response::json($data,200);
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /api/network/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /api/network
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /api/network/{id}
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
	 * GET /api/network/{id}/edit
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
	 * PUT /api/network/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /api/network/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}