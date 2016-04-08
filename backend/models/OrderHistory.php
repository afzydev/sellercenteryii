<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\MyActiveRecord;
use yii\data\SqlDataProvider;
use common\components\Helpers as Helper;
/**
* 
*/
class OrderHistory extends MyActiveRecord
{

    public $id_order;
    public $id_order_state;
    public $id_employee;
    public $waybill;
    public $date_add;
    public $sendemail;
    public $id_cancellation_reason;
    
    public static function tableName(){
        return '{{%ps_order_history}}';
    }
    public static function primaryKey()
    {
       return '{{%id_order_history}}';
    }

    public function updateStatus(){
        try{
            $connection = $this->getDb();
            $orderIdsArra=explode(',',$this->id_order);
            /*
            foreach ($orderIdsArra as $idOrder) {
                if(static::find()->where(['id_order' => $idOrder,'id_employee'=>$this->id_employee,'id_order_state'=>$this->id_order_state ])->orderBy(['id_order_history' => SORT_DESC,])->limit(1)->exists())
                {
                    return false;
                }
            }*/
            $values=[];
            $i=0;
            $queryString='';
            foreach ($orderIdsArra as $idOrder) {
                $queryString.='(';
                $queryString.=$this->id_employee.',';
                $queryString.=$idOrder.',';
                $queryString.=$this->id_order_state.',';
                $queryString.="'".$this->date_add."',";
                $queryString.=$this->id_cancellation_reason.',';
                $queryString.="'".$this->waybill."',";
                $queryString.=$this->sendemail;
                $queryString.='),';
            }
            $queryString=substr($queryString,0,-1);
           
            return $connection->createCommand('INSERT INTO '.OMS_ORDER_HISTORY.' (id_employee,id_order,id_order_state,date_add,id_cancellation_reason,waybill,sendemail) values'.$queryString.' ')->execute();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }



    public function getAllOrderHistory(){
        try{
            $connection = $this->getDb();
            
            $where = '1=1 and';

            // if(Helper::isSeller())
            // {
            //     $where .= ' oh.id_employee ='.Helper::getSessionId().' AND ';
            // }
            return $connection->createCommand('SELECT os.name , c.firstname,c.lastname, oh.id_order, oh.date_add,osr.reason FROM '.ORDER_HISTORY.' oh 
                LEFT JOIN '.ORDER_STATE_LANG.' os ON oh.id_order_state = os.id_order_state 
                LEFT JOIN '.EMPLOYEE.' c ON oh.id_employee = c.id_employee
                LEFT JOIN '.ORDER_STATE_REASON.' osr ON oh.id_cancellation_reason=osr.id_order_state_reason
                WHERE '.$where.' oh.id_order ='.$this->id_order.' ORDER BY id_order_history DESC')
            ->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }


}

?>
