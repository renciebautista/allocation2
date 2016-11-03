<?php

class TradeCollectiveSeries extends \Eloquent {
	protected $fillable = ['month_year', 'tradedeal_scheme_id', 'series'];

	public static function getSeries($month_year, $scheme_id){
		$data = self::where('month_year', $month_year)
			->where('tradedeal_scheme_id', $scheme_id)
			->first();

		if(empty($data)){
			$last = self::orderBy('id', 'desc')->first();
			$series = 1;
			if(!empty($last)){
				$series = $last->series + 1;
			}

			if($series == 100){
				$series = 1;
			}

			$data = self::create(['month_year' => $month_year,
				'tradedeal_scheme_id' => $scheme_id,
				'series' => $series]);
		}

		return $data;
	}
}