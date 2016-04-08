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
                        <h4><b>Manifest Sheet</b></h4>                        
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
                    <td style="width: 20%" align="right" valign="top">
                        <h4><b><?php if(!empty($carrier['name'])) echo $carrier['name'];?></b></h4>
                    </td>
                </tr>
 
            </table>
        <?php //} ?>

        <table style="width: 100%; border-bottom:#c8c8c8 solid 1.5px;margin-top: 35px;"  cellpadding="5">
            <tr>
                <td style="width:10%">S.No</td>
                <td style="width:10%">AWB/ No.</td>
                <td style="width:10%">Sub-order reference number</td>
                <td style="width:10%">Org</td>
                <td style="width:10%">Dest</td>
                <td style="width:10%">No. of Parcel</td>
                <td style="width:10%">Product Amount</td>
                <td style="width:10%">Payment Mode</td>
                <td style="width:10%">Aging</td>

            </tr>
            <?php 
            $i=1;
            foreach($setData as $data){?>
            <tr>           
                <td style="width:10%">
                    <?php echo $i++; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['response_waywill']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['reference']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['origin_city']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['dest_city']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['product_quantity']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['total_paid_tax_incl']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['payment']; ?>
                </td>
                <td style="width:10%">
                    <?php echo $data['aging']; ?>
                </td>

            </tr>
            <?php } ?>
        </table>
        <table style="width: 90%;margin-top: 100px;"  cellpadding="5">
            <tr>
                <td style="width:10%"><h4>I HAVE RECEIVED THE PACKAGES AS DETAILED ABOVE</h4></td>
                <td style="width:30%"><h4>LOADED BY NAME/CODE WITH SIGN</h4></td>
                <td style="width:30%"><h4>DATE AND TIME</h4></td>
            </tr>

            <tr>
                <td style="width: 100%">
                    <h4>SIGNATURE OF DRIVER/VENDOR</h4>  
                </td>
            </tr>
        </table>

        


<?php $this->endBody() ?>
</body>

</html> 
<?php $this->endPage() ?>

