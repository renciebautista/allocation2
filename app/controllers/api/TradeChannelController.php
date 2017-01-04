<?php
namespace Api;
class TradeChannelController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /api/tradechannel
	 *
	 * @return Response
	 */
	public function index()
	{

		$id = \Input::get('id');
		$activity = \Activity::find($id);

		if(\Input::has('sc')){
			$sc = \Input::get('sc');
			$scheme = \TradedealScheme::find($sc);
			$scheme_channels = \TradedealSchemeChannel::where('tradedeal_scheme_id',$scheme->id)->get();
		}

		$scheme_selection = [];
		if(!empty($scheme_channels)){
			foreach ($scheme_channels as $sel) {
				$scheme_selection[] = $sel->channel_node;
			}
		}

		$all_selected_nodes = \TradedealSchemeChannel::getallselected($activity);
		$all_nodes = [];
		
		// if(!empty($all_selected_nodes)){
		// 	foreach ($all_selected_nodes as $sel) {
		// 		$all_nodes[] = $sel->channel_node;
		// 	}
		// }

		$final_nodes = array_diff($all_nodes,$scheme_selection);

		$selected_channels = \ActivityCustomer::getSelectedChannels($activity);
		$channels = \DB::table('sub_channels')
			->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code')
			->where('trade_deal', 1)
			->whereIn('channels.channel_code', $selected_channels)
			->groupBy('channels.channel_code')
			->orderBy('channel_name')
			->get();

		$data = array();
		foreach ($channels as $channel) {
			$sub_channels = \DB::table('sub_channels')
				->where('channel_code', $channel->channel_code)
				->where('trade_deal', 1)
				->groupBy('rtm_tag')
				->orderBy('rtm_tag')
				->get();
				
			$channel_children = array();
			foreach ($sub_channels as $sub_channel) {
				$key = $channel->channel_code.".".$sub_channel->rtm_tag;
				if(!in_array($key, $final_nodes)){
					$channel_children[] = array(
						'select' => true,
						'title' => $sub_channel->rtm_tag,
						'isFolder' => true,
						'key' => $key,
						);
				}
			}
			if(count($channel_children) > 0){
				$data[] = array(
					'title' => $channel->channel_name,
					'isFolder' => true,
					'key' => $channel->channel_code,
					'children' => $channel_children,
					);				
			}
			
		}
		return \Response::json($data,200);
	}

	public function selectedtdchannels(){
		$id = \Input::get('id');
		$data = array();
		$sel = \TradedealSchemeChannel::where('tradedeal_scheme_id',$id)->get();
		if(!empty($sel)){
			foreach ($sel as $row) {
				$data[] = $row->channel_node;
			}
		}
		return \Response::json($data,200);
	}

}