<?php
 namespace common\components;
 use Yii;
 use yii\base\Component;
 use common\models\MyActiveRecordShop;
 
 class ShopdevConfiguration extends Component{

		 /**
		 * Function name    : getConfigValue
		 * Description      : get default configuration from database .
		 * @param           : 3 parA
		 * @return          : none
		 * Created By       : Preet Saxena
		 * Created Date     : 23-02-2016
		 * Modified By      : 
		 * Modified Date    : 00-00-0000
		 */
                
	public static function getConfigValue($key = null, $idShopGroup = null, $idShop = null) {
        try{
            $connection = MyActiveRecordShop::getDb();
            $WHERE = " WHERE 1=1 ";
            if (!empty($idShopGroup)) {
                $WHERE .= " AND id_shop_group=$idShopGroup ";
            }
            if (!empty($idShop)) {
                $WHERE .= " AND id_shop=$idShop ";
            }
            if (!empty($key) && !is_array($key)) {
                $WHERE .= " AND name ='$key' ";
            }elseif(count($key) && is_array($key)){
                $setkey = null;
                foreach ($key as $key1){
                    if($setkey){
                        $setkey .=  ','."'$key1'";
                    }else{
                        $setkey .=  "'$key1'";
                    }
                }
                $WHERE .= " AND name IN ($setkey)";
            }
            $query = "SELECT name, value FROM " . PS_CONFIGURATION . "$WHERE";
            if (!empty($key) && !is_array($key)) {
                $setResult = $connection->createCommand($query)->queryOne();
            }elseif(count($key) && is_array($key)){
                $setResult = $connection->createCommand($query)->queryAll();
            }
            
            return $setResult;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
 } 
?>