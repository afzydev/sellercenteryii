<?php
use yii\helpers\Html;
use common\components\Helpers as Helper;
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" 0="">×</button>
	<b>Payment Details</b>
</div>
<div class="modal-body">
	<table class="table table-striped">
		<tr>
			<th>Order Id</th>
			<th>Mode of Payment</th>
			<th>Order Number</th>	
			<th>Product Price</th>
			<th>Qty</th>
			<th>Discount</th>
			<th>COD Fee</th>
			<th>Shipping Fee</th>
			<th>Total</th>

		</tr>
		<tr> 
			<td><?php if(!empty($id_order)) {echo $id_order;}?></td>
			<td><?php if(!empty($payment)) {echo $payment;}else{echo 'N/A';}?></td>
			<td><?php if(!empty($invoice_ref_key)) {echo $invoice_ref_key;}else{echo 'N/A';}?></td>
			<td><?php if(!empty($price)) { echo Helper::formatNumberByType($price,Yii::$app->params['formatNumberType']);} else {echo '0.00';}?></td>
			<td><?php if(!empty($product_quantity)) {echo ($product_quantity);}else {echo '0.00';} ?></td>
			<td><?php if(!empty($discount)) {echo $discount;} else {echo '0.00';}?></td>
			<td><?php if(!empty($cod)) {echo $cod;}else {echo '0.00';}?></td>
			<td><?php if(!empty($shipping)) {echo $shipping;}else {echo '0.00';}?></td>
			<td><?php if(!empty($paid)) {echo Helper::formatNumberByType($paid,Yii::$app->params['formatNumberType']);}else {echo '0.00';}?></td>
		</tr>
	</table>
</div>
<div class="modal-footer">
</div>