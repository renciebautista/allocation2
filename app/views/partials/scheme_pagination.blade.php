<div class="row">
	<div class="col-lg-12">
		<div class="form-group">

			@if(count($activity_schemes) > 1)
				@if($activity_schemes[0] == $scheme->id)
					<a href="{{ URL::action('SchemeController@edit', $scheme->id) }}" class="btn btn-primary disabled">Previous</a>
				@else
					<?php 
						$id = $id_index - 1;
					?>
					<a href="{{ URL::action('SchemeController@edit', $activity_schemes[$id]) }}" class="btn btn-primary">Previous</a>
				@endif
				
				<?php 
					$last_cnt = count($activity_schemes);
				?>

				@if($activity_schemes[$last_cnt - 1] == $scheme->id)
					<a href="{{ URL::action('SchemeController@edit', $scheme->id) }}" class="btn btn-primary disabled">Next</a>
				@else
					<?php 
						$id = $id_index + 1;
					?>
					<a href="{{ URL::action('SchemeController@edit',  $activity_schemes[$id]) }}" class="btn btn-primary">Next</a>
				@endif
			@endif
			
		</div>
	</div>

</div>