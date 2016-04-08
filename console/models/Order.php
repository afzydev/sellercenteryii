<?php
namespace console\models;
use Yii;
use yii\db\ActiveRecord;
use common\components\Curl;

class Order extends ActiveRecord
{
	  public function updateOrder(){
			$rstAll = Yii::$app->db->createCommand('SELECT *FROM '.OMS_ORDER_HISTORY.' WHERE  status=0 ')->queryAll();
			if(count($rstAll)>0){
				foreach($rstAll as $res){
					  $params='&id_order_state='.$res['id_order_state'].'&id_order='.$res['id_order'].'&id_employee='.$res['id_employee'].'&id_reason='.$res['id_cancellation_reason'].'&waybill='.$res['waybill'].'&sendemail='.$res['sendemail'].'&date_add='.$res['date_add'];
					  $resp = Curl::executeCurl(Yii::$app->params['api_url'].'create.php?resource=order_histories&action=add', $params);
					  if($resp){
						   $resp = json_decode($resp, True);
						   if(isset($resp['status']) && $resp['status']){
							   Yii::$app->db->createCommand("UPDATE ".OMS_ORDER_HISTORY." SET status=1 WHERE id_order=".$res['id_order'])->execute();
						   }
					 }
				}
			}
	  }
}
?>