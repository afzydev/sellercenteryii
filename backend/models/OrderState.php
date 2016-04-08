<?php
namespace backend\models;

use Yii;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;
/**
* 
*/
class OrderState extends MyActiveRecordShop
{
    public $id_order_state;

    public static function tableName(){
        return '{{%ps_order_state}}';
    }
    public static function primaryKey()
    {
       return '{{%id_order_state}}';
    }

    public function getAllDeletedOrderState()
    {
        try{
            $connection = $this->getDb();

            $allDeletedIds = $connection->createCommand('SELECT GROUP_CONCAT(id_order_state) as id_order_state FROM '.ORDER_STATE.' where deleted=1 ')
                ->queryAll();
            $delIds=$allDeletedIds[0]['id_order_state'];
            return $delIds;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    public function findSendEmail()
    {
        try{
            return static::find()->where(['id_order_state' => $this->id_order_state])->one();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
  /*
   * Get color from id
   * @Author Amit Chaudhary
   * @Date:- 04 march 2016
   */  
    public function getColorById($id_order_state)
    {
        try{
            $connection = $this->getDb();
             $query = 'SELECT color FROM '.ORDER_STATE.' where id_order_state ='. $id_order_state ;
             $color = $connection->createCommand($query)->queryOne();
             return $color['color'];
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

}

?>