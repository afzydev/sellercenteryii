<?php
namespace common\components;
use Yii;
use yii\base\Component;

class Message extends Component {
	  public static $arr_message = [];
	  
	  /*
	  * Load and get message 
	  */
	  public static function loadMessage($key=null){
		   if(empty(self::$arr_message) && $key==null){
			     $str = file_get_contents('json/message.json');
				 self::$arr_message = json_decode($str, true);
		   }
		   else
		   {
		       return self::$arr_message[$key];
		   }			  
	  }
}
?>