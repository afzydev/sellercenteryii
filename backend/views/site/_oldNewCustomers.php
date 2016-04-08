<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
use kartik\date\DatePicker;
?>

<thead> 
	<tr> 
		<th colspan="4"> Customer<span class="glyphicon glyphicon-refresh get-updated-details" style="float:right; cursor:pointer"></span><br>
			Top 10 Product from 12/12/2016 to 12/12/2017
		</th>
	</tr>
	<tr> 
		<td colspan="2">New Customers<br><?php echo $data['newCustomers']['new_customer']; ?></td>
		<td colspan="2">Returning Customers<br><?php echo $data['returningCustomer']['returning_customer']; ?></td>
	</tr>
	<tr> 
		<th colspan="4">Top 5 Ordering Cities</th>
	</tr>
</thead>
<tbody>
	<?php
	if (count($data['order'])) {
		foreach ($data['topFiveOrderCity'] as $topFiveOrderCity) {
			?>
			<tr>
				<td colspan="4"><?php echo $topFiveOrderCity['city'] ?></td>
			</tr>
		<?php }
	}
	?>

</tbody>

            