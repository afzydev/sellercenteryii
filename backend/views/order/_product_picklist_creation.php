<?php
use yii\helpers\Html;
use common\components\Helpers as Helper;

if (class_exists('backend\assets\AppAsset')) {
    backend\assets\AppAsset::register($this);
} else {
    app\assets\AppAsset::register($this);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">

    </head>
    <body>
        <?php $this->beginBody() ?>
        <?php //if (isset($duplicate) && !empty($duplicate)) { ?>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 100%" align="center">
                        <h4><b>Product Picklist Sheet</b></h4>                        
                    </td>    
                </tr>
            </table>
            <table style="width: 100%;margin-top:25px;" >
                <tr>
                    <td style="width: 20%;text-align: center;" valign="top" >
                        <img src="images/cardekho-manifest.jpg" style="width:180px;"/>
                    </td>
                    <td style="width: 60%" align="left" valign="top">
                        <p style="width: 5%;">
                        <?php
                           if(!empty($sellerInfo['company_name'])) echo $sellerInfo['company_name'].'<br/>';
                           if(!empty($sellerInfo['address'])) echo $sellerInfo['address'].'<br/>';
                        ?> 
                        </p>                     
                    </td>
                    
                </tr>
 
            </table>
        <?php //} ?>

        <table style="width: 100%; border-bottom:#c8c8c8 solid 1.5px;margin-top: 35px;"  cellpadding="5">
            <tr>
                <td style="width:10%">S.No</td>
                <td style="width:10%">Order Id</td>
                <td style="width:10%">Product Id</td>
                <td style="width:10%">Product Name</td>
                <td style="width:10%">Product Quantity</td>
                <td style="width:10%">Product SKU</td>

            </tr>
            <?php 
            $i=1;
            foreach($productData as $data){?>
            <tr>           
                <td style="width:5%">
                    <?php echo $i++; ?>
                </td>
                <td style="width:5%">
                    <?php echo $data['id_order']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['id_product']; ?>
                </td>
                <td style="width:40%">
                    <?php echo $data['product_name']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['product_quantity']; ?>
                </td>
                <td style="width:30%">
                    <?php echo $data['product_reference']; ?>
                </td>

            </tr>
            <?php } ?>
        </table>

<?php $this->endBody() ?>
</body>

</html> 
<?php $this->endPage() ?>

