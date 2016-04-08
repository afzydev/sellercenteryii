<?php
 namespace common\components;
 use Yii;
 use yii\base\Component;
 use common\models\MyActiveRecord;
 
 class Configuration extends Component{
		
		public static $psconfig_values=[];

		 /**
		 * Function name    : loadPSConfiguration
		 * Description      : get all configuration set in a table .
		 * @param           : none
		 * @return          : none
		 * Created By       : Dharmendra Shahi
		 * Created Date     : 15-01-2016
		 * Modified By      : 
		 * Modified Date    : 00-00-0000
		 */

		public static function get($key=null){
		      $connection = MyActiveRecord::getDb();
		      if(empty(self::$psconfig_values))
		      {
			      $query = "SELECT name, value FROM " . SITE_CONFIG;
			      $rst = $connection->createCommand($query)->queryAll();
			      foreach($rst as $v){
				     self::$psconfig_values[$v['name']] = $v['value'];
			      }
		      }
      		return self::$psconfig_values[$key];
		}
 } 
?>