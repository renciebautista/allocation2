@if(count($tradedealschemes) > 0)
<div>
	<h2>BBFREE Schemes</h2>
	<h3>Download attached excel file for allocations.</h3>
	<table class="trade-table" >
		<tr>
			<th >Activity</th>
			<th >Scheme Code</th>
			<th >Scheme Description</th>
			<th >Host Code</th>
			<th >Host Description</th>
			<th >Premium Code / PIMS Code</th>
			<th >Premium Description</th>
			<th >Channels Involved</th>
		</tr>
		@foreach($tradedealschemes as $scheme)
				<?php $x = false; ?>
				<?php $host_cnt = 1; ?>
				@if(!empty($scheme->host_skus))
				<?php $host_cnt = count($scheme->host_skus); ?>
					@foreach($scheme->host_skus as $host_sku)
						@if($host_cnt == 1)
						<tr>
							<td>{{ $scheme->name }}</td>
							<td>{{ $host_sku->scheme_code }} </td>
							<td>{{ $host_sku->scheme_desc }}</td>
							<td>{{ $host_sku->host_code }}</td>
							<td>{{ $host_sku->desc_variant }}</td>
							<td>{{ $host_sku->pre_code }}</td>
							<td>{{ $host_sku->pre_variant }}</td>
							<td>
								<ul>
									@if(!empty($scheme->rtms))
										@foreach($scheme->rtms as $rtm)
										<li>{{ $rtm->sold_to }}</li>
										@endforeach
									@endif
									
									@if(!empty($scheme->channels))
										@foreach($scheme->channels as $channel)
										<li>{{ $channel->sub_type_desc }}</li>
										@endforeach
									@endif
								</ul>
							</td>
						</tr>
						@else
						<tr>
							@if(!$x)
							<td rowspan="{{$host_cnt}}">{{ $scheme->name }}</td>
							@endif
							<td>{{ $host_sku->scheme_code }} </td>
							<td>{{ $host_sku->scheme_desc }}</td>
							<td>{{ $host_sku->host_code }}</td>
							<td>{{ $host_sku->desc_variant }}</td>
							<td>{{ $host_sku->pre_code }}</td>
							<td>{{ $host_sku->pre_variant }}</td>
							@if(!$x)
							<td rowspan="{{$host_cnt}}">
								<ul>
									@if(!empty($scheme->rtms))
										@foreach($scheme->rtms as $rtm)
										<li>{{ $rtm->sold_to }}</li>
										@endforeach
									@endif
									
									@if(!empty($scheme->channels))
										@foreach($scheme->channels as $channel)
										<li>{{ $channel->sub_type_desc }}</li>
										@endforeach
									@endif
								</ul>
							</td>
							@endif
							<?php $x = true; ?>
						</tr>
						@endif
					@endforeach
				@endif
			
		@endforeach
	</table>
</div>
@endif