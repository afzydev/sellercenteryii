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
            <th>Base Price</th>
            <th>Updated Selling Price</th>
            <th>Updated Date</th>
		</tr> 
	</thead>
        
        
	<tbody>
            
		<?php

		if($viewPriceLog>0) 
		{
			foreach($viewPriceLog as $log){?>
		<tr>
			<td> <?php echo $log['name'];?> </td>
			<td> <?php echo $log['base_price'];?> </td>
			<td> <?php echo $log['selling_price'];?> </td>
			<td> <?php echo $log['date_add'];?> </td>
		</tr>
		<?php }
		}
		if($viewPriceLog==0)
		{
		 ?>
		<tr>
			<td colspan="3">No result found</td>
		</tr>
		<?php } ?>
                
	</tbody>
       
</table>
    </div>