<?php
namespace common\components;

use Yii;
use yii\base\ActiveRecord;
use yii\base\InvalidConfigException;

/**
*  
*/
class MyActiveRecord extends ActiveRecord
{
	public static $server_id = 2; 
	public static $master_db;
	public function getDbConnection() { 
		self::$master_db = Yii::app()->{"db" . self::$server_id}; 
		if (self::$master_db instanceof CDbConnection) {
			self::$master_db->setActive(true); 
			return self::$master_db; 
		} 
		else
		{
			 throw new CDbException(Yii::t('yii', 'Active Record requires a "db" CDbConnection application component.'));
		}
	}
}



?>