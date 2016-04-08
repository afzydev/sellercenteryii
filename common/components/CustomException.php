<?php
 namespace common\components;
 use Yii;
 use yii\base\Component;
 class CustomException extends Component{
	    public static function errorLog($str, $function, $file, $line){
			  $strLog = "[ ".$_SERVER["REMOTE_ADDR"]." ] [ ".date('Y-m-d h:i:s')." ] [ ERROR:'".$str."' ] [ FUNCTION:'".$function."' ] [ FILE:'".$file."' ] [ LINE:'".$line."' ]";
			  error_log("".$strLog."\r\n", 3, dirname(dirname(__DIR__)) ."/console/runtime/logs/".date('Y-m-d')."-access.log");
		}  
 }
?>