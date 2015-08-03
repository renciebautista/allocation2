@if(count($pis) > 0)
<div>
	<h2>Product Information Sheet</h2>
	<table class="bordered">
		<tr nobr="true">
			<td width="120">Product Category</td>
			<td width="730">{{ $pis[2][1] }}</td>
		</tr>
		<tr nobr="true">
			<td>Sub Category</td>
			<td>{{ $pis[3][1] }}</td>
		</tr>
		<tr nobr="true">
			<td>Brand / Scheme</td>
			<td>{{ $pis[4][1] }}</td>
		</tr>
		<tr nobr="true">
			<td>Target Market</td>
			<td>{{ $pis[5][1] }}</td>
		</tr>
		<tr nobr="true">
			<td>Product Features</td>
			<td>{{ $pis[6][1] }}</td>
		</tr>
		<tr nobr="true">
			<td>Major Competitors</td>
			<td>{{ $pis[7][1] }}</td>
		</tr>
		<tr nobr="true">
			<td>Minor Competitors</td>
			<td>{{ $pis[8][1] }}</td>
		</tr>
		<?php 
			$x = 0; 
			for ($i=8; $i < count($pis); $i++) { 
				if($pis[$i][7] == "Case Dimensions (MM)"){
					$x = $i+2;
					break;
				}
			}
		?>
		<tr nobr="true">
			<td colspan="2">
				<table class="sub-table">
					<tr nobr="true">
						<th colspan="7"></th>
						<th colspan="3">Case Dimensions (MM)</th>
					</tr>
					<tr nobr="true">
						<th width="195">Item Description</th>
						<th width="100">Pack Size</th>
						<th width="100">Pack Color</th>
						<th width="50">Units/Case</th>
						<th width="100">Product Barcode</th>
						<th width="100">Product Code</th>
						<th width="50">Case/Ton</th>
						<th width="50" style="text-align:center">L</th>
						<th width="50" style="text-align:center">W</th>
						<th width="50" style="text-align:center">H</th>
					</tr>
					@for($x1 = $x; $x1 < count($pis); $x1++)
						<?php if($pis[$x1][1] == "Product Dimension (MM)"){
							break;
							
						} ?>
						@if($pis[$x1][0] != "")
						<tr nobr="true">
							@for($_x = 0; $_x < 10 ; $_x++)
								@if($_x == 6)
								<td>{{ number_format($pis[$x1][$_x],2) }}</td>
								@else
								<td>{{ $pis[$x1][$_x] }}</td>
								@endif
							
							@endfor
						</tr>
						@endif
						<?php $x = $x1; ?>
					@endfor
				</table>
			</td>
		</tr>

		<tr nobr="true">
			<td colspan="2">
				<table class="sub-table">
					<tr nobr="true">
						<th></th>
						<th colspan="3">Product Dimension (MM)</th>
						<th colspan="3"></th>
						<th colspan="3">Maximum Case Stocking</th>
					</tr>
					<tr nobr="true">
						<th width="145">Item Description</th>
						<th width="50" style="text-align:center">L</th>	
						<th width="50" style="text-align:center">W</th>
						<th width="50" style="text-align:center">H</th>
						<th width="120">Product Casecode</th>
						<th width="50">Net Wgt Kg</th>
						<th>Gross Wgt KG</th>
						<th width="50">CS/Layer</th>
						<th width="50">Layer/Pallet</th>
						<th width="100">Pallets/Tier</th>
					</tr>
					<?php $x = $x+3; ?>
					@for($x2 = $x; $x2 < count($pis); $x2++)
						<?php if($pis[$x2][8] == "Trade margins"){
							break;
							$x = $x2;
						} ?>
						@if($pis[$x2][0] != "")
						<tr nobr="true">
							@for($_x2 = 0; $_x2 < 10 ; $_x2++)
							<td>{{ $pis[$x2][$_x2] }}</td>
							@endfor
						</tr>
						@endif
						<?php $x = $x2; ?>
					@endfor
				</table>
			</td>
		</tr>

		<tr nobr="true">
			<td colspan="2">
				<table class="sub-table">
					<tr nobr="true">
						<th colspan="8"></th>
						<th colspan="2">Trade margins</th>
					</tr>
					<tr nobr="true">
						<th width="150">Item Description</th>
						<th width="50">Total Shelf Life (SLED in Days)</th>
						<th width="50">Pieces/Inner Pack (regular SKU with inner pack/carton)</th>
						<th width="65">Product Barcode</th>
						<th width="60">Product Code</th>
						<th width="40">LPAT/CS</th>
						<th width="40">LPAT per PC/MP</th>
						<th width="40">SRP Per PC/MP</th>
						<th width="20">%</th>
						<th width="34">Absolute</th>
					</tr>
					<?php $x = $x+3; ?>
					@for($x3 = $x; $x3 < count($pis); $x3++)
						@if($pis[$x3][0] != "")
						<tr nobr="true">
							@for($_x3 = 0; $_x3 < 10 ; $_x3++)
								@if($_x3 == 8)
								<td>{{ round(number_format($pis[$x3][$_x3],2)) }}</td>
								@else
								<td>{{ $pis[$x3][$_x3] }}</td>
								@endif
							
							@endfor
						</tr>
						@endif
					@endfor
				</table>
			</td>
		</tr>
	</table>
</div>
@endif