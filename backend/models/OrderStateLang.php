<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\SqlDataProvider;
use backend\models\OrderState;
use backend\models\StateMachine;
use common\components\Session as ShopSession;
use common\components\ShopdevConfiguration;
use common\components\Helpers as Helper;

/**
* 
*/
class OrderStateLang extends OrderState
{

    public $id_order_state;
    public $name;
    public $current_state;
    public $allDeletedIds;
    public $criteria;
    public $employeeIds;
    
    protected $order;
    public function init(){
        $this->allDeletedIds = OrderState::getAllDeletedOrderState();
        $this->order=new Order;
    }


    public static function getDb() {
        return Yii::$app->dbshop;
    }
    public static function primaryKey() {
       return '{{%id_order_state}}';
    }

    public function getAllOrderStatus(){
        try{
        $connection = $this->getDb();
        $configValues = ShopdevConfiguration::getConfigValue('_SHOP_LIVE_DATE_TIME_');
        $date = $configValues['value'];



        $this->criteria="1=1 and ".ORDERS.".date_add>='".$date."'";
        $notShowIds='';
        //echo $this->allDeletedIds;die;
        if(Helper::isSeller())
        {
            $notShowSellerStatus = ShopdevConfiguration::getConfigValue('_NOT_SHOW_SELLER_FILTER_STATUS_', null, ShopSession::shopSessionId());
            if(isset($notShowSellerStatus['value']) && !empty($notShowSellerStatus['value']))
                $notShowIds=$notShowSellerStatus['value'];
            
            if(!empty($notShowIds) && !empty($this->allDeletedIds))
                $this->allDeletedIds=$notShowIds.','.$this->allDeletedIds;
            else if(empty($notShowIds) && !empty($this->allDeletedIds))
                $this->allDeletedIds=$this->allDeletedIds;
            else if(!empty($notShowIds) && empty($this->allDeletedIds))
                $this->allDeletedIds=$this->notShowIds;
        }

        
        if(!empty($this->allDeletedIds))
            $this->criteria.=' and '.ORDER_STATE_LANG.'.id_order_state NOT IN ('.$this->allDeletedIds.')';
        if(!empty($this->employeeIds))
            $this->criteria.=' and product.id_employee in ('.$this->employeeIds.')';
        if(ShopSession::shopSessionId())
            $this->criteria.=' and '.ORDERS.'.id_shop = '.ShopSession::shopSessionId().' ';

        return $connection->createCommand('SELECT COUNT('.ORDERS.'.id_order ) AS count_orders, '.ORDERS.'.current_state AS id_order_state, '.ORDER_STATE_LANG.'.name AS name FROM  '.ORDERS.' 
            LEFT JOIN  '.ORDER_STATE_LANG.' ON '.ORDERS.'.current_state = '.ORDER_STATE_LANG.'.id_order_state
            LEFT JOIN ' . ORDER_DETAIL . ' detail ON detail.id_order = '.ORDERS.'.id_order
            LEFT JOIN ' . PRODUCT . ' product ON product.id_product = detail.product_id 
            WHERE '.$this->criteria.' GROUP BY '.ORDERS.'.current_state')
            ->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }


}

?>