<?php
namespace common\components;
use Yii;
use yii\base\Component;

class Curl extends Component {

    /**
    * Execute curl
    */
   public static function executeCurl($url, $parameters, $method='POST'){
		    $ch = curl_init($url);
		    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		   // curl_setopt($ch, CURLOPT_HTTPHEADER, false);

		    $result = curl_exec($ch);

		    curl_close($ch);
			return $result;
	 }

}
?>