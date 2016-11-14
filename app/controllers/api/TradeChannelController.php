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

		// if(\Input::has('sc')){
		// 	$sc = \Input::get('sc');
		// 	$scheme = \TradedealScheme::find($sc);
		// 	$scheme_channels = \TradedealSchemeChannel::where('tradedeal_scheme_id',$scheme->id)->get();
		// }

		// $selecteds = \TradedealSchemeChannel::getallselected($activity);
		
		// $_ch = [];
		// $_l5 = [];
		// $sc_ch = [];
		// $sc_l5 = [];

		// if(!empty($scheme_channels)){
		// 	foreach ($scheme_channels as $sel) {
		// 		$node = explode(".", $sel->channel_node);
		// 		if(isset($node[1])){
		// 			$sc_l5[] = $node[1];
		// 		}else{
		// 			$sc_ch[] = $node[0];
		// 		}
		// 	}
		// }
		// foreach ($selecteds as $sel) {
		// 	$node = explode(".", $sel->channel_node);
		// 	if(isset($node[1])){
		// 		$_l5[] = $node[1];
		// 	}else{
		// 		$_ch[] = $node[0];
		// 	}
		// }



		// $final_ch = array_diff($_ch,$sc_ch);
		// $final_l5 = array_diff($_l5,$sc_l5);

		$selected_channels = \ActivityCustomer::getSelectedChannels($activity);

		
		$channels = \DB::table('sub_channels')
			->join('channels', 'channels.channel_code', '=', 'sub_channels.channel_code')
			->where('trade_deal', 1)
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
				// if(!in_array($level5->l5_code, $final_l5)){
					$channel_children[] = array(
						'select' => true,
						'title' => $sub_channel->rtm_tag,
						'isFolder' => true,
						'key' => $channel->channel_code.".".$sub_channel->rtm_tag,
						);
				// }
			}
			// if(count($channel_children) >0){
				// if(!in_array($channel->channel_code, $final_ch)){
					$data[] = array(
						'title' => $channel->channel_name,
						'isFolder' => true,
						'key' => $channel->channel_code,
						'children' => $channel_children,
						);
				// }
				
			// }
			
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