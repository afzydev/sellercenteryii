<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
*  
*/
class MyActiveRecordShop extends ActiveRecord
{

	public static function getDb(){
        return Yii::$app->dbshop;
	}


}



?>