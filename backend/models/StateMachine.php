<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\SqlDataProvider;
use common\components\Helpers as Helper;
use backend\models\OrderState;

/**
* 
*/
class StateMachine extends ActiveRecord
{

    public $id_order_state;
    public $seller_statuses_not_show;
    public $sellerStatusIds=[];
    public $allDeletedIds;

    public function init(){
        $this->allDeletedIds = OrderState::getAllDeletedOrderState();
    }

    public static function getDb() {
        return Yii::$app->dbshop;
    }
    public static function tableName(){
        return '{{%ps_state_machine}}';
    }
    public static function primaryKey()
    {
       return '{{%id}}';
    }

    public function getOrderStatusAccToStateMachine(){
        try{
            if(Helper::isSeller()) 
            {   
                if(!empty($this->allDeletedIds))
                {
                    $this->seller_statuses_not_show=Yii::$app->params['ps_configuration']['value'].','.$this->allDeletedIds;
                }
                else
                {
                    $this->seller_statuses_not_show=Yii::$app->params['ps_configuration']['value'];
                }
                $sellerStatusIds=explode(',',$this->seller_statuses_not_show);
            }  

            $getPossibleOrderState= static::find()->where(['id_order_state' => $this->id_order_state,'active'=>1])->all();
            $orderStatusIds=0;
            foreach($getPossibleOrderState as $PossibleOrderState){
                if(!in_array($PossibleOrderState['id_possible_order_state'],$sellerStatusIds)){
                    $orderStatusIds.=','.$PossibleOrderState['id_possible_order_state'];
                }
            }
            
            return Yii::$app->dbshop->createCommand('SELECT * FROM '.ORDER_STATE_LANG.' where id_order_state IN ('.$orderStatusIds.') ')
            ->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }



}

?>