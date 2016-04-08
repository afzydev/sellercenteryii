<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
?>
<div style="height:450px; overflow:auto;">
<table class="table table-striped table-bordered detail-view">
	<thead> 
		<tr> 
            <th>Updated By</th>
            <th>Updated Quantity</th>
            <th>Updated Date</th>
		</tr> 
	</thead>
	<tbody>
		<?php

		if($viewLog>0) 
		{
			foreach($viewLog as $log){?>
		<tr>
			<td> <?php echo $log['name'];?> </td>
			<td> <?php echo $log['quantity'];?> </td>
			<td> <?php echo $log['date_generated'];?> </td>
		</tr>
		<?php }
		}
		if($viewLog==0)
		{
		 ?>
		<tr>
			<td colspan="3">No result found</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
</div>