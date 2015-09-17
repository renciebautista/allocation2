<div class="row">
	<div class="col-lg-12">
		<div class="form-group">

			@if(count($activityIdList) > 1)
				@if($activityIdList[0] == $activity->id)
					<a href="{{ URL::action('SchemeController@edit', $activity->id) }}" class="btn btn-primary disabled">Previous</a>
				@else
					<?php 
						$id = $id_index - 1;
					?>
					<a href="{{ URL::action('SchemeController@edit', $activityIdList[$id]) }}" class="btn btn-primary">Previous</a>
				@endif
				
				<?php 
					$last_cnt = count($activityIdList);
				?>

				@if($activityIdList[$last_cnt - 1] == $activity->id)
					<a href="{{ URL::action('SchemeController@edit', $activity->id) }}" class="btn btn-primary disabled">Next</a>
				@else
					<?php 
						$id = $id_index + 1;
					?>
					<a href="{{ URL::action('SchemeController@edit',  $activityIdList[$id]) }}" class="btn btn-primary">Next</a>
				@endif
			@endif
			
		</div>
	</div>

</div>