<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\Helpers as Helper;
?>

<thead> 
	<tr> 
            <th colspan="4"> <a style="color:#fff" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index'; ?>">ORDERS</a><span class="glyphicon glyphicon-refresh" onclick="updateDashboardDetails('_orders')" style="float:right; cursor:pointer" id="_orders"></span></th>
	</tr> 
</thead>
<tbody >
	<tr>
		<td><b><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_ORDERCONFIRMATION'];; ?>">New Orders</a></b></td>
		<td align="right"><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_ORDERCONFIRMATION']; ?>"><?php echo isset($data['ordersInfo']['new_orders']) ? $data['ordersInfo']['new_orders'] : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_ORDERCONFIRMATION'].'&state_name=overdue'; ?>">Overdue Orders</a></b></td>
		<td align="right"><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_ORDERCONFIRMATION'].'&state_name=overdue'; ?>"><?php echo isset($data['ordersInfo']['overdue']) ? $data['ordersInfo']['overdue'] : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_READYTOSHIPPED']; ?>">Ready to be Shipped</a></b></td>
		<td align="right"><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_READYTOSHIPPED']; ?>"><?php echo isset($data['ordersInfo']['ready_to_shipped']) ? $data['ordersInfo']['ready_to_shipped'] : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['RECEIVED_AT_COURIER_HUB']; ?>">Recieved at Courier Hub</a></b></td>
		<td align="right"><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['RECEIVED_AT_COURIER_HUB']; ?>"><?php echo isset($data['ordersInfo']['handed_over_courier']) ? $data['ordersInfo']['handed_over_courier'] : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_RTO_INITIATED_DELIVERED']; ?>">RTO</a></b></td>
		<td align="right"><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_RTO_INITIATED_DELIVERED']; ?>"><?php echo isset($data['ordersInfo']['rto_initiated_delivered']) ? $data['ordersInfo']['rto_initiated_delivered'] : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_RETURNED']; ?>">Returns</a></b></td>
		<td align="right"><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_OS_RETURNED']; ?>"><?php echo isset($data['ordersInfo']['returned']) ? $data['ordersInfo']['returned'] : 0; ?></a></br></td>
	</tr>
	<tr>
		<td><b><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_SHIPPED']; ?>">Shipped</a></b></td>
		<td align="right"><a style="color:#333333" href="<?php echo Yii::$app->params['WEB_URL'].'index.php?r=order/index&search=true&status='.$orderState['PS_SHIPPED']; ?>"><?php echo isset($data['ordersInfo']['shipped']) ? $data['ordersInfo']['shipped'] : 0; ?></a></br></td>
	</tr>
	
</tbody>

