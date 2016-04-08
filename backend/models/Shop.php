<?php
namespace backend\models;

use Yii;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;
use  yii\web\Session;
/**
* 
*/
class Shop extends MyActiveRecordShop
{

    public $id_shop;
    public $name;
    public $shopData;

    public static function tableName(){
        return '{{%ps_shop}}';
    }
    public static function primaryKey()
    {
       return '{{%id_shop}}';
    }
    public function getShops()
    {
        try{
            if(empty($this->shopData) && count($this->shopData)==0){
                $shops = static::find()->asArray()->all();
                foreach ($shops as $key=>$value) {
                    $this->shopData[$key]=$value;
                }
            }
            return $this->shopData;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
    public function setShopSession()
    {
        try{
            $session = Yii::$app->session;
            $session->set('id_shop', $this->id_shop);

            if($session->has('id_shop'))
            {
                $id_shop = $session->get('id_shop');
                $shopvalue=$this->getShopName($id_shop);
                $session->set('shop_name', $shopvalue[0]['name']);
                return $session->get('id_shop');
            }
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
    public function getShopName($id_shop){
        try{
            return static::find()->where(['id_shop'=>$id_shop])->asArray()->all();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }


}

?>