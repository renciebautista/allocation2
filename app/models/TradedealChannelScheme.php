<?php

class TradedealChannelScheme extends \Eloquent {
	protected $fillable = [];

	public static function getSchemeSku($channel_id){
		$query = sprintf("select CONCAT(tradedeal_part_skus.host_desc, ' - ', tradedeal_part_skus.host_code) as host_sku,
			CONCAT(tradedeal_part_skus.pre_desc, ' - ', tradedeal_part_skus.pre_code) as pre_sku,
			buy, free, host_cost, host_pcs_case,tradedeal_uom_id,tradedeal_uoms.tradedeal_uom
			from tradedeal_channel_schemes
			join tradedeal_part_skus on tradedeal_part_skus.id = tradedeal_channel_schemes.tradedeal_part_sku_id
			join tradedeal_uoms on tradedeal_uoms.id = tradedeal_channel_schemes.tradedeal_uom_id
			where tradedeal_channel_id = '%s' order by tradedeal_part_skus.id", $channel_id);

		return DB::select(DB::raw($query));
	}
}