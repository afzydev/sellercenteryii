<?php
     //open session
	 function openSession(){
		 $session=Yii::$app->session;
		 $session->open();
		 return $session;
	 }
	 
	  //close session
	 function destroySession(){
		 $session=Yii::$app->session;
		 $session->open();
		 $session->destroy();
	 }
	 
	  //return all the session variable
	 function fbsessionlog(){
		 foreach ($_SESSION as $session_name => $session_value)
			echo $session_name.' - '.$session_value.'<br>';

	 }
	 
?>