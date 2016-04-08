<?php

namespace common\controllers;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\components\Configuration;
use common\components\Message;
use yii\web\Request;
use common\models\Visitlog;
use common\components\Session as ShopSession;

class AppController extends Controller
{
	public function init(){
		 Visitlog::saveVisitLog(); //Save visit log
	     ShopSession::shopSessionId(); //Set global session
	}
}
?>