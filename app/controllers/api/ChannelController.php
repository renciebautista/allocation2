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

			// mpk
			// if(count($subgroups)>0){
			// 	foreach ($subgroups as $subgroup) {
			// 		$group_children[] = array(
			// 			'title' => $subgroup->account_group_name,
			// 			'key' => $channel->channel_code.".".$subgroup->account_group_code,
			// 			);
			// 	}
			// 	$group_children[] = array(
			// 			'title' => 'OTHERS',
			// 			'key' => $channel->channel_code.".OTHERS",
			// 			);
			// }
			// end
			
			
			$data[] = array(
				'title' => $channel->channel_name,
				'isFolder' => true,
				'key' => $channel->channel_code,
				'children' => $group_children
				);
		}
		return \Response::json($data,200);
	}

	public function channelselected(){
		$id = \Input::get('id');
		$data = array();
		$sel = \ActivityChannel2::where('activity_id',$id)->get();
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row->channel_node;
			}
		}
		return \Response::json($data,200);
	}


	public function getpostedchannels(){
		$id = \Input::get('id');
		
		$channels = \ActivityChannelList::where('activity_id',$id)
			->whereNull('parent_id')
			->orderBy('id')
			->get();

		$data = array();
		foreach ($channels as $channel) {
			$subgroups = \ActivityChannelList::where('activity_id',$id)
				->where('parent_id', $channel->key)
				->orderBy('id')
				->get();
			$channel_children = array();
			foreach ($subgroups as $subgroup) {
				$channel_children[] = array(
				'title' => $subgroup->title,
				'isFolder' => $subgroup->isfolder,
				'key' => $subgroup->key,
				'unselectable' => $subgroup->unselectable,
				'selected' => $subgroup->selected,
				// 'children' => $channel_children
				);
			}
			$data[] = array(
				'title' => $channel->title,
				'isFolder' => $channel->isfolder,
				'key' => $channel->key,
				'unselectable' => $channel->unselectable,
				'selected' => $channel->selected,
				'children' => $channel_children
				);
		}
		return \Response::json($data,200);
	}

}