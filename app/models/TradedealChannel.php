<?php

class TradedealChannel extends \Eloquent {
	protected $fillable = [];

	public static function getSchemeChannels($activity){
		$query = sprintf("select tradedeal_channels.id, tradedeal_channels.l5_code, tradedeal_channels.l5_desc,tradedeal_channels.rtm_tag,temp.name,temp.scheme_id
			from tradedeal_channels
			left join (
			select tradedeal_channel_id, name, tradedeal_scheme_id as scheme_id from tradedeal_scheme_channels
			join tradedeal_schemes on tradedeal_scheme_channels.tradedeal_scheme_id = tradedeal_schemes.id) as temp on temp.tradedeal_channel_id = tradedeal_channels.id
			where activity_id = '%s'
			order by tradedeal_channels.id",$activity->id);
		return DB::select(DB::raw($query));;
	}
}