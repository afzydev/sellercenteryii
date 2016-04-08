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
class StockLog extends MyActiveRecord
{

    public $id_stock_log;
    public $id_product;
    public $id_employee;
    public $quantity;
    public $id_shop;
    public $date_generated;
    
    public static function tableName(){
        return '{{%stock_log}}';
    }
    public static function primaryKey()
    {
       return '{{%id_stock_log}}';
    }

    public function updateStock(){
       
        try{
            $connection = $this->getDb();
            

          
                $queryString='';
                $queryString.='(';
                $queryString.=$this->id_employee.',';
                $queryString.=$this->id_shop.',';
                $queryString.="'".$this->date_generated."',";
                $queryString.=$this->id_product.',';
                $queryString.=$this->quantity.',';
                $queryString.=')';
            
            return $connection->createCommand('INSERT INTO '.OMS_STOCK_LOG.' (id_employee,id_shop,date_generated,id_product,quantity) values("'.$this->id_employee.'","'.$this->id_shop.'","'.$this->date_generated.'","'.$this->id_product.'","'.$this->quantity.'") ')->execute();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    public function getStockLog($idProduct){
        try
        {
            $connection = $this->getDb();
            $query='Select CONCAT(em.`firstname`, " ", em.`lastname`) as name,sl.quantity,sl.date_generated from '.OMS_STOCK_LOG.' sl LEFT JOIN '.EMPLOYEE.' em ON sl.id_employee=em.id_employee where sl.id_product='.$idProduct.' ORDER BY id_stock_log DESC';
            return $connection->createCommand($query)->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

}

?>
