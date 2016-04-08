<?php
namespace common\components;
use Yii;
use yii\base\Component;
use backend\models\Shop;

class Session extends Component { 
	 //Set initial session
	 public static function shopSessionId(){
  	    $session = Yii::$app->session;
		$shopModel = new Shop;
		Yii::$app->params['shopValue']=$shopModel->getShops();

		if($session->has('id_shop') && Yii::$app->request->post('id_shop')==null)
		{
	        return $session->get('id_shop'); // getting session shop id
		}
		else
		{
			 if(Yii::$app->request->post('id_shop')!=null)
			 {
				$shopModel = new Shop;
				if(Yii::$app->request->post('id_shop')!=0)
				{
			     	$shopModel->id_shop=Yii::$app->request->post('id_shop');
			     	return $shopModel->setShopSession(); // setting the shop id to the session
	  		 	}
	  		 	else
	  		 	{
	  		 		 $session->remove('id_shop');
	  		 		 $session->remove('shop_name');
	  		 	}
	  		 }
  		}
	 }

}
?>