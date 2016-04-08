<?php
namespace console\controllers;
use Yii;
use yii\console\Controller;
use console\models\Order;
use backend\models\Order as modelOrder;
/**
 * Updateorder controller
 */
class UpdateOrderController extends Controller {
    public function actionIndex() {
             $model = new Order;
			 $modelOrder = new modelOrder;
			 Yii::$app->params['api_url'] = current($modelOrder->getConfigValue('API_URL'));
		     $model->updateOrder();
    }
}
?>