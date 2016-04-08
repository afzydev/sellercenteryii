<?php
namespace backend\models;

use Yii;
use common\models\MyActiveRecordShop;
use yii\data\SqlDataProvider;
/**
* 
*/
class ProfileLang extends MyActiveRecordShop
{

    public static function tableName(){
        return '{{%ps_profile_lang}}';
    }
    public function getProfile(){
    	try{
	        $profileName= static::find()->where(['id_profile' => Yii::$app->user->identity->id_profile])->one();
	        return $profileName->id_profile;
    	}
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

}

?>