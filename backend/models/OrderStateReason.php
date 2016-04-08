<?php
namespace backend\models;

use Yii;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;
/**
* 
*/
class OrderStateReason extends MyActiveRecordShop
{
    public $id_order_state;

    public static function tableName(){
        return '{{%ps_order_state_reason}}';
    }
    public static function primaryKey()
    {
       return '{{%id_order_state_reason}}';
    }

    public function getAllOrderStateReason()
    {
        try{
            $connection = $this->getDb();
            $orderStateReason = $connection->createCommand('SELECT * FROM '.ORDER_STATE_REASON.' where id_order_state =:id_order_state ');
            $orderStateReason->bindValue(':id_order_state', $this->id_order_state);
            return $orderStateReason->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

}

?>