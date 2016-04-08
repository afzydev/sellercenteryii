<?php
use yii\helpers\Html;
use common\components\Helpers as Helper;
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" 0="">×</button>
	<b>Vendor Payout Details</b>
</div>
<div class="modal-body">
	<table class="table table-striped">
		<tr>
			<th>Selling Price</th>
			<th>Shop Margin</th>
			<th>Payment Gateway Fee</th>	
			<th>Shipping Charge</th>
			<th>Total Deduction</th>
			<th>Net Vendor Payout</th>

		</tr>
		<tr> 
			<td><?php if(!empty($sellingPrice)) {echo $sellingPrice;}?></td>
			<td><?php if(!empty($shop_margin)) {echo $shop_margin;}?></td>
			<td><?php if(!empty($payment_gateway_fee)) {echo $payment_gateway_fee;}else{echo 0.00;}?></td>
			<td><?php echo $shipping_cost; ?></td>
			<td><?php if(!empty($total_deductions)) {echo $total_deductions;} ?></td>
			<td><?php echo $vendor_payout;	?></td>
		</tr>
	</table>
</div>
<div class="modal-footer">
</div>