<?php
use yii\helpers\Html;
use common\components\Helpers as Helper;
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" 0="">×</button>
	<b>Shipping Details</b>
</div>
<div class="modal-body">
	<table class="table table-striped">
		<tr>
			<th>Order Id</th>
			<th>Type</th>
			<th>Carrier Name</th>	
			<th>Waywill Number</th>
		</tr>
		<tr> 
			<td><?php if(!empty($id_order)) {echo $id_order;}?></td>
			<td><?php if(!empty($delivery_type)) {echo $delivery_type;}else{echo 'N/A';}?></td>
			<td><?php if(!empty($carrier_name)) {echo $carrier_name;}else{echo 'N/A';}?></td>
			<td><?php if(!empty($response_waywill)) {echo $response_waywill;}else{echo 'N/A';}?></td>

		</tr>
	</table>
</div>
<div class="modal-footer">
</div>