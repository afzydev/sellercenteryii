<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
*  
*/
class MyActiveRecord extends ActiveRecord
{

	public static function getDb(){
        return Yii::$app->db;
	}
}



?>