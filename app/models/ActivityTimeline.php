<?php

class ActivityTimeline extends \Eloquent {
	protected $fillable = [];

	public function createdby()
    {
        return $this->belongsTo('User','created_by','id');
    }

    public static function getTop($activity){
    	return self::where('activity_id', $activity->id)
    		->take(20)
    		->orderBy('created_at', 'desc')
    		->get();;
    }


	public static function addTimeline($activity, $user, $header, $comment=null, $link = null){
		$summary_arr = explode(' ', $comment);
		$summary = '';
		if(count($summary_arr) > 10){
			$summary = implode(" ", array_slice($summary_arr, 0, 10));
		}else{
			$summary = $comment;
		}

		// Helper::debug($summary_arr);

		$timeline = new ActivityTimeline;
		$timeline->created_by = $user->id;
		$timeline->activity_id = $activity->id;
		$timeline->header_text = $header;
		$timeline->comment = $summary;
		$timeline->save();
	}
}