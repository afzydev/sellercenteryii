<?php
    namespace common\models;
     use Yii;
     use yii\db\ActiveRecord;
     use common\models\MyActiveRecord;
     use yii\base\Exception;
	 use common\components\CustomException;
	 use common\components\Helpers as Helper;

   class Visitlog extends MyActiveRecord{
	     //Save visit log of all users
	     public static function saveVisitLog(){
			  try
			  {
					  $connection = self::getDb();
					  $request_date = date('Y-m-d H:i:s');
					  $remote_ip = $_SERVER['REMOTE_ADDR'];
					  $server_ip = $_SERVER['SERVER_ADDR'];
					  $request_url = urldecode($_SERVER['REQUEST_URI']);
					  $seller_id = !empty(Helper::getSessionId()) ? Helper::getSessionId() : 0;
					  $browser =  $_SERVER['HTTP_USER_AGENT'];
					  $session_id= session_id();
					  $Query = "INSERT INTO ".VISIT_LOG."(request_date, remote_ip, server_ip, request_url, seller_id, browser, session_id)
									VALUES('".date('Y-m-d H:i:s')."','$remote_ip','$server_ip','$request_url','$seller_id','$browser','$session_id')";
					  $connection->createCommand($Query)->execute();
			   }
			  catch(Exception $e){
				    CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
			  }
		 }
   }
?>