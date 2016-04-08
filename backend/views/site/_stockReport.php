<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
?>
<thead> 
	<tr> 
            <th colspan="4"><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index'; ?>" style="color:#fff">INVENTORY</a><span class="glyphicon glyphicon-refresh" onclick="updateDashboardDetails('_stockReport')" style="float:right; cursor:pointer"></span></th> 
	</tr> 
</thead>
<tbody>
	<tr>
		<td><b><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index'; ?>" style="color:#333333">Total Products</a></b></td>
		<td align="right"><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index'; ?>" style="color:#333333"><?php echo isset($data['productInfo']['total_products']) ? Helper::getFormattedNumber($data['productInfo']['total_products']) : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&in_stock=true'; ?>" style="color:#333333">In Stock</a></b></td>
		<td align="right"><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&in_stock=true'; ?>" style="color:#333333"><?php echo isset($data['productInfo']['in_stock']) ? Helper::getFormattedNumber($data['productInfo']['in_stock']) : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&low_stock=true'; ?>" style="color:#333333">Low In Stock</a></b></td>
		<td align="right"><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&low_stock=true'; ?>" style="color:#333333"><?php echo isset($data['productInfo']['low_in_stock']) ? Helper::getFormattedNumber($data['productInfo']['low_in_stock']) : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&out_stock=true'; ?>" style="color:#333333">Out of Stock</a></b></td>
		<td align="right"><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&out_stock=true'; ?>" style="color:#333333"><?php echo isset($data['productInfo']['out_of_stock']) ? Helper::getFormattedNumber($data['productInfo']['out_of_stock']) : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&is_active=true'; ?>" style="color:#333333">Inactive</a></b></td>
		<td align="right"><a href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=product/index&is_active=true'; ?>" style="color:#333333"><?php echo isset($data['productInfo']['inactive']) ? Helper::getFormattedNumber($data['productInfo']['inactive']) : 0; ?></a></br></td>
	</tr>
</tbody>
            