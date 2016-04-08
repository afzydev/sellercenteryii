<?php
namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;
/**
* 
*/
class OrderDetail extends MyActiveRecordShop
{

    public $id_order;
   
    public static function tableName(){
        return '{{%ps_order_detail}}';
    }
    public static function primaryKey()
    {
       return '{{%id_order_detail}}';
    }

    public function getFullOrderDetails(){
        try{
            $connection = $this->getDb();

            return $connection->createCommand('SELECT  od.product_id as id_product,od.unit_price_tax_incl,od.total_price_tax_incl,od.product_quantity, od.product_weight,od.product_name, od.product_quantity_refunded, od.product_quantity_return, sa.quantity,p.shop_margin,p.pg_fee,p.shipping_charge,p.price as mrp
                FROM '.ORDER_DETAIL.' od
                LEFT JOIN '.STOCK_AVAILABLE.' sa ON od.product_id = sa.id_product
                LEFT JOIN '.PRODUCT.' p ON od.product_id = p.id_product
                WHERE od.id_order='.$this->id_order)->queryOne();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }


}

?>
