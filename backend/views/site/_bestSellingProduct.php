<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
use kartik\date\DatePicker;
?>
<thead>
	<tr>
		<th colspan="5"><b><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&bestSellingProduct=true'; ?>" style="color:#fff">BEST SELLING PRODUCTS</a><span class="glyphicon glyphicon-refresh" onclick="updateDashboardDetails('_bestSellingProduct')" style="float:right; cursor:pointer"></span></th>
	</tr>
	<tr>
		<th>Id</th>
		<th>Product Name</th>
		<th>Quantity in Stock</th>
		<th>Total Sold</th>
		<!-- <th>Revenue</th> -->
	</tr>
</thead>

<tbody>
	<?php
	if (count($data['bestSellingProduct'])) {
		foreach ($data['bestSellingProduct'] as $bestSellingProduct) {
			?>
		<tr style="vertical-align:top;">
			<td><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&Product[id_product]='.$bestSellingProduct['id_product'];?>" style="color:#333333"><?php echo $bestSellingProduct['id_product']; ?></a></td>
			<td><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&Product[id_product]='.$bestSellingProduct['id_product'];?>" style="color:#333333"><?php echo $bestSellingProduct['name']; ?></a></td>
			<td><?php echo $bestSellingProduct['available_qty']; ?></td>
			<td><?php echo $bestSellingProduct['sold']; ?></td>
			<!-- <td align="right"><?php echo $bestSellingProduct['revenue']; ?></td> -->
		</tr>
<?php }
}
else
{ ?>
		<tr style="vertical-align:top;" >
			<td colspan="4" align="center">No records found</td>
		</tr>
<?php
}
?>

</tbody>

