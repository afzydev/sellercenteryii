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
class PriceLog extends MyActiveRecord
{

    public $id_price_log;
    public $id_product;
    public $id_employee;
    public $id_shop;
    public $sellPrice;
    public $basePrice;
    public $date_add;
    
    public static function tableName(){
        return '{{%price_log}}';
    }
    public static function primaryKey()
    {
       return '{{%id_price_log}}';
    }

    public function updatePrice(){
               
        try{
            $connection = $this->getDb();
                           
            return $connection->createCommand('INSERT INTO '.OMS_PRICE_LOG.' (id_employee,id_shop,date_add,id_product,base_price,selling_price) values("'.$this->id_employee.'","'.$this->id_shop.'","'.$this->date_add.'","'.$this->id_product.'","'.$this->basePrice.'","'.$this->sellPrice.'") ')->execute();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    public function getPriceLog($idProduct){
        try
        {
            $connection = $this->getDb();
            $query='Select CONCAT(em.`firstname`, " ", em.`lastname`) as name,sl.selling_price,sl.base_price as base_price,sl.date_add from '.OMS_PRICE_LOG.' sl LEFT JOIN '.EMPLOYEE.' em ON sl.id_employee=em.id_employee where sl.id_product='.$idProduct.' ORDER BY id_price_log DESC';
            return $connection->createCommand($query)->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
}

?>
