<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\web\Session;
use common\components\Helpers as Helper;
use common\components\Configuration;
use common\components\Session as ShopSession;

/* @var $this \yii\web\View */
/* @var $content string */
$session = Yii::$app->session;

?>
<div class="d-overly" id="ajxld" style="display:none;"></div>
<header class="main-header">

    <?= Html::a('<span class="logo-mini"><img src='.Yii::$app->params["WEB_URL"].'images/car-dekho-sm.jpg title="CarDekho Seller Dashboard" class="cardekho-logo" style="width: 51px;"  ></span><span class="logo-lg"><img src='.Yii::$app->params["WEB_URL"].'images/seller-dashboard.png title="CarDekho Seller Dashboard" class="cardekho-logo" style="width: 200px;" ></span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    
    <nav class="navbar navbar-static-top" role="navigation">
    <?php
    if(Yii::$app->controller->id!='order'){
    ?>
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
    <?php } ?>
        <div class="navbar-custom-menu" style="float:left">
                

            <ul class="nav navbar-nav">
               <!-- User Account: style can be found in dropdown.less -->
            <?php if(Configuration::get('DISPLAY_SELLER_SHOP')==1 || !Helper::isSeller()){ ?>
            <li class="dropdown messages-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                
                  <?php if($session->has('shop_name')) { echo $session->get('shop_name');} else { echo 'All Shop';} ?>
                  <i class="fa fa-caret-down"></i>

                </a>
                <ul class="dropdown-menu">
                <?php
                $i=0;
                foreach (Yii::$app->params['shopValue'] as $value) { $i++; ?>
                  <li>
                    <ul class="menu">
                    <?php if($session->has('shop_name') && $i==1) { ?>
                      <li>
                        <a href="javascript:void(0)" onclick="changeShopSession('0');" >
                          <h4>
                            All Shops
                          </h4>
                        </a>
                      </li>
                      <?php } ?>
                      <li>
                        <a href="javascript:void(0)" onclick="changeShopSession(<?php echo $value['id_shop']?>);" >
                          <h4>
                          <?php $form = ActiveForm::begin(['method' => 'post','id'     => 'setSessionForm',]); ?>
                          <input type="hidden" name="id_shop" id="id_shop" value="<?php echo $value['id_shop']?>">
                            <?php echo $value['name']?>
                          <?php ActiveForm::end(); ?>
                          </h4>
                        </a>
                      </li>

                    </ul>

                  </li>
                <?php } ?>
                 
                </ul>
              </li>
              <?php } ?>
              

            </ul>
        </div>


        <div class="navbar-custom-menu">
                

            <ul class="nav navbar-nav">
               <!-- User Account: style can be found in dropdown.less -->
               <?php
               if(Helper::isSeller()) {
               ?>
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?php
                $ratingValue='';
                $rating=Helper::getSellerInfo();
                if(!empty($rating['seller_rating']))
                {
                  $ratingValue=$rating['seller_rating'];
                ?>
                  <div id="stars-yellow" style="font-size: 1.2em;" data-rating="<?php echo $ratingValue;?>" ></div>
                <?php
                }
                ?>
                </a>
              </li>
              <?php } ?>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php
                    $fullstoreName='';
                    if(Helper::isSeller()) { 
                        $storeName=Helper::getSellerInfo(); 
                        if(isset($storeName['company_name']) && !empty($storeName['company_name']))
                        {
                          $fullstoreName="(".$storeName['company_name'].")";
                        }
                        $fullName='<span style="font-size: 15px;">'.Helper::getSessionFullName().'</span>&nbsp;&nbsp;'.'<span style="font-size: 11px;">'.$fullstoreName.'</span>';
                      } 
                    else { 
                      $fullName= Helper::getSessionFullName(); 
                    } 
                    ?>
                        <span class="hidden-xs"><?php echo $fullName; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <?= Html::a(
                                    'Sign out',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                
            </ul>
        </div>
    </nav>
</header>
<script type="text/javascript">
function changeShopSession (id_shop) {
    $('#id_shop').val(id_shop);
    $( "#setSessionForm" ).submit();
}

          
</script>

